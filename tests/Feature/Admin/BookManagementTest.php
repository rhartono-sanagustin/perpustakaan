<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_book_with_authors_and_cover(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.books.store'), [
            'title' => 'Buku Baru',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'authors' => [$author->id],
            'isbn' => '9786020000001',
            'publication_year' => 2024,
            'edition' => 'Cetakan 1',
            'language' => 'Indonesia',
            'summary' => 'Ringkasan buku baru.',
            'cover' => UploadedFile::fake()->image('cover.jpg', 600, 800),
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.books.index'));

        $book = Book::query()->where('title', 'Buku Baru')->firstOrFail();

        $this->assertSame('buku-baru', $book->slug);
        $this->assertTrue($book->is_active);
        $this->assertSame($category->id, $book->category_id);
        $this->assertSame($publisher->id, $book->publisher_id);
        $this->assertDatabaseHas('book_author', [
            'book_id' => $book->id,
            'author_id' => $author->id,
        ]);
        Storage::disk('public')->assertExists($book->cover_path);
    }

    public function test_book_validation_requires_main_fields_and_unique_isbn(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create(['isbn' => '9786020000002']);

        $response = $this->actingAs($admin)->post(route('admin.books.store'), [
            'isbn' => '9786020000002',
        ]);

        $response->assertSessionHasErrors(['title', 'category_id', 'authors', 'language', 'isbn']);
    }

    public function test_admin_can_update_archive_and_activate_book(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create([
            'title' => 'Judul Lama',
            'is_active' => true,
        ]);
        $authorIds = $book->authors->pluck('id')->all();

        $response = $this->actingAs($admin)->put(route('admin.books.update', $book), [
            'title' => 'Judul Baru',
            'category_id' => $book->category_id,
            'publisher_id' => $book->publisher_id,
            'authors' => $authorIds,
            'isbn' => $book->isbn,
            'publication_year' => $book->publication_year,
            'edition' => $book->edition,
            'language' => $book->language,
            'summary' => 'Ringkasan yang diperbarui.',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.books.index'));
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Judul Baru',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.books.archive', $book))
            ->assertRedirect(route('admin.books.index'));

        $this->assertFalse($book->fresh()->is_active);

        $this->actingAs($admin)
            ->post(route('admin.books.activate', $book))
            ->assertRedirect(route('admin.books.index'));

        $this->assertTrue($book->fresh()->is_active);
    }

    public function test_borrower_cannot_access_book_management(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $this->actingAs($siswa)->get(route('admin.books.index'))->assertForbidden();
        $this->actingAs($siswa)->get(route('admin.books.create'))->assertForbidden();
        $this->actingAs($siswa)->put(route('admin.books.update', $book), [])->assertForbidden();
    }
}
