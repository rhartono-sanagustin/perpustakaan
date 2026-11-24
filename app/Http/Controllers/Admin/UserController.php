<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\LibrarySetting;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\UserFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filters = UserFilters::fromRequest($request);

        $users = $filters->apply(User::query())
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $filters->search,
            'role' => $filters->role,
            'status' => $filters->status,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
            'counts' => [
                'total' => User::query()->count(),
                'aktif' => User::query()->where('status', UserStatus::Aktif)->count(),
                'admin' => User::query()->where('role', UserRole::Admin)->count(),
                'guru' => User::query()->where('role', UserRole::Guru)->count(),
                'siswa' => User::query()->where('role', UserRole::Siswa)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.form', [
            'user' => new User([
                'role' => UserRole::Siswa,
                'status' => UserStatus::Aktif,
            ]),
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = User::query()->create($request->validated());
        $this->auditLogger->record('user.created', $user, null, $this->snapshot($user));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna '.$user->name.' berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.form', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $before = $this->snapshot($user);
        $user->update($request->validated());
        $this->auditLogger->recordChanges('user.updated', $user, $before, $this->snapshot($user->refresh()));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna '.$user->name.' berhasil diperbarui.');
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $this->authorize('resetPassword', $user);

        $user->update(['password' => $request->validated('password')]);

        $this->auditLogger->record('user.password_reset', $user, null, [
            'reset_by' => $request->user()?->id,
            'reset_at' => now()->toDateTimeString(),
        ]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'Kata sandi '.$user->name.' berhasil direset. Sandi baru tidak ditampilkan di antarmuka.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorize('toggleStatus', $user);

        $before = $this->snapshot($user);
        $newStatus = $user->isActive() ? UserStatus::Nonaktif : UserStatus::Aktif;

        $user->update(['status' => $newStatus]);
        $this->auditLogger->recordChanges('user.status_toggled', $user, $before, $this->snapshot($user->refresh()));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Status akun '.$user->name.' diubah menjadi '.$newStatus->label().'.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($this->hasHistory($user)) {
            if ($user->isActive()) {
                $before = $this->snapshot($user);
                $user->update(['status' => UserStatus::Nonaktif]);
                $this->auditLogger->recordChanges(
                    'user.deactivated',
                    $user,
                    $before,
                    $this->snapshot($user->refresh())
                );
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Pengguna '.$user->name.' dinonaktifkan karena memiliki riwayat transaksi atau jejak audit.');
        }

        $before = $this->snapshot($user);
        $name = $user->name;
        $user->delete();
        $this->auditLogger->record('user.deleted', $user, $before, null);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna '.$name.' berhasil dihapus permanen.');
    }

    /**
     * Riwayat transaksi dan jejak kepengarangan dipertahankan agar audit tidak hilang.
     */
    private function hasHistory(User $user): bool
    {
        return $user->loans()->exists()
            || $user->loanRequests()->exists()
            || $user->visits()->exists()
            || Loan::query()->where('processed_by', $user->id)->exists()
            || LoanRequest::query()->where('processed_by', $user->id)->exists()
            || AuditLog::query()->where('actor_id', $user->id)->exists()
            || Announcement::query()->where('created_by', $user->id)->exists()
            || LibrarySetting::query()->where('updated_by', $user->id)->exists();
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'nomor_induk' => $user->nomor_induk,
            'role' => $user->role->value,
            'status' => $user->status->value,
            'kelas' => $user->kelas,
            'unit_kerja' => $user->unit_kerja,
            'no_telepon' => $user->no_telepon,
        ];
    }
}
