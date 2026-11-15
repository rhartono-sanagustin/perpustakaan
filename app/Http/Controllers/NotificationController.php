<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Ringkasan notifikasi operasional untuk admin.
     */
    public function admin(): JsonResponse
    {
        $pendingRequests = LoanRequest::query()->pending()->count();
        $overdueLoans = Loan::query()->overdue()->count();

        $latestRequest = LoanRequest::query()
            ->pending()
            ->with(['user', 'book'])
            ->latest('requested_at')
            ->latest('id')
            ->first();

        return response()->json([
            'pending_requests' => $pendingRequests,
            'overdue_loans' => $overdueLoans,
            'latest_request_id' => $latestRequest?->getKey(),
            'latest_request' => $latestRequest === null ? null : [
                'borrower' => $latestRequest->user->name,
                'book' => $latestRequest->book->title,
                'requested_at' => $latestRequest->requested_at?->toIso8601String(),
                'url' => route('admin.requests.show', $latestRequest),
            ],
            'requests_url' => route('admin.requests.index'),
            'overdue_url' => route('admin.loans.index', ['overdue' => 1]),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Ringkasan pengajuan dan pinjaman milik peminjam yang sedang login.
     */
    public function borrower(Request $request): JsonResponse
    {
        $user = $request->user();

        $requests = LoanRequest::query()
            ->where('user_id', $user->id)
            ->with('book')
            ->latest('requested_at')
            ->latest('id')
            ->take(10)
            ->get()
            ->map(fn (LoanRequest $loanRequest): array => [
                'id' => $loanRequest->getKey(),
                'book' => $loanRequest->book->title,
                'status' => $loanRequest->status->value,
                'status_label' => $loanRequest->displayStatusLabel(),
                'updated_at' => $loanRequest->updated_at?->toIso8601String(),
                'url' => route('borrower.loans.index'),
            ])
            ->values();

        $loans = Loan::query()
            ->where('user_id', $user->id)
            ->active()
            ->with('copy.book')
            ->orderBy('due_at')
            ->get()
            ->map(fn (Loan $loan): array => [
                'id' => $loan->getKey(),
                'book' => $loan->copy->book->title,
                'status_label' => $loan->statusLabel(),
                'due_at' => $loan->due_at?->toDateString(),
                'due_at_label' => $loan->due_at?->locale('id')->translatedFormat('d M Y'),
                'days_until_due' => $loan->daysUntilDue(),
                'overdue' => $loan->isOverdue(),
                'url' => route('borrower.loans.index'),
            ])
            ->values();

        return response()->json([
            'pending_requests' => LoanRequest::query()
                ->where('user_id', $user->id)
                ->pending()
                ->count(),
            'active_loans' => $loans->count(),
            'overdue_loans' => $loans->where('overdue', true)->count(),
            'requests' => $requests,
            'loans' => $loans,
            'requests_url' => route('borrower.loans.index'),
            'loans_url' => route('borrower.loans.index'),
            'generated_at' => now()->toIso8601String(),
        ]);
    }
}
