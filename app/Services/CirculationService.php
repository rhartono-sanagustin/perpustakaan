<?php

namespace App\Services;

use App\Enums\BookCopyStatus;
use App\Enums\LoanRequestStatus;
use App\Enums\LoanStatus;
use App\Enums\ReturnCondition;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibrarySetting;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirculationService
{
    /** @var array<string, mixed> */
    private array $settingCache = [];

    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function maxLoans(User $user): int
    {
        $key = $user->isGuru() ? 'max_loans_guru' : 'max_loans_siswa';
        $default = $user->isGuru()
            ? (int) config('library.loan_defaults.max_loans_guru', 5)
            : (int) config('library.loan_defaults.max_loans_siswa', 2);

        return max(0, (int) $this->setting($key, $default));
    }

    public function loanDurationDays(User $user): int
    {
        $key = $user->isGuru() ? 'loan_duration_guru' : 'loan_duration_siswa';
        $default = $user->isGuru()
            ? (int) config('library.loan_defaults.loan_duration_guru', 14)
            : (int) config('library.loan_defaults.loan_duration_siswa', 7);

        return max(1, (int) $this->setting($key, $default));
    }

    public function pickupDeadlineDays(): int
    {
        return max(1, (int) $this->setting(
            'pickup_deadline_days',
            (int) config('library.loan_defaults.pickup_deadline_days', 1)
        ));
    }

    public function activeLoanCount(User $user): int
    {
        return Loan::query()
            ->where('user_id', $user->id)
            ->active()
            ->count();
    }

    public function openRequestCount(User $user, ?int $exceptRequestId = null): int
    {
        return LoanRequest::query()
            ->where('user_id', $user->id)
            ->open()
            ->when($exceptRequestId, fn ($query) => $query->whereKeyNot($exceptRequestId))
            ->count();
    }

    /**
     * @return array{used: int, max: int, remaining: int, duration_days: int, pickup_deadline_days: int}
     */
    public function quotaSummary(User $user): array
    {
        $max = $this->maxLoans($user);
        $used = $this->activeLoanCount($user) + $this->openRequestCount($user);

        return [
            'used' => $used,
            'max' => $max,
            'remaining' => max(0, $max - $used),
            'duration_days' => $this->loanDurationDays($user),
            'pickup_deadline_days' => $this->pickupDeadlineDays(),
        ];
    }

    public function availableCopyCount(Book $book): int
    {
        return BookCopy::query()
            ->where('book_id', $book->id)
            ->available()
            ->count();
    }

    public function approvedHoldCount(Book $book, ?int $exceptRequestId = null): int
    {
        return LoanRequest::query()
            ->where('book_id', $book->id)
            ->approvedActive()
            ->when($exceptRequestId, fn ($query) => $query->whereKeyNot($exceptRequestId))
            ->count();
    }

    public function netAvailableCopyCount(Book $book): int
    {
        return max(0, $this->availableCopyCount($book) - $this->approvedHoldCount($book));
    }

    /**
     * @return Collection<int, BookCopy>
     */
    public function availableCopies(Book $book): Collection
    {
        return BookCopy::query()
            ->where('book_id', $book->id)
            ->available()
            ->orderBy('inventory_code')
            ->get();
    }

    public function openRequestFor(User $user, Book $book): ?LoanRequest
    {
        return LoanRequest::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->open()
            ->latest('requested_at')
            ->first();
    }

    /**
     * @return array{
     *     allowed: bool,
     *     reason: ?string,
     *     open_request: ?LoanRequest,
     *     quota_used: int,
     *     quota_max: int,
     *     remaining_quota: int,
     *     duration_days: int,
     *     pickup_deadline_days: int,
     *     available_copies: int,
     *     net_available_copies: int
     * }
     */
    public function requestEligibility(User $user, Book $book): array
    {
        $quotaMax = $this->maxLoans($user);
        $quotaUsed = $this->activeLoanCount($user) + $this->openRequestCount($user);
        $openRequest = $this->openRequestFor($user, $book);
        $availableCopies = $this->availableCopyCount($book);
        $netAvailableCopies = max(0, $availableCopies - $this->approvedHoldCount($book));

        $reason = match (true) {
            ! $user->isActive() => 'Akun Anda tidak aktif. Hubungi admin perpustakaan untuk mengaktifkan akun.',
            ! $user->isBorrower() => 'Hanya guru dan siswa yang dapat mengajukan peminjaman.',
            ! $book->is_active => 'Buku ini sudah diarsipkan dan tidak dapat diajukan.',
            $openRequest !== null => 'Anda masih memiliki pengajuan aktif untuk buku ini.',
            $quotaUsed >= $quotaMax => 'Kuota peminjaman Anda sudah penuh. Kembalikan buku terlebih dahulu atau batalkan pengajuan yang masih aktif.',
            $netAvailableCopies < 1 => 'Belum ada eksemplar yang dapat diajukan saat ini. Semua eksemplar sedang dipinjam, ditahan, atau tidak tersedia.',
            default => null,
        };

        return [
            'allowed' => $reason === null,
            'reason' => $reason,
            'open_request' => $openRequest,
            'quota_used' => $quotaUsed,
            'quota_max' => $quotaMax,
            'remaining_quota' => max(0, $quotaMax - $quotaUsed),
            'duration_days' => $this->loanDurationDays($user),
            'pickup_deadline_days' => $this->pickupDeadlineDays(),
            'available_copies' => $availableCopies,
            'net_available_copies' => $netAvailableCopies,
        ];
    }

    public function submitRequest(User $user, Book $book, ?string $notes = null): LoanRequest
    {
        return DB::transaction(function () use ($user, $book, $notes) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $lockedBook = Book::query()->whereKey($book->id)->firstOrFail();
            $eligibility = $this->requestEligibility($lockedUser, $lockedBook);

            if (! $eligibility['allowed']) {
                $this->fail('book', (string) $eligibility['reason']);
            }

            $loanRequest = LoanRequest::query()->create([
                'user_id' => $lockedUser->id,
                'book_id' => $lockedBook->id,
                'status' => LoanRequestStatus::Menunggu,
                'requested_at' => now(),
                'notes' => $notes,
            ]);

            $this->auditLogger->record(
                'loan_request.created',
                $loanRequest,
                null,
                $this->requestSnapshot($loanRequest)
            );

            return $loanRequest->load(['book', 'user']);
        });
    }

    public function approve(LoanRequest $loanRequest, User $admin): LoanRequest
    {
        return DB::transaction(function () use ($loanRequest, $admin) {
            $request = LoanRequest::query()->whereKey($loanRequest->id)->lockForUpdate()->firstOrFail();

            if ($request->status !== LoanRequestStatus::Menunggu) {
                $this->fail('status', 'Hanya pengajuan yang menunggu diproses yang dapat disetujui.');
            }

            $borrower = User::query()->whereKey($request->user_id)->lockForUpdate()->firstOrFail();

            if (! $borrower->isActive()) {
                $this->fail('status', 'Akun peminjam tidak aktif. Pengajuan tidak dapat disetujui.');
            }

            if ($this->activeLoanCount($borrower) + $this->openRequestCount($borrower, $request->id) >= $this->maxLoans($borrower)) {
                $this->fail('status', 'Kuota peminjam sudah penuh. Pengajuan tidak dapat disetujui.');
            }

            $book = Book::query()->whereKey($request->book_id)->lockForUpdate()->firstOrFail();

            if (! $book->is_active) {
                $this->fail('status', 'Buku sudah diarsipkan dan tidak dapat disetujui.');
            }

            if ($this->availableCopyCount($book) - $this->approvedHoldCount($book, $request->id) < 1) {
                $this->fail('status', 'Tidak ada eksemplar tersedia yang belum ditahan pengajuan lain.');
            }

            $before = $this->requestSnapshot($request);

            $request->update([
                'status' => LoanRequestStatus::Disetujui,
                'processed_by' => $admin->id,
                'approved_at' => now(),
                'expires_at' => now()->addDays($this->pickupDeadlineDays()),
            ]);

            $this->auditLogger->recordChanges(
                'loan_request.approved',
                $request,
                $before,
                $this->requestSnapshot($request->refresh())
            );

            return $request->load(['book', 'user', 'processor']);
        });
    }

    public function reject(LoanRequest $loanRequest, User $admin, string $reason): LoanRequest
    {
        return DB::transaction(function () use ($loanRequest, $admin, $reason) {
            $request = LoanRequest::query()->whereKey($loanRequest->id)->lockForUpdate()->firstOrFail();

            if ($request->status !== LoanRequestStatus::Menunggu) {
                $this->fail('status', 'Hanya pengajuan yang menunggu diproses yang dapat ditolak.');
            }

            $reason = trim($reason);

            if ($reason === '') {
                $this->fail('rejection_reason', 'Alasan penolakan wajib diisi.');
            }

            $before = $this->requestSnapshot($request);

            $request->update([
                'status' => LoanRequestStatus::Ditolak,
                'processed_by' => $admin->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->auditLogger->recordChanges(
                'loan_request.rejected',
                $request,
                $before,
                $this->requestSnapshot($request->refresh())
            );

            return $request->load(['book', 'user', 'processor']);
        });
    }

    public function cancel(LoanRequest $loanRequest, User $actor): LoanRequest
    {
        return DB::transaction(function () use ($loanRequest, $actor) {
            $request = LoanRequest::query()->whereKey($loanRequest->id)->lockForUpdate()->firstOrFail();

            if (! $request->canBeCancelled()) {
                $this->fail('status', 'Pengajuan ini sudah tidak dapat dibatalkan.');
            }

            $before = $this->requestSnapshot($request);

            $request->update([
                'status' => LoanRequestStatus::Dibatalkan,
                'cancelled_at' => now(),
                'processed_by' => $request->processed_by ?? ($actor->isAdmin() ? $actor->id : null),
            ]);

            $this->auditLogger->recordChanges(
                'loan_request.cancelled',
                $request,
                $before,
                $this->requestSnapshot($request->refresh())
            );

            return $request->load(['book', 'user', 'processor']);
        });
    }

    public function checkout(LoanRequest $loanRequest, BookCopy $bookCopy, User $admin): Loan
    {
        return DB::transaction(function () use ($loanRequest, $bookCopy, $admin) {
            $request = LoanRequest::query()->whereKey($loanRequest->id)->lockForUpdate()->firstOrFail();

            if ($request->status !== LoanRequestStatus::Disetujui) {
                $this->fail('status', 'Pengajuan belum disetujui atau sudah diproses.');
            }

            if ($request->isExpired()) {
                $this->fail('status', 'Batas pengambilan pengajuan ini sudah kedaluwarsa.');
            }

            if ($request->loan()->exists()) {
                $this->fail('status', 'Pengajuan ini sudah memiliki transaksi peminjaman.');
            }

            $borrower = User::query()->whereKey($request->user_id)->lockForUpdate()->firstOrFail();

            if (! $borrower->isActive()) {
                $this->fail('status', 'Akun peminjam tidak aktif. Checkout tidak dapat dilakukan.');
            }

            if ($this->activeLoanCount($borrower) >= $this->maxLoans($borrower)) {
                $this->fail('status', 'Kuota peminjam sudah penuh. Checkout tidak dapat dilakukan.');
            }

            $copy = BookCopy::query()->whereKey($bookCopy->id)->lockForUpdate()->firstOrFail();

            if ($copy->book_id !== $request->book_id) {
                $this->fail('book_copy_id', 'Eksemplar yang dipilih bukan milik buku pada pengajuan ini.');
            }

            if ($copy->status !== BookCopyStatus::Tersedia) {
                $this->fail('book_copy_id', 'Eksemplar yang dipilih sudah tidak tersedia.');
            }

            if ($copy->activeLoan()->exists()) {
                $this->fail('book_copy_id', 'Eksemplar masih memiliki pinjaman aktif.');
            }

            $durationDays = $this->loanDurationDays($borrower);
            $quotaSnapshot = $this->maxLoans($borrower);
            $borrowedAt = today();

            $loan = Loan::query()->create([
                'loan_request_id' => $request->id,
                'user_id' => $borrower->id,
                'book_copy_id' => $copy->id,
                'processed_by' => $admin->id,
                'borrowed_at' => $borrowedAt,
                'due_at' => $borrowedAt->copy()->addDays($durationDays),
                'status' => LoanStatus::Dipinjam,
                'duration_days' => $durationDays,
                'quota_snapshot' => $quotaSnapshot,
            ]);

            $copy->update(['status' => BookCopyStatus::Dipinjam]);
            $request->update([
                'status' => LoanRequestStatus::Dipinjam,
                'processed_by' => $admin->id,
            ]);

            $this->auditLogger->record(
                'loan.checked_out',
                $loan,
                null,
                $this->loanSnapshot($loan)
            );

            return $loan->load(['user', 'copy.book', 'request']);
        });
    }

    public function returnLoan(
        Loan $loan,
        User $admin,
        ReturnCondition $condition,
        ?string $notes = null
    ): Loan {
        return DB::transaction(function () use ($loan, $condition, $notes) {
            $lockedLoan = Loan::query()->whereKey($loan->id)->lockForUpdate()->firstOrFail();

            if (! $lockedLoan->isActive()) {
                $this->fail('status', 'Transaksi ini sudah dikembalikan atau tidak aktif.');
            }

            $copy = BookCopy::query()->whereKey($lockedLoan->book_copy_id)->lockForUpdate()->firstOrFail();
            $before = $this->loanSnapshot($lockedLoan);

            $lockedLoan->update([
                'status' => LoanStatus::Dikembalikan,
                'returned_at' => today(),
                'return_condition' => $condition,
                'return_notes' => $notes,
            ]);

            $copy->update(['status' => $condition->copyStatus()]);

            if ($condition->copyCondition() !== null) {
                $copy->update(['condition' => $condition->copyCondition()]);
            }

            if ($lockedLoan->loan_request_id !== null) {
                LoanRequest::query()
                    ->whereKey($lockedLoan->loan_request_id)
                    ->update(['status' => LoanRequestStatus::Dikembalikan]);
            }

            $this->auditLogger->recordChanges(
                'loan.returned',
                $lockedLoan,
                $before,
                $this->loanSnapshot($lockedLoan->refresh())
            );

            return $lockedLoan->load(['user', 'copy.book', 'request', 'processor']);
        });
    }

    /**
     * Batalkan persetujuan yang melewati batas pengambilan.
     */
    public function expireStaleApprovedRequests(bool $dryRun = false): int
    {
        $query = LoanRequest::query()
            ->where('status', LoanRequestStatus::Disetujui)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());

        if ($dryRun) {
            return $query->count();
        }

        return DB::transaction(function (): int {
            $requests = LoanRequest::query()
                ->where('status', LoanRequestStatus::Disetujui)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $count = 0;

            foreach ($requests as $request) {
                $before = $this->requestSnapshot($request);

                $request->update([
                    'status' => LoanRequestStatus::Dibatalkan,
                    'cancelled_at' => now(),
                ]);

                $this->auditLogger->recordChanges(
                    'loan_request.expired',
                    $request,
                    $before,
                    $this->requestSnapshot($request->refresh())
                );

                $count++;
            }

            return $count;
        });
    }

    private function setting(string $key, mixed $default): mixed
    {
        if (! array_key_exists($key, $this->settingCache)) {
            $this->settingCache[$key] = LibrarySetting::getValue($key, $default);
        }

        return $this->settingCache[$key];
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    /**
     * @return array<string, mixed>
     */
    private function requestSnapshot(LoanRequest $loanRequest): array
    {
        return [
            'user_id' => $loanRequest->user_id,
            'book_id' => $loanRequest->book_id,
            'status' => $loanRequest->status->value,
            'processed_by' => $loanRequest->processed_by,
            'requested_at' => $loanRequest->requested_at?->toDateTimeString(),
            'approved_at' => $loanRequest->approved_at?->toDateTimeString(),
            'rejected_at' => $loanRequest->rejected_at?->toDateTimeString(),
            'cancelled_at' => $loanRequest->cancelled_at?->toDateTimeString(),
            'expires_at' => $loanRequest->expires_at?->toDateTimeString(),
            'rejection_reason' => $loanRequest->rejection_reason,
            'notes' => $loanRequest->notes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function loanSnapshot(Loan $loan): array
    {
        return [
            'loan_request_id' => $loan->loan_request_id,
            'user_id' => $loan->user_id,
            'book_copy_id' => $loan->book_copy_id,
            'processed_by' => $loan->processed_by,
            'borrowed_at' => $loan->borrowed_at?->toDateString(),
            'due_at' => $loan->due_at?->toDateString(),
            'returned_at' => $loan->returned_at?->toDateString(),
            'status' => $loan->status->value,
            'return_condition' => $loan->return_condition?->value,
            'return_notes' => $loan->return_notes,
            'duration_days' => $loan->duration_days,
            'quota_snapshot' => $loan->quota_snapshot,
        ];
    }
}
