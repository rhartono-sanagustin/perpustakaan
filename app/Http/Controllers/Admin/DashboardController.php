<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookCopyStatus;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibraryVisit;
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

        $visitsPerDay = LibraryVisit::query()
            ->whereBetween('visit_date', [$activityStart->toDateString(), $activityEnd->toDateString()])
            ->get(['visit_date'])
            ->countBy(fn (LibraryVisit $visit): string => $visit->visit_date->toDateString());

        $visitActivity = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($visitsPerDay, $today): array {
                $date = $today->copy()->subDays($daysAgo);

                return [
                    'date' => $date,
                    'label' => $date->locale('id')->translatedFormat('D'),
                    'full_label' => $date->locale('id')->translatedFormat('d M Y'),
                    'count' => (int) ($visitsPerDay[$date->toDateString()] ?? 0),
                ];
            });

        $visitTotal = (int) $visitActivity->sum('count');
        $visitMax = max(1, (int) $visitActivity->max('count'));
        $todayVisits = LibraryVisit::query()->whereDate('visit_date', $today->toDateString())->count();

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
            'visitActivity' => $visitActivity,
            'visitTotal' => $visitTotal,
            'visitMax' => $visitMax,
            'todayVisits' => $todayVisits,
        ]);
    }
}
