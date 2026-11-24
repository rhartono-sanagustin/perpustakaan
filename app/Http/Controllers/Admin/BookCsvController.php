<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportBooksRequest;
use App\Models\Book;
use App\Services\AuditLogger;
use App\Services\BookImportService;
use App\Services\CsvExporter;
use App\Services\XlsxExporter;
use App\Support\BookFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class BookCsvController extends Controller
{
    /** @var list<string> */
    private const HEADERS = [
        'ID',
        'Judul',
        'ISBN',
        'Kategori',
        'Penulis',
        'Penerbit',
        'Tahun Terbit',
        'Edisi',
        'Bahasa',
        'Ringkasan',
        'Status Katalog',
    ];

    /** @var list<int> */
    private const WIDTHS = [8, 40, 20, 20, 32, 24, 14, 14, 14, 50, 16];

    public function __construct(
        private readonly CsvExporter $csvExporter,
        private readonly XlsxExporter $xlsxExporter,
        private readonly BookImportService $importService,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function export(Request $request): Response
    {
        $this->authorize('export', Book::class);

        $format = $this->requestedFormat($request);
        $filters = BookFilters::fromRequest($request);
        $query = $filters->apply(Book::query())
            ->with(['category', 'publisher', 'authors'])
            ->latest();
        $count = $query->count();

        $this->auditLogger->recordEvent('book.exported', Book::class, null, null, [
            'format' => $format,
            'count' => $count,
            'filters' => $filters->toQuery(),
        ]);

        $rows = (function () use ($query): \Generator {
            foreach ($query->lazy(500) as $book) {
                yield $this->exportRow($book);
            }
        })();

        $filename = 'katalog-buku-'.now()->format('Ymd-His');

        if ($format === 'xlsx') {
            return $this->xlsxExporter->download(
                $filename.'.xlsx',
                'Katalog Buku',
                self::HEADERS,
                $rows,
                self::WIDTHS
            );
        }

        return $this->csvExporter->stream($filename.'.csv', self::HEADERS, $rows);
    }

    public function importForm(): View
    {
        $this->authorize('import', Book::class);

        return view('admin.books.import', [
            'report' => session('import_report'),
            'maxRows' => (int) config('library.imports.max_rows', 1000),
            'maxFileKb' => (int) config('library.imports.max_file_kb', 5120),
        ]);
    }

    public function import(ImportBooksRequest $request): RedirectResponse
    {
        $this->authorize('import', Book::class);

        try {
            $report = $this->importService->import(
                $request->file('file'),
                $request->boolean('create_missing')
            );
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.books.import')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.books.import')
            ->with('import_report', $report->toArray())
            ->with($report->hasFailures() ? 'warning' : 'success', $report->summary());
    }

    public function template(Request $request): Response
    {
        $this->authorize('import', Book::class);

        $format = $this->requestedFormat($request);
        $filename = 'template-impor-buku-'.now()->format('Ymd');

        if ($format === 'xlsx') {
            return $this->xlsxExporter->download(
                $filename.'.xlsx',
                'Template Buku',
                self::HEADERS,
                [],
                self::WIDTHS
            );
        }

        return $this->csvExporter->stream($filename.'.csv', self::HEADERS, []);
    }

    /**
     * @return array<int, string|int|null>
     */
    private function exportRow(Book $book): array
    {
        return [
            $book->id,
            $book->title,
            $book->isbn,
            $book->category?->name,
            $book->authors->pluck('name')->implode('|'),
            $book->publisher?->name,
            $book->publication_year,
            $book->edition,
            $book->language,
            $book->summary,
            $book->is_active ? 'Aktif' : 'Diarsipkan',
        ];
    }

    private function requestedFormat(Request $request): string
    {
        return $request->query('format') === 'xlsx' ? 'xlsx' : 'csv';
    }
}
