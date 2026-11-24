<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;
use Tests\TestCase;

class BookImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_books_as_csv_with_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $fiksi = Category::query()->create(['name' => 'Fiksi']);
        $sains = Category::query()->create(['name' => 'Sains']);
        $author = Author::query()->create(['name' => 'Andrea Hirata']);
        $publisher = Publisher::query()->create(['name' => 'Bentang Pustaka']);

        $book = Book::query()->create([
            'title' => 'Laskar Pelangi',
            'category_id' => $fiksi->id,
            'publisher_id' => $publisher->id,
            'isbn' => '9789793062792',
            'publication_year' => 2005,
            'language' => 'Indonesia',
            'is_active' => true,
        ]);
        $book->authors()->attach($author);

        Book::factory()->create([
            'title' => 'Buku Sains',
            'category_id' => $sains->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.books.export', [
            'format' => 'csv',
            'category_id' => $fiksi->id,
        ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('ID;Judul;ISBN;Kategori;Penulis', $content);
        $this->assertStringContainsString('"Laskar Pelangi";9789793062792;Fiksi;"Andrea Hirata"', $content);
        $this->assertStringNotContainsString('Buku Sains', $content);

        $log = AuditLog::query()->where('action', 'book.exported')->firstOrFail();

        $this->assertSame(Book::class, $log->entity_type);
        $this->assertSame(1, $log->new_values['count']);
        $this->assertSame($fiksi->id, $log->new_values['filters']['category_id']);
    }

    public function test_admin_can_export_books_as_excel(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['title' => 'Buku Excel']);

        $response = $this->actingAs($admin)->get(route('admin.books.export', ['format' => 'xlsx']));

        $response->assertOk();

        $xlsx = SimpleXLSX::parseData($response->getContent());

        $this->assertNotFalse($xlsx);

        $rows = $xlsx->rows(0);

        $this->assertSame('Judul', $rows[0][1]);
        $this->assertSame('Buku Excel', $rows[1][1]);
        $this->assertSame((string) $book->id, $rows[1][0]);
    }

    public function test_admin_can_import_books_and_create_missing_master_data(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = implode("\n", [
            'ID;Judul;ISBN;Kategori;Penulis;Penerbit;Tahun Terbit;Edisi;Bahasa;Ringkasan;Status Katalog',
            ';Buku Impor;9786020000001;Fiksi Baru;Andrea Hirata|Ahmad Fuadi;Penerbit Baru;2020;Cetakan 1;Indonesia;Ringkasan singkat;Aktif',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.csv', $csv),
            'create_missing' => '1',
        ]);

        $response->assertRedirect(route('admin.books.import'))
            ->assertSessionHas('success');

        $book = Book::query()->where('isbn', '9786020000001')->firstOrFail();

        $this->assertSame('Buku Impor', $book->title);
        $this->assertSame('Fiksi Baru', $book->category->name);
        $this->assertSame('Penerbit Baru', $book->publisher->name);
        $this->assertSame(2020, $book->publication_year);
        $this->assertSame(
            ['Ahmad Fuadi', 'Andrea Hirata'],
            $book->authors()->orderBy('name')->pluck('name')->all()
        );
        $this->assertSame(0, $book->copies()->count());

        $this->assertSame(1, session('import_report')['created']);
        $this->assertSame(0, session('import_report')['failed']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'book.imported',
            'entity_type' => Book::class,
            'entity_id' => null,
        ]);
    }

    public function test_book_import_fails_when_master_data_creation_is_disabled(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = implode("\n", [
            'Judul;Kategori;Penulis',
            'Buku Tanpa Master;Kategori Asing;Penulis Asing',
        ]);

        $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.csv', $csv),
            'create_missing' => '0',
        ])->assertRedirect(route('admin.books.import'));

        $this->assertDatabaseMissing('books', ['title' => 'Buku Tanpa Master']);
        $this->assertDatabaseMissing('categories', ['name' => 'Kategori Asing']);
        $this->assertDatabaseMissing('authors', ['name' => 'Penulis Asing']);
        $this->assertSame(1, session('import_report')['failed']);
        $this->assertStringContainsString(
            'belum ada',
            session('import_report')['errors'][0]['message']
        );
    }

    public function test_book_import_updates_by_id_and_by_isbn(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::query()->create(['name' => 'Fiksi']);
        $author = Author::query()->create(['name' => 'Penulis Lama']);
        $newAuthor = Author::query()->create(['name' => 'Penulis Baru']);

        $byId = Book::query()->create([
            'title' => 'Judul Lama',
            'category_id' => $category->id,
            'isbn' => '9786020000002',
            'is_active' => true,
        ]);
        $byId->authors()->attach($author);

        $byIsbn = Book::query()->create([
            'title' => 'Judul ISBN Lama',
            'category_id' => $category->id,
            'isbn' => '9786020000003',
            'is_active' => true,
        ]);
        $byIsbn->authors()->attach($author);

        $csv = implode("\n", [
            'ID;Judul;ISBN;Kategori;Penulis;Status Katalog',
            $byId->id.';Judul Baru;9786020000002;Fiksi;Penulis Baru;Arsip',
            ';Judul ISBN Baru;9786020000003;Fiksi;Penulis Baru;Aktif',
        ]);

        $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.csv', $csv),
            'create_missing' => '1',
        ])->assertRedirect(route('admin.books.import'));

        $byId->refresh();
        $byIsbn->refresh();

        $this->assertSame('Judul Baru', $byId->title);
        $this->assertFalse($byId->is_active);
        $this->assertSame([$newAuthor->id], $byId->authors()->pluck('authors.id')->all());

        $this->assertSame('Judul ISBN Baru', $byIsbn->title);
        $this->assertTrue($byIsbn->is_active);
        $this->assertSame([$newAuthor->id], $byIsbn->authors()->pluck('authors.id')->all());

        $this->assertSame(0, session('import_report')['created']);
        $this->assertSame(2, session('import_report')['updated']);
    }

    public function test_book_import_reports_invalid_rows_without_blocking_valid_rows(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::query()->create(['name' => 'Fiksi']);

        $csv = implode("\n", [
            'ID;Judul;ISBN;Kategori;Penulis;Tahun Terbit',
            ';Buku Valid;9786020000010;Fiksi;Penulis Valid;2021',
            '999999;Buku ID Salah;9786020000011;Fiksi;Penulis Valid;2021',
            ';;9786020000012;Fiksi;Penulis Valid;2021',
            ';Tahun Salah;9786020000013;Fiksi;Penulis Valid;abcd',
            ';ISBN Ganda;9786020000010;Fiksi;Penulis Valid;2021',
        ]);

        $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.csv', $csv),
            'create_missing' => '1',
        ])->assertRedirect(route('admin.books.import'));

        $report = session('import_report');

        $this->assertSame(1, $report['created']);
        $this->assertSame(4, $report['failed']);
        $this->assertSame(5, $report['total']);
        $this->assertTrue($report['truncated'] === false);

        $messages = array_column($report['errors'], 'message');

        $this->assertContains('Buku dengan ID 999999 tidak ditemukan.', $messages);
        $this->assertContains('Judul wajib diisi.', $messages);
        $this->assertContains('Tahun terbit harus berupa angka.', $messages);
        $this->assertContains('ISBN 9786020000010 muncul lebih dari sekali di berkas.', $messages);

        $this->assertDatabaseHas('books', ['title' => 'Buku Valid']);
        $this->assertDatabaseMissing('books', ['title' => 'Buku ID Salah']);
    }

    public function test_book_import_can_read_excel_files(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::query()->create(['name' => 'Fiksi']);
        $author = Author::query()->create(['name' => 'Penulis Excel']);

        $xlsx = SimpleXLSXGen::fromArray([
            ['Judul', 'ISBN', 'Kategori', 'Penulis', 'Tahun Terbit'],
            ['Buku dari Excel', "\0".'9786020000020', 'Fiksi', 'Penulis Excel', '2022'],
        ]);

        $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.xlsx', (string) $xlsx),
            'create_missing' => '0',
        ])->assertRedirect(route('admin.books.import'));

        $book = Book::query()->where('isbn', '9786020000020')->firstOrFail();

        $this->assertSame('Buku dari Excel', $book->title);
        $this->assertSame(2022, $book->publication_year);
        $this->assertSame([$author->id], $book->authors()->pluck('authors.id')->all());
        $this->assertSame($category->id, $book->category_id);
    }

    public function test_book_import_requires_columns_and_a_valid_file(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.csv', "Judul;ISBN\nBuku;123"),
        ])->assertRedirect(route('admin.books.import'))
            ->assertSessionHas('error');

        $this->assertStringContainsString(
            'Kolom wajib tidak ditemukan',
            session('error')
        );

        $this->actingAs($admin)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.txt', 'bukan spreadsheet'),
        ])->assertSessionHasErrors('file');
    }

    public function test_book_export_and_import_are_forbidden_for_borrowers(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->get(route('admin.books.export'))->assertForbidden();
        $this->actingAs($siswa)->get(route('admin.books.import'))->assertForbidden();
        $this->actingAs($siswa)->post(route('admin.books.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('buku.csv', 'Judul;Kategori;Penulis'),
        ])->assertForbidden();
    }

    public function test_admin_can_open_book_import_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.books.import'))
            ->assertOk()
            ->assertSee('Impor Buku')
            ->assertSee('Template Excel');

        $this->actingAs($admin)
            ->get(route('admin.books.index'))
            ->assertOk()
            ->assertSee('Impor');
    }

    public function test_admin_can_download_book_import_templates(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = $this->actingAs($admin)->get(route('admin.books.import.template', ['format' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('Status Katalog', $csv->streamedContent());

        $xlsx = $this->actingAs($admin)->get(route('admin.books.import.template', ['format' => 'xlsx']));
        $xlsx->assertOk();

        $parsed = SimpleXLSX::parseData($xlsx->getContent());

        $this->assertNotFalse($parsed);
        $this->assertSame('Judul', $parsed->rows(0)[0][1]);
    }
}
