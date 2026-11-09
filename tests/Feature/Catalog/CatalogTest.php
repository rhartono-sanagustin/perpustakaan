<?php

namespace Tests\Feature\Catalog;

use App\Enums\BookCopyStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_only_active_books_with_availability_counts(): void
    {
        $category = Category::factory()->create(['name' => 'Fiksi', 'slug' => 'fiksi']);
        $publisher = Publisher::factory()->create();

        $activeBook = Book::factory()->create([
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'title' => 'Buku Aktif',
        ]);
        BookCopy::factory()->create(['book_id' => $activeBook->id]);
        BookCopy::factory()->borrowed()->create(['book_id' => $activeBook->id]);

        $archivedBook = Book::factory()->inactive()->create([
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'title' => 'Buku Diarsipkan',
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee('Buku Aktif');
        $response->assertSee('Tersedia 1 eksemplar');
        $response->assertDontSee('Buku Diarsipkan');
    }

    public function test_catalog_can_be_searched_by_title_author_and_isbn(): void
    {
        $category = Category::factory()->create();
        $publisher = Publisher::factory()->create();

        $author = Author::factory()->create(['name' => 'Andrea Hirata']);
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'title' => 'Laskar Pelangi',
            'isbn' => '9789793062792',
        ]);
        $book->authors()->sync([$author->id]);

        $otherBook = Book::factory()->create([
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'title' => 'Bumi Manusia',
            'isbn' => '9789799731234',
        ]);

        $this->get(route('catalog.index', ['q' => 'Laskar']))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertDontSee('Bumi Manusia');

        $this->get(route('catalog.index', ['q' => 'Andrea']))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertDontSee('Bumi Manusia');

        $this->get(route('catalog.index', ['q' => '9789799731234']))
            ->assertOk()
            ->assertSee('Bumi Manusia')
            ->assertDontSee('Laskar Pelangi');
    }

    public function test_catalog_can_filter_by_category_and_availability(): void
    {
        $fiction = Category::factory()->create(['name' => 'Fiksi', 'slug' => 'fiksi']);
        $science = Category::factory()->create(['name' => 'Sains', 'slug' => 'sains']);
        $publisher = Publisher::factory()->create();

        $availableBook = Book::factory()->create([
            'category_id' => $fiction->id,
            'publisher_id' => $publisher->id,
            'title' => 'Fiksi Tersedia',
        ]);
        BookCopy::factory()->create(['book_id' => $availableBook->id]);

        $unavailableBook = Book::factory()->create([
            'category_id' => $science->id,
            'publisher_id' => $publisher->id,
            'title' => 'Sains Tidak Tersedia',
        ]);
        BookCopy::factory()->create([
            'book_id' => $unavailableBook->id,
            'status' => BookCopyStatus::Rusak,
        ]);

        $this->get(route('catalog.index', ['category' => 'fiksi']))
            ->assertOk()
            ->assertSee('Fiksi Tersedia')
            ->assertDontSee('Sains Tidak Tersedia');

        $this->get(route('catalog.index', ['availability' => 'tersedia']))
            ->assertOk()
            ->assertSee('Fiksi Tersedia')
            ->assertDontSee('Sains Tidak Tersedia');

        $this->get(route('catalog.index', ['availability' => 'habis']))
            ->assertOk()
            ->assertSee('Sains Tidak Tersedia')
            ->assertDontSee('Fiksi Tersedia');
    }

    public function test_book_detail_displays_metadata_and_availability(): void
    {
        $category = Category::factory()->create(['name' => 'Fiksi']);
        $publisher = Publisher::factory()->create(['name' => 'Bentang Pustaka']);

        $book = Book::factory()->create([
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'title' => 'Laskar Pelangi',
            'isbn' => '9789793062792',
            'publication_year' => 2005,
            'language' => 'Indonesia',
            'summary' => 'Ringkasan khusus untuk pengujian detail buku.',
        ]);
        BookCopy::factory()->create(['book_id' => $book->id, 'shelf_location' => 'Rak A-1']);
        BookCopy::factory()->borrowed()->create(['book_id' => $book->id]);

        $response = $this->get(route('catalog.show', $book));

        $response->assertOk();
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('Bentang Pustaka');
        $response->assertSee('9789793062792');
        $response->assertSee('Ringkasan khusus untuk pengujian detail buku.');
        $response->assertSee('Tersedia 1 eksemplar');
        $response->assertSee('Rak A-1');
    }

    public function test_archived_book_detail_returns_not_found(): void
    {
        $book = Book::factory()->inactive()->create();

        $this->get(route('catalog.show', $book))->assertNotFound();
    }
}
