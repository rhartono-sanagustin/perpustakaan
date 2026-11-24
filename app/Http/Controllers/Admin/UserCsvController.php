<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportUsersRequest;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\CsvExporter;
use App\Services\UserImportService;
use App\Services\XlsxExporter;
use App\Support\UserFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class UserCsvController extends Controller
{
    /** @var list<string> */
    private const EXPORT_HEADERS = [
        'Nama',
        'Nomor Induk',
        'Email',
        'Peran',
        'Status',
        'Kelas',
        'Unit Kerja',
        'Nomor Telepon',
    ];

    /** @var list<string> */
    private const IMPORT_HEADERS = [
        'Nama',
        'Nomor Induk',
        'Email',
        'Peran',
        'Status',
        'Kelas',
        'Unit Kerja',
        'Nomor Telepon',
        'Kata Sandi',
    ];

    /** @var list<int> */
    private const EXPORT_WIDTHS = [28, 18, 32, 12, 12, 16, 20, 18];

    /** @var list<int> */
    private const IMPORT_WIDTHS = [28, 18, 32, 12, 12, 16, 20, 18, 20];

    public function __construct(
        private readonly CsvExporter $csvExporter,
        private readonly XlsxExporter $xlsxExporter,
        private readonly UserImportService $importService,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function export(Request $request): Response
    {
        $this->authorize('export', User::class);

        $format = $this->requestedFormat($request);
        $filters = UserFilters::fromRequest($request);
        $query = $filters->apply(User::query())->orderBy('name');
        $count = $query->count();

        $this->auditLogger->recordEvent('user.exported', User::class, null, null, [
            'format' => $format,
            'count' => $count,
            'filters' => $filters->toQuery(),
        ]);

        $rows = (function () use ($query): \Generator {
            foreach ($query->lazy(500) as $user) {
                yield $this->exportRow($user);
            }
        })();

        $filename = 'pengguna-'.now()->format('Ymd-His');

        if ($format === 'xlsx') {
            return $this->xlsxExporter->download(
                $filename.'.xlsx',
                'Pengguna',
                self::EXPORT_HEADERS,
                $rows,
                self::EXPORT_WIDTHS
            );
        }

        return $this->csvExporter->stream($filename.'.csv', self::EXPORT_HEADERS, $rows);
    }

    public function importForm(): View
    {
        $this->authorize('import', User::class);

        return view('admin.users.import', [
            'report' => session('import_report'),
            'maxRows' => (int) config('library.imports.max_rows', 1000),
            'maxFileKb' => (int) config('library.imports.max_file_kb', 5120),
        ]);
    }

    public function import(ImportUsersRequest $request): RedirectResponse
    {
        $this->authorize('import', User::class);

        /** @var User $actor */
        $actor = $request->user();

        try {
            $report = $this->importService->import($request->file('file'), $actor);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('admin.users.import')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.users.import')
            ->with('import_report', $report->toArray())
            ->with($report->hasFailures() ? 'warning' : 'success', $report->summary());
    }

    public function template(Request $request): Response
    {
        $this->authorize('import', User::class);

        $format = $this->requestedFormat($request);
        $filename = 'template-impor-pengguna-'.now()->format('Ymd');

        if ($format === 'xlsx') {
            return $this->xlsxExporter->download(
                $filename.'.xlsx',
                'Template Pengguna',
                self::IMPORT_HEADERS,
                [],
                self::IMPORT_WIDTHS
            );
        }

        return $this->csvExporter->stream($filename.'.csv', self::IMPORT_HEADERS, []);
    }

    /**
     * @return array<int, string>
     */
    private function exportRow(User $user): array
    {
        return [
            $user->name,
            $user->nomor_induk,
            $user->email,
            $user->roleLabel(),
            $user->statusLabel(),
            $user->kelas,
            $user->unit_kerja,
            $user->no_telepon,
        ];
    }

    private function requestedFormat(Request $request): string
    {
        return $request->query('format') === 'xlsx' ? 'xlsx' : 'csv';
    }
}
