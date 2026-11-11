<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Enums\ReturnCondition;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CsvExporter;
use App\Services\ReportService;
use App\Support\ReportFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /** @var list<string> */
    private const REPORTS = [
        'ringkasan',
        'peminjaman',
        'pengembalian',
        'keterlambatan',
        'inventaris',
        'populer',
    ];

    /** @var array<string, string> */
    private const LOAN_STATUSES = [
        'dipinjam' => 'Dipinjam',
        'terlambat' => 'Terlambat',
        'dikembalikan' => 'Dikembalikan',
    ];

    public function __construct(
        private readonly ReportService $reports,
        private readonly CsvExporter $csvExporter
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('view-reports');

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.index', $this->baseData($filters, 'ringkasan') + [
            'summary' => $this->reports->summary($filters),
        ]);
    }

    public function loans(Request $request): View
    {
        $this->authorize('view-reports');

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.loans', $this->baseData($filters, 'peminjaman') + [
            'mode' => 'peminjaman',
            'title' => 'Laporan Peminjaman',
            'subtitle' => 'Daftar transaksi peminjaman pada periode yang dipilih.',
            'loans' => $this->reports->loanQuery($filters, 'peminjaman')->paginate(15)->withQueryString(),
            'statusOptions' => self::LOAN_STATUSES,
        ]);
    }

    public function returns(Request $request): View
    {
        $this->authorize('view-reports');

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.loans', $this->baseData($filters, 'pengembalian') + [
            'mode' => 'pengembalian',
            'title' => 'Laporan Pengembalian',
            'subtitle' => 'Daftar transaksi pengembalian beserta kondisi buku pada periode yang dipilih.',
            'loans' => $this->reports->loanQuery($filters, 'pengembalian')->paginate(15)->withQueryString(),
            'conditionOptions' => collect(ReturnCondition::cases())
                ->mapWithKeys(fn (ReturnCondition $condition): array => [$condition->value => $condition->label()])
                ->all(),
        ]);
    }

    public function overdue(Request $request): View
    {
        $this->authorize('view-reports');

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.overdue', $this->baseData($filters, 'keterlambatan') + [
            'loans' => $this->reports->overdueQuery($filters)->paginate(15)->withQueryString(),
        ]);
    }

    public function inventory(Request $request): View
    {
        $this->authorize('view-reports');

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.inventory', $this->baseData($filters, 'inventaris') + [
            'copies' => $this->reports->inventoryQuery($filters)->paginate(20)->withQueryString(),
            'copiesByStatus' => $this->reports->inventorySummary(),
            'statusOptions' => collect(BookCopyStatus::cases())
                ->mapWithKeys(fn (BookCopyStatus $status): array => [$status->value => $status->label()])
                ->all(),
            'conditionOptions' => collect(BookCondition::cases())
                ->mapWithKeys(fn (BookCondition $condition): array => [$condition->value => $condition->label()])
                ->all(),
        ]);
    }

    public function popular(Request $request): View
    {
        $this->authorize('view-reports');

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.popular', $this->baseData($filters, 'populer') + [
            'books' => $this->reports->popularBooks($filters, $filters->limit),
        ]);
    }

    public function export(Request $request, string $report): StreamedResponse
    {
        $this->authorize('view-reports');
        $this->ensureKnownReport($report);

        $filters = ReportFilters::fromRequest($request);
        $payload = $this->reports->csv($report, $filters);

        return $this->csvExporter->stream($payload['filename'], $payload['headers'], $payload['rows']);
    }

    public function print(Request $request, string $report): View
    {
        $this->authorize('view-reports');
        $this->ensureKnownReport($report);

        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.print', [
            'report' => $report,
            'filters' => $filters,
            'data' => $this->reports->printData($report, $filters),
            'printedAt' => now(),
            'copyStatuses' => BookCopyStatus::cases(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function baseData(ReportFilters $filters, string $activeReport): array
    {
        return [
            'filters' => $filters,
            'activeReport' => $activeReport,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'roles' => UserRole::cases(),
            'limitOptions' => [10, 25, 50, 100],
            'copyStatuses' => BookCopyStatus::cases(),
        ];
    }

    private function ensureKnownReport(string $report): void
    {
        abort_unless(in_array($report, self::REPORTS, true), 404);
    }
}
