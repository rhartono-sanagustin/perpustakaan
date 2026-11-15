<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookCopyStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $recentRequests = LoanRequest::query()
            ->with(['user', 'book'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        $overdueLoans = Loan::query()
            ->overdue()
            ->with(['user', 'copy.book'])
            ->orderBy('due_at')
            ->take(5)
            ->get();

        $today = now()->startOfDay();
        $activityStart = $today->copy()->subDays(6);
        $activityEnd = $today->copy()->endOfDay();

        $borrowedPerDay = Loan::query()
            ->whereBetween('borrowed_at', [$activityStart->toDateString(), $activityEnd->toDateString()])
            ->get(['borrowed_at'])
            ->countBy(fn (Loan $loan): string => $loan->borrowed_at->toDateString());

        $loanActivity = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($borrowedPerDay, $today): array {
                $date = $today->copy()->subDays($daysAgo);

                return [
                    'date' => $date,
                    'label' => $date->locale('id')->translatedFormat('D'),
                    'full_label' => $date->locale('id')->translatedFormat('d M Y'),
                    'count' => (int) ($borrowedPerDay[$date->toDateString()] ?? 0),
                ];
            });

        $activityTotal = (int) $loanActivity->sum('count');
        $activityMax = max(1, (int) $loanActivity->max('count'));

        return view('admin.dashboard', [
            'totalBooks' => Book::active()->count(),
            'availableCopies' => BookCopy::available()->count(),
            'activeLoans' => Loan::active()->count(),
            'overdueLoansCount' => Loan::overdue()->count(),
            'pendingRequestsCount' => LoanRequest::pending()->count(),
            'damagedCopiesCount' => BookCopy::whereIn('status', [
                BookCopyStatus::Rusak,
                BookCopyStatus::Hilang,
            ])->count(),
            'recentRequests' => $recentRequests,
            'overdueLoans' => $overdueLoans,
            'loanActivity' => $loanActivity,
            'activityTotal' => $activityTotal,
            'activityMax' => $activityMax,
        ]);
    }
}
