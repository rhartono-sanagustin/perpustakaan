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
        ]);
    }
}
