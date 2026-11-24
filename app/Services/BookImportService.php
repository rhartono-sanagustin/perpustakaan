<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Support\ImportReport;
use App\Support\SpreadsheetData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookImportService
{
    /**
     * Nama kolom yang wajib ada, beserta alias yang diterima.
     *
     * @var array<string, list<string>>
     */
    private const REQUIRED_HEADERS = [
        'Judul' => ['Judul', 'Title'],
        'Kategori' => ['Kategori', 'Category'],
        'Penulis' => ['Penulis', 'Pengarang', 'Author'],
    ];

    public function __construct(
        private readonly SpreadsheetReader $reader,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function import(UploadedFile $file, bool $createMissing = true): ImportReport
    {
        $data = $this->reader->read($file);
        $missing = $this->missingHeaders($data);

        if ($missing !== []) {
            throw new InvalidArgumentException(
                'Kolom wajib tidak ditemukan: '.implode(', ', $missing).'.'
            );
        }

        $report = new ImportReport(mb_substr($file->getClientOriginalName(), 0, 255));
        $seenIds = [];
        $seenIsbns = [];

        /** @var Collection<string, Category> $categories */
        $categories = Category::query()->get()->keyBy(
            fn (Category $category): string => SpreadsheetData::normalize($category->name)
        );

        /** @var Collection<string, Author> $authors */
        $authors = Author::query()->get()->keyBy(
            fn (Author $author): string => SpreadsheetData::normalize($author->name)
        );

        /** @var Collection<string, Publisher> $publishers */
        $publishers = Publisher::query()->get()->keyBy(
            fn (Publisher $publisher): string => SpreadsheetData::normalize($publisher->name)
        );

        DB::transaction(function () use (
            $data,
            $createMissing,
            $report,
            $categories,
            $authors,
            $publishers,
            &$seenIds,
            &$seenIsbns
        ): void {
            foreach ($data->rows as $row) {
                $line = $row['line'];

                try {
                    $this->importRow(
                        $data,
                        $row['values'],
                        $createMissing,
                        $categories,
                        $authors,
                        $publishers,
                        $seenIds,
                        $seenIsbns,
                        $report
                    );
                } catch (InvalidArgumentException $exception) {
                    $report->markFailed($line, $exception->getMessage());
                }
            }
        });

        $this->auditLogger->recordEvent('book.imported', Book::class, null, null, [
            'filename' => $report->filename,
            'created' => $report->created,
            'updated' => $report->updated,
            'failed' => $report->failed,
        ]);

        return $report;
    }

    private function missingHeaders(SpreadsheetData $data): array
    {
        $missing = [];

        foreach (self::REQUIRED_HEADERS as $label => $aliases) {
            if (! $data->hasAny($aliases)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    /**
     * @param  list<string>  $values
     * @param  Collection<string, Category>  $categories
     * @param  Collection<string, Author>  $authors
     * @param  Collection<string, Publisher>  $publishers
     * @param  array<int, bool>  $seenIds
     * @param  array<string, bool>  $seenIsbns
     */
    private function importRow(
        SpreadsheetData $data,
        array $values,
        bool $createMissing,
        Collection $categories,
        Collection $authors,
        Collection $publishers,
        array &$seenIds,
        array &$seenIsbns,
        ImportReport $report
    ): void {
        $idRaw = $data->value($values, ['ID', 'ID Buku', 'Book ID']);
        $title = $data->value($values, ['Judul', 'Title']);
        $isbn = $data->value($values, ['ISBN']);
        $categoryName = $data->value($values, ['Kategori', 'Category']);
        $authorNames = $data->value($values, ['Penulis', 'Pengarang', 'Author']);
        $publisherName = $data->value($values, ['Penerbit', 'Publisher']);
        $yearRaw = $data->value($values, ['Tahun Terbit', 'Tahun']);
        $edition = $data->value($values, ['Edisi']);
        $language = $data->value($values, ['Bahasa']);
        $summary = $data->value($values, ['Ringkasan', 'Sinopsis']);
        $status = $this->parseStatus($data->value($values, ['Status Katalog', 'Status']));

        if ($title === '') {
            throw new InvalidArgumentException('Judul wajib diisi.');
        }

        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException('Judul maksimal 255 karakter.');
        }

        if (mb_strlen($isbn) > 32) {
            throw new InvalidArgumentException('ISBN maksimal 32 karakter.');
        }

        if ($categoryName === '') {
            throw new InvalidArgumentException('Kategori wajib diisi.');
        }

        $book = null;
        $id = null;

        if ($idRaw !== '') {
            if (! ctype_digit($idRaw)) {
                throw new InvalidArgumentException('ID buku harus berupa angka.');
            }

            $id = (int) $idRaw;

            if (isset($seenIds[$id])) {
                throw new InvalidArgumentException('ID buku '.$id.' muncul lebih dari sekali di berkas.');
            }

            $book = Book::query()->find($id);

            if ($book === null) {
                throw new InvalidArgumentException('Buku dengan ID '.$id.' tidak ditemukan.');
            }
        }

        if ($isbn !== '') {
            if (isset($seenIsbns[$isbn])) {
                throw new InvalidArgumentException('ISBN '.$isbn.' muncul lebih dari sekali di berkas.');
            }

            if ($book === null) {
                $book = Book::query()->where('isbn', $isbn)->first();
            }

            $this->assertIsbnAvailable($isbn, $book);
        }

        $year = $this->parseYear($yearRaw);

        if (mb_strlen($edition) > 100) {
            throw new InvalidArgumentException('Edisi maksimal 100 karakter.');
        }

        if (mb_strlen($language) > 50) {
            throw new InvalidArgumentException('Bahasa maksimal 50 karakter.');
        }

        if (mb_strlen($summary) > 5000) {
            throw new InvalidArgumentException('Ringkasan maksimal 5.000 karakter.');
        }

        $category = $this->resolveCategory($categoryName, $categories, $createMissing);
        $authorIds = $this->resolveAuthors($authorNames, $authors, $createMissing);
        $publisher = $publisherName !== ''
            ? $this->resolvePublisher($publisherName, $publishers, $createMissing)
            : null;

        if ($book === null) {
            $book = Book::query()->create([
                'title' => $title,
                'category_id' => $category->id,
                'publisher_id' => $publisher?->id,
                'isbn' => $isbn !== '' ? $isbn : null,
                'publication_year' => $year,
                'edition' => $edition !== '' ? $edition : null,
                'language' => $language !== '' ? $language : 'Indonesia',
                'summary' => $summary !== '' ? $summary : null,
                'is_active' => $status ?? true,
            ]);

            $report->markCreated();
        } else {
            $attributes = [
                'title' => $title,
                'category_id' => $category->id,
            ];

            if ($publisher !== null) {
                $attributes['publisher_id'] = $publisher->id;
            }

            if ($isbn !== '') {
                $attributes['isbn'] = $isbn;
            }

            if ($year !== null) {
                $attributes['publication_year'] = $year;
            }

            if ($edition !== '') {
                $attributes['edition'] = $edition;
            }

            if ($language !== '') {
                $attributes['language'] = $language;
            }

            if ($summary !== '') {
                $attributes['summary'] = $summary;
            }

            if ($status !== null) {
                $attributes['is_active'] = $status;
            }

            $book->update($attributes);
            $report->markUpdated();
        }

        $book->authors()->sync($authorIds);

        if ($id !== null) {
            $seenIds[$id] = true;
        }

        if ($isbn !== '') {
            $seenIsbns[$isbn] = true;
        }
    }

    /**
     * @param  Collection<string, Category>  $categories
     */
    private function resolveCategory(string $name, Collection $categories, bool $createMissing): Category
    {
        $key = SpreadsheetData::normalize($name);
        $category = $categories->get($key);

        if ($category !== null) {
            return $category;
        }

        if (! $createMissing) {
            throw new InvalidArgumentException(
                'Kategori "'.$name.'" belum ada. Tambahkan data master atau aktifkan opsi buat otomatis.'
            );
        }

        $category = Category::query()->create(['name' => $name, 'is_active' => true]);
        $categories->put($key, $category);

        return $category;
    }

    /**
     * @param  Collection<string, Author>  $authors
     * @return list<int>
     */
    private function resolveAuthors(string $raw, Collection $authors, bool $createMissing): array
    {
        $names = [];

        foreach (array_map('trim', explode('|', $raw)) as $name) {
            $key = SpreadsheetData::normalize($name);

            if ($key !== '') {
                $names[$key] = $name;
            }
        }

        if ($names === []) {
            throw new InvalidArgumentException(
                'Penulis wajib diisi. Pisahkan beberapa penulis dengan tanda |.'
            );
        }

        if (count($names) > 20) {
            throw new InvalidArgumentException('Maksimal 20 penulis untuk satu buku.');
        }

        $resolved = [];

        foreach ($names as $key => $name) {
            $author = $authors->get($key);

            if ($author === null) {
                if (! $createMissing) {
                    throw new InvalidArgumentException(
                        'Penulis "'.$name.'" belum ada. Tambahkan data master atau aktifkan opsi buat otomatis.'
                    );
                }

                $author = Author::query()->create(['name' => $name]);
                $authors->put($key, $author);
            }

            $resolved[$key] = $author->id;
        }

        return array_values($resolved);
    }

    /**
     * @param  Collection<string, Publisher>  $publishers
     */
    private function resolvePublisher(string $name, Collection $publishers, bool $createMissing): Publisher
    {
        $key = SpreadsheetData::normalize($name);
        $publisher = $publishers->get($key);

        if ($publisher !== null) {
            return $publisher;
        }

        if (! $createMissing) {
            throw new InvalidArgumentException(
                'Penerbit "'.$name.'" belum ada. Tambahkan data master atau aktifkan opsi buat otomatis.'
            );
        }

        $publisher = Publisher::query()->create(['name' => $name]);
        $publishers->put($key, $publisher);

        return $publisher;
    }

    private function parseYear(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        if (! ctype_digit($value)) {
            throw new InvalidArgumentException('Tahun terbit harus berupa angka.');
        }

        $year = (int) $value;
        $max = (int) date('Y') + 1;

        if ($year < 1000 || $year > $max) {
            throw new InvalidArgumentException('Tahun terbit harus antara 1000 dan '.$max.'.');
        }

        return $year;
    }

    private function parseStatus(string $value): ?bool
    {
        $normalized = mb_strtolower(trim($value));

        return match ($normalized) {
            '' => null,
            'aktif', 'active', '1', 'ya', 'true' => true,
            'arsip', 'diarsipkan', 'nonaktif', 'inactive', '0', 'tidak', 'false' => false,
            default => throw new InvalidArgumentException(
                'Status katalog "'.$value.'" tidak dikenal. Gunakan Aktif atau Arsip.'
            ),
        };
    }

    private function assertIsbnAvailable(string $isbn, ?Book $book): void
    {
        if ($isbn === '') {
            return;
        }

        $query = Book::query()->where('isbn', $isbn);

        if ($book !== null) {
            $query->whereKeyNot($book->getKey());
        }

        if ($query->exists()) {
            throw new InvalidArgumentException('ISBN '.$isbn.' sudah digunakan buku lain.');
        }
    }
}
