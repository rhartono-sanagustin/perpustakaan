<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_admin_actions_are_recorded(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();

        $this->actingAs($admin)->post(route('admin.books.store'), [
            'title' => 'Buku Audit',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'authors' => [$author->id],
            'isbn' => '9786021111111',
            'publication_year' => 2025,
            'edition' => 'Cetakan 1',
            'language' => 'Indonesia',
            'summary' => 'Ringkasan untuk pengujian audit.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.books.index'));

        $book = Book::query()->where('title', 'Buku Audit')->firstOrFail();
        $created = AuditLog::query()->where('action', 'book.created')->firstOrFail();

        $this->assertSame($admin->id, $created->actor_id);
        $this->assertSame(Book::class, $created->entity_type);
        $this->assertSame($book->id, $created->entity_id);
        $this->assertSame('Buku Audit', $created->new_values['title']);
        $this->assertNull($created->old_values);

        $this->actingAs($admin)->put(route('admin.books.update', $book), [
            'title' => 'Buku Audit Edisi Revisi',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'authors' => [$author->id],
            'isbn' => '9786021111111',
            'publication_year' => 2025,
            'edition' => 'Cetakan 1',
            'language' => 'Indonesia',
            'summary' => 'Ringkasan untuk pengujian audit.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.books.index'));

        $updated = AuditLog::query()->where('action', 'book.updated')->latest('id')->firstOrFail();

        $this->assertSame('Buku Audit', $updated->old_values['title']);
        $this->assertSame('Buku Audit Edisi Revisi', $updated->new_values['title']);
        $this->assertArrayNotHasKey('isbn', $updated->new_values);

        $this->actingAs($admin)
            ->post(route('admin.books.archive', $book))
            ->assertRedirect(route('admin.books.index'));

        $archived = AuditLog::query()->where('action', 'book.archived')->latest('id')->firstOrFail();

        $this->assertTrue($archived->old_values['is_active']);
        $this->assertFalse($archived->new_values['is_active']);
    }

    public function test_copy_and_master_data_actions_are_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();

        $this->actingAs($admin)->post(route('admin.books.copies.store', $book), [
            'inventory_code' => 'INV-AUDIT-001',
            'shelf_location' => 'Rak Audit',
            'condition' => 'baik',
            'status' => 'tersedia',
        ])->assertRedirect(route('admin.books.copies.index', $book));

        $copy = BookCopy::query()->where('inventory_code', 'INV-AUDIT-001')->firstOrFail();
        $copyCreated = AuditLog::query()->where('action', 'book_copy.created')->firstOrFail();

        $this->assertSame('INV-AUDIT-001', $copyCreated->new_values['inventory_code']);

        $this->actingAs($admin)->put(route('admin.books.copies.update', [$book, $copy]), [
            'inventory_code' => 'INV-AUDIT-001',
            'shelf_location' => 'Rak Audit Baru',
            'condition' => 'rusak_berat',
            'status' => 'rusak',
        ])->assertRedirect(route('admin.books.copies.index', $book));

        $copyUpdated = AuditLog::query()->where('action', 'book_copy.updated')->latest('id')->firstOrFail();

        $this->assertSame('tersedia', $copyUpdated->old_values['status']);
        $this->assertSame('rusak', $copyUpdated->new_values['status']);
        $this->assertSame('Rak Audit', $copyUpdated->old_values['shelf_location']);
        $this->assertSame('Rak Audit Baru', $copyUpdated->new_values['shelf_location']);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Kategori Audit',
            'description' => 'Deskripsi kategori audit.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::query()->where('name', 'Kategori Audit')->firstOrFail();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'category.created',
            'entity_id' => $category->id,
            'actor_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.categories.toggle', $category))
            ->assertRedirect(route('admin.categories.index'));

        $categoryToggled = AuditLog::query()->where('action', 'category.updated')->latest('id')->firstOrFail();

        $this->assertTrue($categoryToggled->old_values['is_active']);
        $this->assertFalse($categoryToggled->new_values['is_active']);

        $this->actingAs($admin)
            ->post(route('admin.authors.store'), ['name' => 'Penulis Audit'])
            ->assertRedirect(route('admin.authors.index'));

        $this->actingAs($admin)
            ->post(route('admin.publishers.store'), ['name' => 'Penerbit Audit'])
            ->assertRedirect(route('admin.publishers.index'));

        $this->assertDatabaseHas('audit_logs', ['action' => 'author.created', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'publisher.created', 'actor_id' => $admin->id]);
    }

    public function test_copy_deletion_and_deactivation_are_recorded(): void
    {
        $admin = User::factory()->admin()->create();

        $deletedCopy = BookCopy::factory()->create();
        $this->actingAs($admin)
            ->delete(route('admin.books.copies.destroy', [$deletedCopy->book, $deletedCopy]))
            ->assertRedirect(route('admin.books.copies.index', $deletedCopy->book));

        $deletedLog = AuditLog::query()->where('action', 'book_copy.deleted')->firstOrFail();

        $this->assertSame($deletedCopy->id, $deletedLog->entity_id);
        $this->assertSame($deletedCopy->inventory_code, $deletedLog->old_values['inventory_code']);

        $deactivatedCopy = BookCopy::factory()->create();
        Loan::factory()->returned()->create([
            'user_id' => User::factory()->siswa()->create()->id,
            'book_copy_id' => $deactivatedCopy->id,
            'processed_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.books.copies.destroy', [$deactivatedCopy->book, $deactivatedCopy]))
            ->assertRedirect(route('admin.books.copies.index', $deactivatedCopy->book));

        $deactivatedLog = AuditLog::query()->where('action', 'book_copy.deactivated')->firstOrFail();

        $this->assertSame('tersedia', $deactivatedLog->old_values['status']);
        $this->assertSame('nonaktif', $deactivatedLog->new_values['status']);
    }

    public function test_denied_action_does_not_create_audit_log(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $this->actingAs($siswa)
            ->put(route('admin.books.update', $book), [])
            ->assertForbidden();

        $this->assertDatabaseCount('audit_logs', 0);
    }
}
