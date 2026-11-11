<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LoanRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CheckoutLoanRequest;
use App\Http\Requests\Admin\RejectLoanRequest;
use App\Models\BookCopy;
use App\Models\LoanRequest;
use App\Services\CirculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LoanRequestController extends Controller
{
    public function __construct(private readonly CirculationService $circulation) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', LoanRequest::class);

        $status = $this->stringQuery($request, 'status', 30);

        if ($status === '') {
            $status = 'menunggu';
        }

        if (! in_array($status, ['menunggu', 'disetujui', 'ditolak', 'dipinjam', 'dikembalikan', 'dibatalkan', 'all'], true)) {
            $status = 'menunggu';
        }

        $search = $this->stringQuery($request, 'q', 100);
        $dateFrom = $this->dateQuery($request, 'date_from');
        $dateTo = $this->dateQuery($request, 'date_to');

        $requests = LoanRequest::query()
            ->with(['user', 'book', 'processor'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';

                $query->where(function ($query) use ($like) {
                    $query->whereHas('user', function ($query) use ($like) {
                        $query->where('name', 'like', $like)
                            ->orWhere('nomor_induk', 'like', $like);
                    })->orWhereHas('book', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('isbn', 'like', $like);
                    });
                });
            })
            ->when($dateFrom, fn ($query) => $query->whereDate('requested_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('requested_at', '<=', $dateTo))
            ->latest('requested_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.circulation.requests.index', [
            'requests' => $requests,
            'status' => $status,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'statuses' => LoanRequestStatus::cases(),
            'counts' => $this->statusCounts(),
        ]);
    }

    public function show(LoanRequest $loanRequest): View
    {
        $this->authorize('view', $loanRequest);

        $loanRequest->load(['user', 'book.authors', 'book.category', 'processor', 'loan.copy']);

        return view('admin.circulation.requests.show', [
            'loanRequest' => $loanRequest,
            'quota' => $this->circulation->quotaSummary($loanRequest->user),
            'availableCopies' => $this->circulation->availableCopies($loanRequest->book),
            'approvedHolds' => $this->circulation->approvedHoldCount($loanRequest->book, $loanRequest->id),
        ]);
    }

    public function approve(Request $request, LoanRequest $loanRequest): RedirectResponse
    {
        $this->authorize('view', $loanRequest);

        if (! $request->user()->can('approve', $loanRequest)) {
            return redirect()
                ->route('admin.requests.show', $loanRequest)
                ->with('error', 'Pengajuan ini sudah diproses dan tidak dapat disetujui lagi.');
        }

        $this->circulation->approve($loanRequest, $request->user());

        return redirect()
            ->route('admin.requests.show', $loanRequest)
            ->with('success', 'Pengajuan berhasil disetujui. Buku belum dipinjam sampai checkout dicatat.');
    }

    public function reject(RejectLoanRequest $request, LoanRequest $loanRequest): RedirectResponse
    {
        $this->authorize('view', $loanRequest);

        if (! $request->user()->can('reject', $loanRequest)) {
            return redirect()
                ->route('admin.requests.show', $loanRequest)
                ->with('error', 'Pengajuan ini sudah diproses dan tidak dapat ditolak lagi.');
        }

        $this->circulation->reject($loanRequest, $request->user(), $request->validated('rejection_reason'));

        return redirect()
            ->route('admin.requests.show', $loanRequest)
            ->with('success', 'Pengajuan ditolak. Alasan penolakan sudah disimpan.');
    }

    public function cancel(Request $request, LoanRequest $loanRequest): RedirectResponse
    {
        $this->authorize('view', $loanRequest);

        if (! $request->user()->can('cancel', $loanRequest)) {
            return redirect()
                ->route('admin.requests.show', $loanRequest)
                ->with('error', 'Pengajuan ini sudah tidak dapat dibatalkan.');
        }

        $this->circulation->cancel($loanRequest, $request->user());

        return redirect()
            ->route('admin.requests.show', $loanRequest)
            ->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
    }

    public function checkoutForm(Request $request, LoanRequest $loanRequest): View|RedirectResponse
    {
        $this->authorize('view', $loanRequest);

        if (! $request->user()->can('checkout', $loanRequest)) {
            return redirect()
                ->route('admin.requests.show', $loanRequest)
                ->with('error', $this->checkoutBlockedMessage($loanRequest));
        }

        $loanRequest->load(['user', 'book.authors', 'book.category']);

        $quota = $this->circulation->quotaSummary($loanRequest->user);

        return view('admin.circulation.requests.checkout', [
            'loanRequest' => $loanRequest,
            'copies' => $this->circulation->availableCopies($loanRequest->book),
            'quota' => $quota,
            'dueDate' => today()->addDays($quota['duration_days']),
        ]);
    }

    public function checkout(CheckoutLoanRequest $request, LoanRequest $loanRequest): RedirectResponse
    {
        $this->authorize('view', $loanRequest);

        if (! $request->user()->can('checkout', $loanRequest)) {
            return redirect()
                ->route('admin.requests.show', $loanRequest)
                ->with('error', $this->checkoutBlockedMessage($loanRequest));
        }

        $copy = BookCopy::query()->findOrFail($request->validated('book_copy_id'));
        $loan = $this->circulation->checkout($loanRequest, $copy, $request->user());

        return redirect()
            ->route('admin.requests.show', $loanRequest)
            ->with('success', 'Peminjaman berhasil dicatat. Jatuh tempo: '.$loan->due_at->locale('id')->translatedFormat('d F Y').'.');
    }

    /**
     * @return array<string, int>
     */
    private function statusCounts(): array
    {
        $counts = LoanRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(LoanRequestStatus::cases())
            ->mapWithKeys(fn (LoanRequestStatus $status) => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }

    private function checkoutBlockedMessage(LoanRequest $loanRequest): string
    {
        if ($loanRequest->status === LoanRequestStatus::Disetujui && $loanRequest->isExpired()) {
            return 'Batas pengambilan pengajuan ini sudah kedaluwarsa. Batalkan pengajuan atau minta peminjam mengajukan ulang.';
        }

        if ($loanRequest->status !== LoanRequestStatus::Disetujui) {
            return 'Pengajuan belum disetujui atau sudah diproses.';
        }

        return 'Pengajuan ini tidak dapat di-checkout saat ini.';
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
