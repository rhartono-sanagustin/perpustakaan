<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\MemberBarcode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use InvalidArgumentException;

class MemberCardController extends Controller
{
    public function __construct(private readonly MemberBarcode $barcode) {}

    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $users = $this->query($filters)
            ->paginate(15)
            ->withQueryString();

        return view('admin.member-cards.index', [
            'users' => $users,
            'search' => $filters['search'],
            'role' => $filters['role'],
            'kelas' => $filters['kelas'],
            'roles' => [UserRole::Guru, UserRole::Siswa],
            'classes' => User::query()
                ->where('role', UserRole::Siswa)
                ->whereNotNull('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas'),
            'units' => User::query()
                ->where('role', UserRole::Guru)
                ->whereNotNull('unit_kerja')
                ->distinct()
                ->orderBy('unit_kerja')
                ->pluck('unit_kerja'),
        ]);
    }

    public function print(Request $request): View|RedirectResponse
    {
        $filters = $this->filters($request);

        $users = $this->query($filters)
            ->limit(200)
            ->get();

        [$cards, $skipped] = $this->buildCards($users);

        if ($cards === []) {
            return redirect()
                ->route('admin.member-cards.index', $request->query())
                ->with('error', 'Tidak ada kartu yang dapat dicetak untuk filter tersebut. Pastikan akun aktif dan nomor induk valid.');
        }

        return view('admin.member-cards.print', [
            'cards' => $cards,
            'skippedCount' => count($skipped),
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        if (! $user->isBorrower() || ! $user->isActive()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Kartu anggota hanya dapat dicetak untuk akun guru atau siswa yang aktif.');
        }

        [$cards, $skipped] = $this->buildCards(collect([$user]));

        if ($cards === []) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Nomor induk '.$user->name.' kosong atau mengandung karakter yang tidak didukung barcode.');
        }

        return view('admin.member-cards.print', [
            'cards' => $cards,
            'skippedCount' => count($skipped),
        ]);
    }

    /**
     * @return array{search: string, role: UserRole|null, kelas: string}
     */
    private function filters(Request $request): array
    {
        $role = UserRole::tryFrom((string) $request->query('role'));

        if ($role !== null && ! $role->isBorrower()) {
            $role = null;
        }

        $search = $request->query('q');
        $kelas = $request->query('kelas');

        return [
            'search' => is_string($search) ? mb_substr(trim($search), 0, 100) : '',
            'role' => $role,
            'kelas' => is_string($kelas) ? mb_substr(trim($kelas), 0, 100) : '',
        ];
    }

    /**
     * @param  array{search: string, role: UserRole|null, kelas: string}  $filters
     */
    private function query(array $filters): Builder
    {
        return User::query()
            ->whereIn('role', [UserRole::Guru, UserRole::Siswa])
            ->where('status', UserStatus::Aktif)
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $like = '%'.$filters['search'].'%';

                $query->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('nomor_induk', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->when($filters['role'] !== null, fn (Builder $query) => $query->where('role', $filters['role']->value))
            ->when($filters['kelas'] !== '', function (Builder $query) use ($filters): void {
                $like = '%'.$filters['kelas'].'%';

                $query->where(function (Builder $query) use ($like): void {
                    $query->where('kelas', 'like', $like)
                        ->orWhere('unit_kerja', 'like', $like);
                });
            })
            ->orderBy('role')
            ->orderBy('name');
    }

    /**
     * @param  Collection<int, User>  $users
     * @return array{0: array<int, array{user: User, barcode: string}>, 1: array<int, User>}
     */
    private function buildCards(Collection $users): array
    {
        $cards = [];
        $skipped = [];

        foreach ($users as $user) {
            try {
                $cards[] = [
                    'user' => $user,
                    'barcode' => $this->barcode->svg((string) $user->nomor_induk),
                ];
            } catch (InvalidArgumentException) {
                $skipped[] = $user;
            }
        }

        return [$cards, $skipped];
    }
}
