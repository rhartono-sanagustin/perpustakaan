<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LoanStatus;
use App\Enums\ReturnCondition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReturnLoanRequest;
use App\Models\Loan;
use App\Services\CirculationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private readonly CirculationService $circulation) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Loan::class);

        $loans = $this->filteredActiveLoans($request)
            ->orderBy('due_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.circulation.loans.index', [
            'loans' => $loans,
            'search' => $this->stringQuery($request, 'q', 100),
            'overdueOnly' => $request->boolean('overdue'),
            'mode' => 'active',
            'pageTitle' => 'Peminjaman Aktif',
            'pageSubtitle' => 'Pantau pinjaman yang sedang berjalan dan catat pengembalian.',
            'summary' => $this->activeSummary(),
        ]);
    }

    public function returns(Request $request): View
    {
        $this->authorize('viewAny', Loan::class);

        $loans = $this->filteredActiveLoans($request)
            ->orderBy('due_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.circulation.loans.index', [
            'loans' => $loans,
            'search' => $this->stringQuery($request, 'q', 100),
            'overdueOnly' => $request->boolean('overdue'),
            'mode' => 'returns',
            'pageTitle' => 'Pengembalian Buku',
            'pageSubtitle' => 'Temukan pinjaman aktif, periksa kondisi buku, lalu catat pengembalian.',
            'summary' => $this->activeSummary(),
        ]);
    }

    public function history(Request $request): View
    {
        $this->authorize('viewAny', Loan::class);

        $search = $this->stringQuery($request, 'q', 100);
        $dateFrom = $this->dateQuery($request, 'date_from');
        $dateTo = $this->dateQuery($request, 'date_to');

        $loans = Loan::query()
            ->where('status', LoanStatus::Dikembalikan)
            ->with(['user', 'copy.book', 'processor'])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';

                $query->where(function ($query) use ($like) {
                    $query->whereHas('user', function ($query) use ($like) {
                        $query->where('name', 'like', $like)
                            ->orWhere('nomor_induk', 'like', $like);
                    })->orWhereHas('copy', function ($query) use ($like) {
                        $query->where('inventory_code', 'like', $like);
                    })->orWhereHas('copy.book', function ($query) use ($like) {
                        $query->where('title', 'like', $like);
                    });
                });
            })
            ->when($dateFrom, fn ($query) => $query->whereDate('returned_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('returned_at', '<=', $dateTo))
            ->latest('returned_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.circulation.loans.history', [
            'loans' => $loans,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'returnedCount' => Loan::query()->where('status', LoanStatus::Dikembalikan)->count(),
        ]);
    }

    public function returnForm(Request $request, Loan $loan): View|RedirectResponse
    {
        $this->authorize('view', $loan);

        if (! $request->user()->can('markReturned', $loan)) {
            return redirect()
                ->route('admin.loans.index')
                ->with('error', 'Transaksi ini sudah dikembalikan atau tidak aktif.');
        }

        $loan->load(['user', 'copy.book', 'request', 'processor']);

        return view('admin.circulation.loans.return', [
            'loan' => $loan,
            'conditions' => ReturnCondition::cases(),
        ]);
    }

    public function returnLoan(ReturnLoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->authorize('view', $loan);

        if (! $request->user()->can('markReturned', $loan)) {
            return redirect()
                ->route('admin.loans.index')
                ->with('error', 'Transaksi ini sudah dikembalikan atau tidak aktif.');
        }

        $returnedLoan = $this->circulation->returnLoan(
            $loan,
            $request->user(),
            ReturnCondition::from($request->validated('return_condition')),
            $request->validated('return_notes')
        );

        return redirect()
            ->route('admin.loans.history')
            ->with('success', 'Pengembalian "'.$returnedLoan->copy->book->title.'" berhasil dicatat.');
    }

    private function filteredActiveLoans(Request $request): Builder
    {
        $search = $this->stringQuery($request, 'q', 100);

        return Loan::query()
            ->active()
            ->with(['user', 'copy.book', 'processor'])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';

                $query->where(function ($query) use ($like) {
                    $query->whereHas('user', function ($query) use ($like) {
                        $query->where('name', 'like', $like)
                            ->orWhere('nomor_induk', 'like', $like);
                    })->orWhereHas('copy', function ($query) use ($like) {
                        $query->where('inventory_code', 'like', $like);
                    })->orWhereHas('copy.book', function ($query) use ($like) {
                        $query->where('title', 'like', $like);
                    });
                });
            })
            ->when($request->boolean('overdue'), fn ($query) => $query->overdue());
    }

    /**
     * @return array{active: int, overdue: int}
     */
    private function activeSummary(): array
    {
        return [
            'active' => Loan::query()->active()->count(),
            'overdue' => Loan::query()->overdue()->count(),
        ];
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }

    private function dateQuery(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }

        return $value;
    }
}
