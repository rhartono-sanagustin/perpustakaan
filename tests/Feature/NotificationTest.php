<?php

namespace Tests\Feature;

use App\Enums\LoanRequestStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_notification_endpoints(): void
    {
        $this->getJson(route('admin.notifications'))->assertUnauthorized();
        $this->getJson(route('notifications.status'))->assertUnauthorized();
    }

    public function test_borrower_cannot_access_admin_notifications(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->getJson(route('admin.notifications'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_borrower_notifications(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->getJson(route('notifications.status'))
            ->assertForbidden();
    }

    public function test_admin_notifications_summarize_pending_requests_and_overdue_loans(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        $olderPending = LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'requested_at' => now()->subMinutes(10),
        ]);
        $latestPending = LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'requested_at' => now()->subMinute(),
        ]);
        LoanRequest::factory()->approved()->create([
            'user_id' => $siswa->id,
            'requested_at' => now(),
        ]);

        Loan::factory()->overdue()->create([
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::factory()->borrowed()->create(),
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.notifications'));

        $response->assertOk()
            ->assertJsonPath('pending_requests', 2)
            ->assertJsonPath('overdue_loans', 1)
            ->assertJsonPath('latest_request_id', $latestPending->id)
            ->assertJsonPath('latest_request.borrower', $siswa->name)
            ->assertJsonPath('latest_request.book', $latestPending->book->title)
            ->assertJsonStructure([
                'pending_requests',
                'overdue_loans',
                'latest_request_id',
                'latest_request' => ['borrower', 'book', 'requested_at', 'url'],
                'requests_url',
                'overdue_url',
                'generated_at',
            ]);

        $this->assertNotSame($olderPending->id, $latestPending->id);
    }

    public function test_borrower_notifications_are_scoped_to_the_authenticated_user(): void
    {
        $siswa = User::factory()->siswa()->create();
        $otherSiswa = User::factory()->siswa()->create();

        $pendingBook = Book::factory()->create(['title' => 'Buku Menunggu Milik Sendiri']);
        LoanRequest::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $pendingBook->id,
            'status' => LoanRequestStatus::Menunggu,
            'requested_at' => now()->subMinute(),
        ]);
        LoanRequest::factory()->approved()->create([
            'user_id' => $siswa->id,
            'book_id' => Book::factory()->create(['title' => 'Buku Disetujui Milik Sendiri'])->id,
            'requested_at' => now()->subMinutes(2),
        ]);

        $overdueBook = Book::factory()->create(['title' => 'Buku Terlambat Milik Sendiri']);
        Loan::factory()->overdue()->create([
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::factory()->borrowed()->create(['book_id' => $overdueBook->id]),
        ]);

        $activeBook = Book::factory()->create(['title' => 'Buku Aktif Milik Sendiri']);
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::factory()->borrowed()->create(['book_id' => $activeBook->id]),
        ]);

        $otherBook = Book::factory()->create(['title' => 'Buku Milik Pengguna Lain']);
        LoanRequest::factory()->create([
            'user_id' => $otherSiswa->id,
            'book_id' => $otherBook->id,
        ]);
        Loan::factory()->overdue()->create([
            'user_id' => $otherSiswa->id,
            'book_copy_id' => BookCopy::factory()->borrowed()->create(['book_id' => $otherBook->id]),
        ]);

        $response = $this->actingAs($siswa)->getJson(route('notifications.status'));

        $response->assertOk()
            ->assertJsonPath('pending_requests', 1)
            ->assertJsonPath('active_loans', 2)
            ->assertJsonPath('overdue_loans', 1)
            ->assertJsonCount(2, 'requests')
            ->assertJsonCount(2, 'loans')
            ->assertJsonPath('requests.0.book', $pendingBook->title)
            ->assertJsonStructure([
                'pending_requests',
                'active_loans',
                'overdue_loans',
                'requests' => [['id', 'book', 'status', 'status_label', 'updated_at', 'url']],
                'loans' => [['id', 'book', 'status_label', 'due_at', 'due_at_label', 'days_until_due', 'overdue', 'url']],
                'requests_url',
                'loans_url',
                'generated_at',
            ])
            ->assertJsonMissing(['book' => $otherBook->title]);
    }
}
