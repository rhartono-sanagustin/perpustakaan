<?php

namespace App\Http\Controllers\Borrower;

use App\Enums\LoanRequestStatus;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $activeLoans = Loan::query()
            ->where('user_id', $user->id)
            ->active()
            ->with('copy.book')
            ->orderBy('due_at')
            ->get();

        $pendingRequests = LoanRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [LoanRequestStatus::Menunggu, LoanRequestStatus::Disetujui])
            ->with('book')
            ->latest('requested_at')
            ->take(5)
            ->get();

        $recentHistory = Loan::query()
            ->where('user_id', $user->id)
            ->where('status', LoanStatus::Dikembalikan)
            ->with('copy.book')
            ->latest('returned_at')
            ->take(5)
            ->get();

        $announcements = Announcement::query()
            ->active()
            ->visibleTo($user->role)
            ->with('creator')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        return view('borrower.dashboard', [
            'activeLoans' => $activeLoans,
            'activeLoansCount' => $activeLoans->count(),
            'pendingRequestsCount' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->where('status', LoanRequestStatus::Menunggu)
                ->count(),
            'overdueLoansCount' => Loan::query()
                ->where('user_id', $user->id)
                ->overdue()
                ->count(),
            'pendingRequests' => $pendingRequests,
            'recentHistory' => $recentHistory,
            'announcements' => $announcements,
        ]);
    }
}
