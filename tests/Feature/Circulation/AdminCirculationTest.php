<?php

namespace Tests\Feature\Circulation;

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCirculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_pending_request_and_hold_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);
        $loanRequest = LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.requests.approve', $loanRequest));

        $response->assertRedirect(route('admin.requests.show', $loanRequest));
        $response->assertSessionHas('success');

        $loanRequest->refresh();

        $this->assertSame(LoanRequestStatus::Disetujui, $loanRequest->status);
        $this->assertSame($admin->id, $loanRequest->processed_by);
        $this->assertNotNull($loanRequest->approved_at);
        $this->assertNotNull($loanRequest->expires_at);
        $this->assertSame(0, Loan::query()->count());

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'loan_request.approved',
            'entity_type' => LoanRequest::class,
            'entity_id' => $loanRequest->id,
        ]);
    }

    public function test_request_cannot_be_approved_when_all_copies_are_already_held(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        $firstRequest = LoanRequest::factory()->create([
            'user_id' => User::factory()->siswa()->create()->id,
            'book_id' => $book->id,
        ]);
        $secondRequest = LoanRequest::factory()->create([
            'user_id' => User::factory()->siswa()->create()->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.approve', $firstRequest))
            ->assertSessionHas('success');

        $response = $this->from(route('admin.requests.show', $secondRequest))
            ->actingAs($admin)
            ->post(route('admin.requests.approve', $secondRequest));

        $response->assertRedirect(route('admin.requests.show', $secondRequest));
        $response->assertSessionHasErrors('status');
        $this->assertSame(LoanRequestStatus::Menunggu, $secondRequest->refresh()->status);
    }

    public function test_admin_can_reject_request_with_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $loanRequest = LoanRequest::factory()->create();

        $invalid = $this->from(route('admin.requests.show', $loanRequest))
            ->actingAs($admin)
            ->post(route('admin.requests.reject', $loanRequest));

        $invalid->assertSessionHasErrors('rejection_reason');
        $this->assertSame(LoanRequestStatus::Menunggu, $loanRequest->refresh()->status);

        $response = $this->actingAs($admin)->post(route('admin.requests.reject', $loanRequest), [
            'rejection_reason' => 'Eksemplar sedang diperiksa.',
        ]);

        $response->assertRedirect(route('admin.requests.show', $loanRequest));

        $loanRequest->refresh();

        $this->assertSame(LoanRequestStatus::Ditolak, $loanRequest->status);
        $this->assertSame($admin->id, $loanRequest->processed_by);
        $this->assertSame('Eksemplar sedang diperiksa.', $loanRequest->rejection_reason);
        $this->assertNotNull($loanRequest->rejected_at);
    }

    public function test_admin_can_cancel_approved_request(): void
    {
        $admin = User::factory()->admin()->create();
        $loanRequest = LoanRequest::factory()->approved()->create();

        $this->actingAs($admin)
            ->post(route('admin.requests.cancel', $loanRequest))
            ->assertRedirect(route('admin.requests.show', $loanRequest));

        $loanRequest->refresh();

        $this->assertSame(LoanRequestStatus::Dibatalkan, $loanRequest->status);
        $this->assertNotNull($loanRequest->cancelled_at);
    }

    public function test_admin_checkout_creates_loan_and_marks_copy_borrowed(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);
        $loanRequest = LoanRequest::factory()->approved()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.requests.checkout.store', $loanRequest), [
            'book_copy_id' => $copy->id,
            'confirm' => '1',
        ]);

        $response->assertRedirect(route('admin.requests.show', $loanRequest));
        $response->assertSessionHas('success');

        $loan = Loan::query()->sole();

        $this->assertSame($loanRequest->id, $loan->loan_request_id);
        $this->assertSame($siswa->id, $loan->user_id);
        $this->assertSame($copy->id, $loan->book_copy_id);
        $this->assertSame($admin->id, $loan->processed_by);
        $this->assertSame(LoanStatus::Dipinjam, $loan->status);
        $this->assertSame(today()->toDateString(), $loan->borrowed_at->toDateString());
        $this->assertSame(today()->addDays(7)->toDateString(), $loan->due_at->toDateString());
        $this->assertSame(7, $loan->duration_days);
        $this->assertSame(2, $loan->quota_snapshot);

        $this->assertSame(BookCopyStatus::Dipinjam, $copy->refresh()->status);
        $this->assertSame(LoanRequestStatus::Dipinjam, $loanRequest->refresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'loan.checked_out',
            'entity_type' => Loan::class,
            'entity_id' => $loan->id,
        ]);
    }

    public function test_expired_approved_request_cannot_be_checked_out(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);
        $loanRequest = LoanRequest::factory()->approved()->create([
            'book_id' => $book->id,
            'expires_at' => now()->subHour(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.requests.checkout', $loanRequest))
            ->assertRedirect(route('admin.requests.show', $loanRequest))
            ->assertSessionHas('error');

        $response = $this->from(route('admin.requests.show', $loanRequest))
            ->actingAs($admin)
            ->post(route('admin.requests.checkout.store', $loanRequest), [
                'book_copy_id' => $copy->id,
                'confirm' => '1',
            ]);

        $response->assertRedirect(route('admin.requests.show', $loanRequest));
        $response->assertSessionHas('error');
        $this->assertSame(0, Loan::query()->count());
        $this->assertSame(LoanRequestStatus::Disetujui, $loanRequest->refresh()->status);
    }

    public function test_checkout_rejects_copy_from_another_book(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();
        $otherCopy = BookCopy::factory()->create();
        $loanRequest = LoanRequest::factory()->approved()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->from(route('admin.requests.show', $loanRequest))
            ->actingAs($admin)
            ->post(route('admin.requests.checkout.store', $loanRequest), [
                'book_copy_id' => $otherCopy->id,
                'confirm' => '1',
            ]);

        $response->assertSessionHasErrors('book_copy_id');
        $this->assertSame(0, Loan::query()->count());
        $this->assertSame(BookCopyStatus::Tersedia, $otherCopy->refresh()->status);
        $this->assertSame(LoanRequestStatus::Disetujui, $loanRequest->refresh()->status);
    }

    public function test_admin_can_return_loan_and_copy_becomes_available(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->borrowed()->create(['book_id' => $book->id]);
        $loanRequest = LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'status' => LoanRequestStatus::Dipinjam,
            'processed_by' => $admin->id,
        ]);
        $loan = Loan::factory()->create([
            'loan_request_id' => $loanRequest->id,
            'user_id' => $siswa->id,
            'book_copy_id' => $copy->id,
            'processed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.loans.return.store', $loan), [
            'return_condition' => ReturnCondition::Baik->value,
            'return_notes' => 'Buku lengkap.',
        ]);

        $response->assertRedirect(route('admin.loans.history'));
        $response->assertSessionHas('success');

        $loan->refresh();

        $this->assertSame(LoanStatus::Dikembalikan, $loan->status);
        $this->assertSame(ReturnCondition::Baik, $loan->return_condition);
        $this->assertSame(today()->toDateString(), $loan->returned_at->toDateString());
        $this->assertSame('Buku lengkap.', $loan->return_notes);

        $this->assertSame(BookCopyStatus::Tersedia, $copy->refresh()->status);
        $this->assertSame(BookCondition::Baik, $copy->condition);
        $this->assertSame(LoanRequestStatus::Dikembalikan, $loanRequest->refresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'loan.returned',
            'entity_type' => Loan::class,
            'entity_id' => $loan->id,
        ]);
    }

    public function test_damaged_return_updates_copy_condition_and_status(): void
    {
        $admin = User::factory()->admin()->create();
        $copy = BookCopy::factory()->borrowed()->create();
        $loan = Loan::factory()->create(['book_copy_id' => $copy->id]);

        $this->actingAs($admin)->post(route('admin.loans.return.store', $loan), [
            'return_condition' => ReturnCondition::RusakRingan->value,
            'return_notes' => 'Sampul lepas.',
        ])->assertRedirect(route('admin.loans.history'));

        $copy->refresh();
        $loan->refresh();

        $this->assertSame(ReturnCondition::RusakRingan, $loan->return_condition);
        $this->assertSame(BookCopyStatus::Rusak, $copy->status);
        $this->assertSame(BookCondition::RusakRingan, $copy->condition);
    }

    public function test_returned_loan_cannot_be_returned_again(): void
    {
        $admin = User::factory()->admin()->create();
        $loan = Loan::factory()->returned()->create();

        $this->actingAs($admin)
            ->post(route('admin.loans.return.store', $loan), [
                'return_condition' => ReturnCondition::Baik->value,
            ])
            ->assertRedirect(route('admin.loans.index'))
            ->assertSessionHas('error');

        $this->assertSame(LoanStatus::Dikembalikan, $loan->refresh()->status);
    }

    public function test_admin_circulation_pages_can_be_rendered(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);

        $loanRequest = LoanRequest::factory()->approved()->create([
            'book_id' => $book->id,
            'user_id' => User::factory()->siswa()->create()->id,
        ]);
        $activeLoan = Loan::factory()->create();

        $pages = [
            route('admin.requests.index'),
            route('admin.requests.show', $loanRequest),
            route('admin.requests.checkout', $loanRequest),
            route('admin.loans.index'),
            route('admin.returns.index'),
            route('admin.loans.history'),
            route('admin.loans.return', $activeLoan),
        ];

        foreach ($pages as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
