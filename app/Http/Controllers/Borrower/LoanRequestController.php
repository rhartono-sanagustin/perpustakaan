<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Http\Requests\Borrower\StoreLoanRequest;
use App\Models\Book;
use App\Models\LoanRequest;
use App\Services\CirculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanRequestController extends Controller
{
    public function __construct(private readonly CirculationService $circulation) {}

    public function create(Request $request, Book $book): View
    {
        $this->authorize('create', LoanRequest::class);
        abort_unless($book->is_active, 404);

        $book->load(['category', 'authors', 'publisher']);

        return view('borrower.requests.create', [
            'book' => $book,
            'eligibility' => $this->circulation->requestEligibility($request->user(), $book),
        ]);
    }

    public function store(StoreLoanRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('create', LoanRequest::class);
        abort_unless($book->is_active, 404);

        $loanRequest = $this->circulation->submitRequest(
            $request->user(),
            $book,
            $request->validated('notes')
        );

        return redirect()
            ->route('borrower.loans.index', ['tab' => 'menunggu'])
            ->with('success', 'Pengajuan peminjaman "'.$loanRequest->book->title.'" berhasil dikirim. Buku belum dipinjam sampai diserahkan oleh admin.');
    }

    public function cancel(Request $request, LoanRequest $loanRequest): RedirectResponse
    {
        $this->authorize('cancel', $loanRequest);

        $this->circulation->cancel($loanRequest, $request->user());

        return back()->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
    }
}
