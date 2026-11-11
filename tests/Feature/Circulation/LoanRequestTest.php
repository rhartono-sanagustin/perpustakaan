<?php

namespace Tests\Feature\Circulation;

use App\Enums\LoanRequestStatus;
use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_can_submit_request_without_creating_a_loan(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        $this->actingAs($siswa)
            ->get(route('borrower.requests.create', $book))
            ->assertOk()
            ->assertSee('Kirim Pengajuan');

        $response = $this->actingAs($siswa)->post(route('borrower.requests.store', $book), [
            'notes' => 'Untuk tugas sekolah.',
        ]);

        $response->assertRedirect(route('borrower.loans.index', ['tab' => 'menunggu']));
        $response->assertSessionHas('success');

        $loanRequest = LoanRequest::query()->sole();

        $this->assertSame($siswa->id, $loanRequest->user_id);
        $this->assertSame($book->id, $loanRequest->book_id);
        $this->assertSame(LoanRequestStatus::Menunggu, $loanRequest->status);
        $this->assertSame('Untuk tugas sekolah.', $loanRequest->notes);
        $this->assertSame(0, Loan::query()->count());

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $siswa->id,
            'action' => 'loan_request.created',
            'entity_type' => LoanRequest::class,
            'entity_id' => $loanRequest->id,
        ]);
    }

    public function test_duplicate_open_request_for_same_book_is_blocked(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $response = $this->from(route('borrower.requests.create', $book))
            ->actingAs($siswa)
            ->post(route('borrower.requests.store', $book));

        $response->assertRedirect(route('borrower.requests.create', $book));
        $response->assertSessionHasErrors('book');
        $this->assertSame(1, LoanRequest::query()->count());
    }

    public function test_quota_counts_active_loans_and_open_requests(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        Loan::factory()->create(['user_id' => $siswa->id]);
        LoanRequest::factory()->create(['user_id' => $siswa->id]);

        $response = $this->from(route('borrower.requests.create', $book))
            ->actingAs($siswa)
            ->post(route('borrower.requests.store', $book));

        $response->assertSessionHasErrors('book');
        $this->assertSame(1, LoanRequest::query()->count());
    }

    public function test_expired_approved_request_does_not_count_toward_quota(): void
    {
        $siswa = User::factory()->siswa()->create();
        $otherBook = Book::factory()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        LoanRequest::factory()->approved()->create([
            'user_id' => $siswa->id,
            'book_id' => $otherBook->id,
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAs($siswa)
            ->post(route('borrower.requests.store', $book))
            ->assertRedirect(route('borrower.loans.index', ['tab' => 'menunggu']));

        $this->assertSame(2, LoanRequest::query()->count());
    }

    public function test_borrower_can_cancel_own_pending_request(): void
    {
        $siswa = User::factory()->siswa()->create();
        $loanRequest = LoanRequest::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)
            ->post(route('borrower.requests.cancel', $loanRequest))
            ->assertRedirect();

        $loanRequest->refresh();

        $this->assertSame(LoanRequestStatus::Dibatalkan, $loanRequest->status);
        $this->assertNotNull($loanRequest->cancelled_at);
    }

    public function test_borrower_cannot_cancel_another_users_request(): void
    {
        $owner = User::factory()->siswa()->create();
        $other = User::factory()->siswa()->create();
        $loanRequest = LoanRequest::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->post(route('borrower.requests.cancel', $loanRequest))
            ->assertForbidden();

        $this->assertSame(LoanRequestStatus::Menunggu, $loanRequest->refresh()->status);
    }

    public function test_borrower_pages_only_show_own_circulation_data(): void
    {
        $siswa = User::factory()->siswa()->create();
        $other = User::factory()->siswa()->create();

        $ownBook = Book::factory()->create(['title' => 'Buku Milik Saya']);
        $otherBook = Book::factory()->create(['title' => 'Buku Orang Lain']);
        $ownRequestBook = Book::factory()->create(['title' => 'Pengajuan Milik Saya']);
        $otherRequestBook = Book::factory()->create(['title' => 'Pengajuan Orang Lain']);

        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::factory()->create(['book_id' => $ownBook->id])->id,
            'status' => LoanStatus::Dipinjam,
        ]);
        Loan::factory()->create([
            'user_id' => $other->id,
            'book_copy_id' => BookCopy::factory()->create(['book_id' => $otherBook->id])->id,
            'status' => LoanStatus::Dipinjam,
        ]);

        LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $ownRequestBook->id,
        ]);
        LoanRequest::factory()->create([
            'user_id' => $other->id,
            'book_id' => $otherRequestBook->id,
        ]);

        $this->actingAs($siswa)
            ->get(route('borrower.loans.index'))
            ->assertOk()
            ->assertSee('Buku Milik Saya')
            ->assertDontSee('Buku Orang Lain');

        $this->actingAs($siswa)
            ->get(route('borrower.loans.index', ['tab' => 'menunggu']))
            ->assertOk()
            ->assertSee('Pengajuan Milik Saya')
            ->assertDontSee('Pengajuan Orang Lain');
    }

    public function test_catalog_detail_offers_request_button_for_eligible_borrower(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        $this->actingAs($siswa)
            ->get(route('catalog.show', $book))
            ->assertOk()
            ->assertSee('Ajukan Peminjaman');
    }

    public function test_catalog_detail_explains_when_quota_is_full(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        BookCopy::factory()->create(['book_id' => $book->id]);

        Loan::factory()->create(['user_id' => $siswa->id]);
        LoanRequest::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)
            ->get(route('catalog.show', $book))
            ->assertOk()
            ->assertSee('Kuota peminjaman Anda sudah penuh');
    }

    public function test_borrowers_cannot_open_admin_circulation_pages(): void
    {
        $siswa = User::factory()->siswa()->create();

        foreach ([
            'admin.requests.index',
            'admin.loans.index',
            'admin.returns.index',
            'admin.loans.history',
        ] as $routeName) {
            $this->actingAs($siswa)->get(route($routeName))->assertForbidden();
        }
    }
}
