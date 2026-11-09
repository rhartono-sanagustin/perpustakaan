<?php

namespace Tests\Feature\Admin;

use App\Enums\BookCopyStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCopyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_book_copy(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.books.copies.store', $book), [
            'inventory_code' => 'INV-BARU-001',
            'shelf_location' => 'Rak Z-1',
            'condition' => 'baik',
            'status' => 'tersedia',
            'notes' => 'Eksemplar baru.',
        ]);

        $response->assertRedirect(route('admin.books.copies.index', $book));
        $this->assertDatabaseHas('book_copies', [
            'book_id' => $book->id,
            'inventory_code' => 'INV-BARU-001',
            'status' => 'tersedia',
        ]);
    }

    public function test_admin_cannot_create_copy_with_borrowed_status_manually(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.books.copies.store', $book), [
            'inventory_code' => 'INV-BARU-002',
            'condition' => 'baik',
            'status' => 'dipinjam',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseMissing('book_copies', ['inventory_code' => 'INV-BARU-002']);
    }

    public function test_copy_with_active_loan_cannot_be_marked_available(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $copy = BookCopy::factory()->borrowed()->create();

        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_copy_id' => $copy->id,
            'processed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.books.copies.update', [$copy->book, $copy]), [
            'inventory_code' => $copy->inventory_code,
            'shelf_location' => $copy->shelf_location,
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertSame(BookCopyStatus::Dipinjam, $copy->fresh()->status);
    }

    public function test_copy_with_transaction_history_is_deactivated_instead_of_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $copy = BookCopy::factory()->create();

        Loan::factory()->returned()->create([
            'user_id' => User::factory()->siswa()->create()->id,
            'book_copy_id' => $copy->id,
            'processed_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.books.copies.destroy', [$copy->book, $copy]))
            ->assertRedirect(route('admin.books.copies.index', $copy->book));

        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'status' => 'nonaktif',
        ]);
    }

    public function test_copy_must_belong_to_the_book_in_the_route(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();
        $otherCopy = BookCopy::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.books.copies.destroy', [$book, $otherCopy]))
            ->assertNotFound();
    }

    public function test_borrower_cannot_manage_copies(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $this->actingAs($siswa)
            ->post(route('admin.books.copies.store', $book), [])
            ->assertForbidden();
    }
}
