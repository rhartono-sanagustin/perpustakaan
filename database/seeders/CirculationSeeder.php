<?php

namespace Database\Seeders;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Enums\LoanRequestStatus;
use App\Enums\LoanStatus;
use App\Enums\ReturnCondition;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class CirculationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('nomor_induk', 'ADM001')->firstOrFail();
        $guru = User::where('nomor_induk', 'NIP001')->firstOrFail();
        $siswa = User::where('nomor_induk', 'NIS001')->firstOrFail();

        $activeRequest = $this->requestFor($siswa, Book::where('slug', 'laskar-pelangi')->firstOrFail(), [
            'processed_by' => $admin->id,
            'status' => LoanRequestStatus::Dipinjam,
            'requested_at' => now()->subDays(5),
            'approved_at' => now()->subDays(4),
        ]);

        $this->loanFor($activeRequest, [
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::where('inventory_code', 'LP-002')->firstOrFail()->id,
            'processed_by' => $admin->id,
            'borrowed_at' => now()->subDays(3)->toDateString(),
            'due_at' => now()->addDays(4)->toDateString(),
            'status' => LoanStatus::Dipinjam,
            'duration_days' => 7,
            'quota_snapshot' => 2,
        ]);

        $overdueRequest = $this->requestFor($guru, Book::where('slug', 'sapiens-riwayat-singkat-umat-manusia')->firstOrFail(), [
            'processed_by' => $admin->id,
            'status' => LoanRequestStatus::Dipinjam,
            'requested_at' => now()->subDays(25),
            'approved_at' => now()->subDays(24),
        ]);

        $this->loanFor($overdueRequest, [
            'user_id' => $guru->id,
            'book_copy_id' => BookCopy::where('inventory_code', 'SP-002')->firstOrFail()->id,
            'processed_by' => $admin->id,
            'borrowed_at' => now()->subDays(20)->toDateString(),
            'due_at' => now()->subDays(6)->toDateString(),
            'status' => LoanStatus::Dipinjam,
            'duration_days' => 14,
            'quota_snapshot' => 5,
        ]);

        $this->requestFor($siswa, Book::where('slug', 'bumi-manusia')->firstOrFail(), [
            'processed_by' => null,
            'status' => LoanRequestStatus::Menunggu,
            'requested_at' => now()->subHours(3),
            'approved_at' => null,
            'expires_at' => null,
        ]);

        $returnedRequest = $this->requestFor($siswa, Book::where('slug', 'perahu-kertas')->firstOrFail(), [
            'processed_by' => $admin->id,
            'status' => LoanRequestStatus::Dikembalikan,
            'requested_at' => now()->subDays(40),
            'approved_at' => now()->subDays(39),
        ]);

        $returnedLoan = $this->loanFor($returnedRequest, [
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::where('inventory_code', 'PK-001')->firstOrFail()->id,
            'processed_by' => $admin->id,
            'borrowed_at' => now()->subDays(38)->toDateString(),
            'due_at' => now()->subDays(31)->toDateString(),
            'returned_at' => now()->subDays(20)->toDateString(),
            'status' => LoanStatus::Dikembalikan,
            'return_condition' => ReturnCondition::Baik,
            'return_notes' => 'Buku dikembalikan dalam kondisi baik.',
            'duration_days' => 7,
            'quota_snapshot' => 2,
        ]);

        $this->requestFor($guru, Book::where('slug', 'kosmos')->firstOrFail(), [
            'processed_by' => $admin->id,
            'status' => LoanRequestStatus::Ditolak,
            'requested_at' => now()->subDays(2),
            'rejected_at' => now()->subDays(2),
            'rejection_reason' => 'Eksemplar sedang dalam proses pemeriksaan kondisi.',
        ]);

        BookCopy::where('inventory_code', 'LP-002')->update(['status' => BookCopyStatus::Dipinjam]);
        BookCopy::where('inventory_code', 'SP-002')->update(['status' => BookCopyStatus::Dipinjam]);
        BookCopy::where('inventory_code', 'PK-001')->update(['status' => BookCopyStatus::Tersedia]);

        if ($returnedLoan->copy) {
            $returnedLoan->copy->update([
                'condition' => BookCondition::Baik,
                'status' => BookCopyStatus::Tersedia,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function requestFor(User $user, Book $book, array $attributes): LoanRequest
    {
        return LoanRequest::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            array_merge([
                'rejection_reason' => null,
                'cancelled_at' => null,
                'notes' => null,
            ], $attributes)
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function loanFor(LoanRequest $request, array $attributes): Loan
    {
        return Loan::updateOrCreate(
            ['loan_request_id' => $request->id],
            array_merge([
                'returned_at' => null,
                'return_condition' => null,
                'return_notes' => null,
            ], $attributes)
        );
    }
}
