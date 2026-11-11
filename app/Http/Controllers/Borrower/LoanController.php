<?php

namespace App\Http\Controllers\Borrower;

use App\Enums\LoanRequestStatus;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $tab = $this->tab($request, ['menunggu', 'disetujui', 'dipinjam', 'terlambat'], 'dipinjam');

        $counts = [
            'menunggu' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->where('status', LoanRequestStatus::Menunggu)
                ->count(),
            'disetujui' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->where('status', LoanRequestStatus::Disetujui)
                ->count(),
            'dipinjam' => Loan::query()
                ->where('user_id', $user->id)
                ->active()
                ->count(),
            'terlambat' => Loan::query()
                ->where('user_id', $user->id)
                ->overdue()
                ->count(),
        ];

        $items = match ($tab) {
            'menunggu' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->where('status', LoanRequestStatus::Menunggu)
                ->with(['book', 'processor'])
                ->latest('requested_at')
                ->paginate(10)
                ->withQueryString(),
            'disetujui' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->where('status', LoanRequestStatus::Disetujui)
                ->with(['book', 'processor'])
                ->latest('approved_at')
                ->paginate(10)
                ->withQueryString(),
            'terlambat' => Loan::query()
                ->where('user_id', $user->id)
                ->overdue()
                ->with(['copy.book', 'processor'])
                ->orderBy('due_at')
                ->paginate(10)
                ->withQueryString(),
            default => Loan::query()
                ->where('user_id', $user->id)
                ->active()
                ->with(['copy.book', 'processor'])
                ->orderBy('due_at')
                ->paginate(10)
                ->withQueryString(),
        };

        return view('borrower.loans.index', [
            'tab' => $tab,
            'counts' => $counts,
            'items' => $items,
        ]);
    }

    public function history(Request $request): View
    {
        $user = $request->user();
        $tab = $this->tab($request, ['pinjaman', 'pengajuan'], 'pinjaman');

        $loanHistory = Loan::query()
            ->where('user_id', $user->id)
            ->where('status', LoanStatus::Dikembalikan)
            ->with(['copy.book', 'processor'])
            ->latest('returned_at')
            ->paginate(10, ['*'], 'loan_page')
            ->withQueryString();

        $requestHistory = LoanRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [LoanRequestStatus::Ditolak, LoanRequestStatus::Dibatalkan])
            ->with(['book', 'processor'])
            ->latest('updated_at')
            ->paginate(10, ['*'], 'request_page')
            ->withQueryString();

        return view('borrower.loans.history', [
            'tab' => $tab,
            'loanHistory' => $loanHistory,
            'requestHistory' => $requestHistory,
            'returnedCount' => Loan::query()
                ->where('user_id', $user->id)
                ->where('status', LoanStatus::Dikembalikan)
                ->count(),
            'finishedRequestCount' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->whereIn('status', [LoanRequestStatus::Ditolak, LoanRequestStatus::Dibatalkan])
                ->count(),
        ]);
    }

    /**
     * @param  list<string>  $allowed
     */
    private function tab(Request $request, array $allowed, string $default): string
    {
        $tab = $request->query('tab');

        return is_string($tab) && in_array($tab, $allowed, true) ? $tab : $default;
    }
}
