<?php

namespace App\Services;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Enums\LoanStatus;
use App\Enums\ReturnCondition;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Support\ReportFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ReportService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(ReportFilters $filters): array
    {
        $copiesByStatus = $this->inventorySummary();

        return [
            'total_books' => Book::query()->active()->count(),
            'total_copies' => array_sum($copiesByStatus),
            'copies_by_status' => $copiesByStatus,
            'active_loans' => Loan::query()->active()->count(),
            'overdue_loans' => Loan::query()->overdue()->count(),
            'pending_requests' => LoanRequest::query()->pending()->count(),
            'approved_requests' => LoanRequest::query()->approvedActive()->count(),
            'loans_in_period' => Loan::query()
                ->whereBetween('borrowed_at', [$filters->from->toDateString(), $filters->to->toDateString()])
                ->count(),
            'returns_in_period' => Loan::query()
                ->whereNotNull('returned_at')
                ->whereBetween('returned_at', [$filters->from->toDateString(), $filters->to->toDateString()])
                ->count(),
            'categories' => Category::query()
                ->withCount(['books as active_books_count' => fn (Builder $query) => $query->active()])
                ->orderBy('name')
                ->get(),
            'top_books' => $this->popularBooks($filters, 5),
        ];
    }

    public function loanQuery(ReportFilters $filters, string $mode = 'peminjaman'): Builder
    {
        $mode = $mode === 'pengembalian' ? 'pengembalian' : 'peminjaman';
        $dateColumn = $mode === 'pengembalian' ? 'returned_at' : 'borrowed_at';

        $query = Loan::query()
            ->with(['user', 'copy.book.category', 'processor'])
            ->whereDate($dateColumn, '>=', $filters->from->toDateString())
            ->whereDate($dateColumn, '<=', $filters->to->toDateString());

        if ($mode === 'pengembalian') {
            $query->whereNotNull('returned_at');
        }

        $this->applyLoanRelationFilters($query, $filters);

        if ($mode === 'pengembalian') {
            $condition = ReturnCondition::tryFrom($filters->condition);

            if ($condition !== null) {
                $query->where('return_condition', $condition->value);
            }
        } else {
            $this->applyLoanStatusFilter($query, $filters->status);
        }

        return $query->orderByDesc($dateColumn)->orderByDesc('id');
    }

    public function overdueQuery(ReportFilters $filters): Builder
    {
        $query = Loan::query()
            ->overdue()
            ->with(['user', 'copy.book.category']);

        $this->applyLoanRelationFilters($query, $filters);

        return $query->orderBy('due_at')->orderBy('id');
    }

    public function inventoryQuery(ReportFilters $filters): Builder
    {
        return BookCopy::query()
            ->with(['book.category'])
            ->when($filters->search !== '', function (Builder $query) use ($filters): void {
                $like = '%'.$filters->search.'%';

                $query->where(function (Builder $query) use ($like): void {
                    $query->where('inventory_code', 'like', $like)
                        ->orWhereHas('book', fn (Builder $book) => $book
                            ->where('title', 'like', $like)
                            ->orWhere('isbn', 'like', $like));
                });
            })
            ->when($filters->categoryId !== null, fn (Builder $query) => $query
                ->whereHas('book', fn (Builder $book) => $book->where('category_id', $filters->categoryId)))
            ->when(
                BookCopyStatus::tryFrom($filters->status),
                fn (Builder $query, BookCopyStatus $status) => $query->where('status', $status->value)
            )
            ->when(
                BookCondition::tryFrom($filters->condition),
                fn (Builder $query, BookCondition $condition) => $query->where('condition', $condition->value)
            )
            ->orderBy('inventory_code');
    }

    /**
     * @return array<string, int>
     */
    public function inventorySummary(): array
    {
        $counts = BookCopy::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(BookCopyStatus::cases())
            ->mapWithKeys(fn (BookCopyStatus $status): array => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }

    /**
     * @return Collection<int, Book>
     */
    public function popularBooks(ReportFilters $filters, int $limit = 10): Collection
    {
        $dateRange = [$filters->from->toDateString(), $filters->to->toDateString()];

        return Book::query()
            ->with(['category', 'authors'])
            ->withCount([
                'loans as checkout_count' => fn (Builder $query) => $query->whereBetween('borrowed_at', $dateRange),
            ])
            ->whereHas('loans', fn (Builder $query) => $query->whereBetween('borrowed_at', $dateRange))
            ->when($filters->categoryId !== null, fn (Builder $query) => $query
                ->where('category_id', $filters->categoryId))
            ->when($filters->search !== '', fn (Builder $query) => $query->search($filters->search))
            ->orderByDesc('checkout_count')
            ->orderBy('title')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    public function printData(string $report, ReportFilters $filters): array
    {
        return match ($report) {
            'ringkasan' => [
                'title' => 'Laporan Ringkasan Operasional',
                'summary' => $this->summary($filters),
            ],
            'peminjaman' => [
                'title' => 'Laporan Peminjaman',
                'mode' => 'peminjaman',
                'loans' => $this->loanQuery($filters, 'peminjaman')->get(),
            ],
            'pengembalian' => [
                'title' => 'Laporan Pengembalian',
                'mode' => 'pengembalian',
                'loans' => $this->loanQuery($filters, 'pengembalian')->get(),
            ],
            'keterlambatan' => [
                'title' => 'Laporan Keterlambatan',
                'loans' => $this->overdueQuery($filters)->get(),
            ],
            'inventaris' => [
                'title' => 'Laporan Inventaris',
                'copies' => $this->inventoryQuery($filters)->get(),
                'copies_by_status' => $this->inventorySummary(),
            ],
            'populer' => [
                'title' => 'Laporan Buku Populer',
                'books' => $this->popularBooks($filters, $filters->limit),
            ],
            default => throw new InvalidArgumentException('Jenis laporan tidak dikenal.'),
        };
    }

    /**
     * @return array{filename: string, headers: list<string>, rows: iterable<int, array<int, mixed>>}
     */
    public function csv(string $report, ReportFilters $filters): array
    {
        return match ($report) {
            'ringkasan' => $this->summaryCsv($filters),
            'peminjaman' => $this->loanCsv($filters, 'peminjaman'),
            'pengembalian' => $this->loanCsv($filters, 'pengembalian'),
            'keterlambatan' => $this->overdueCsv($filters),
            'inventaris' => $this->inventoryCsv($filters),
            'populer' => $this->popularCsv($filters),
            default => throw new InvalidArgumentException('Jenis laporan tidak dikenal.'),
        };
    }

    /**
     * @return array{filename: string, headers: list<string>, rows: list<array<int, mixed>>}
     */
    private function summaryCsv(ReportFilters $filters): array
    {
        $summary = $this->summary($filters);
        $rows = [
            ['Periode', $filters->periodLabel()],
            ['Total judul aktif', $summary['total_books']],
            ['Total eksemplar', $summary['total_copies']],
            ['Peminjaman aktif', $summary['active_loans']],
            ['Terlambat', $summary['overdue_loans']],
            ['Permintaan menunggu', $summary['pending_requests']],
            ['Persetujuan menahan stok', $summary['approved_requests']],
            ['Peminjaman pada periode', $summary['loans_in_period']],
            ['Pengembalian pada periode', $summary['returns_in_period']],
        ];

        foreach (BookCopyStatus::cases() as $status) {
            $rows[] = ['Eksemplar '.$status->label(), $summary['copies_by_status'][$status->value]];
        }

        return [
            'filename' => $this->filename('ringkasan', $filters),
            'headers' => ['Metrik', 'Nilai'],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{filename: string, headers: list<string>, rows: iterable<int, array<int, mixed>>}
     */
    private function loanCsv(ReportFilters $filters, string $mode): array
    {
        $query = $this->loanQuery($filters, $mode);
        $isReturn = $mode === 'pengembalian';

        $headers = [
            'No. Transaksi',
            'Peminjam',
            'Nomor Induk',
            'Peran',
            'Kelas/Unit',
            'Judul Buku',
            'Kode Inventaris',
            'Tanggal Pinjam',
            $isReturn ? 'Tanggal Kembali' : 'Jatuh Tempo',
            $isReturn ? 'Kondisi Kembali' : 'Status',
            'Petugas',
        ];

        $rows = (function () use ($query, $isReturn): iterable {
            foreach ($query->cursor() as $loan) {
                yield [
                    $loan->reference,
                    $loan->user->name,
                    $loan->user->nomor_induk,
                    $loan->user->roleLabel(),
                    $loan->user->kelas ?? $loan->user->unit_kerja ?? '-',
                    $loan->copy->book->title,
                    $loan->copy->inventory_code,
                    $loan->borrowed_at?->toDateString(),
                    $isReturn ? $loan->returned_at?->toDateString() : $loan->due_at?->toDateString(),
                    $isReturn
                        ? ($loan->return_condition?->label() ?? '-')
                        : $loan->statusLabel(),
                    $loan->processor?->name ?? '-',
                ];
            }
        })();

        return [
            'filename' => $this->filename($mode, $filters),
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * @return array{filename: string, headers: list<string>, rows: iterable<int, array<int, mixed>>}
     */
    private function overdueCsv(ReportFilters $filters): array
    {
        $query = $this->overdueQuery($filters);

        $rows = (function () use ($query): iterable {
            foreach ($query->cursor() as $loan) {
                yield [
                    $loan->reference,
                    $loan->user->name,
                    $loan->user->nomor_induk,
                    $loan->user->roleLabel(),
                    $loan->user->kelas ?? $loan->user->unit_kerja ?? '-',
                    $loan->user->no_telepon ?? '-',
                    $loan->copy->book->title,
                    $loan->copy->inventory_code,
                    $loan->borrowed_at?->toDateString(),
                    $loan->due_at?->toDateString(),
                    $loan->daysOverdue(),
                ];
            }
        })();

        return [
            'filename' => $this->filename('keterlambatan', $filters),
            'headers' => [
                'No. Transaksi',
                'Peminjam',
                'Nomor Induk',
                'Peran',
                'Kelas/Unit',
                'Telepon',
                'Judul Buku',
                'Kode Inventaris',
                'Tanggal Pinjam',
                'Jatuh Tempo',
                'Hari Terlambat',
            ],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{filename: string, headers: list<string>, rows: iterable<int, array<int, mixed>>}
     */
    private function inventoryCsv(ReportFilters $filters): array
    {
        $query = $this->inventoryQuery($filters);

        $rows = (function () use ($query): iterable {
            foreach ($query->cursor() as $copy) {
                yield [
                    $copy->inventory_code,
                    $copy->book->title,
                    $copy->book->category?->name ?? '-',
                    $copy->shelf_location ?? '-',
                    $copy->condition->label(),
                    $copy->status->label(),
                    $copy->notes ?? '-',
                ];
            }
        })();

        return [
            'filename' => $this->filename('inventaris', $filters),
            'headers' => [
                'Kode Inventaris',
                'Judul Buku',
                'Kategori',
                'Lokasi Rak',
                'Kondisi',
                'Status',
                'Catatan',
            ],
            'rows' => $rows,
        ];
    }

    /**
     * @return array{filename: string, headers: list<string>, rows: list<array<int, mixed>>}
     */
    private function popularCsv(ReportFilters $filters): array
    {
        $books = $this->popularBooks($filters, $filters->limit);

        $rows = [];

        foreach ($books->values() as $index => $book) {
            $rows[] = [
                $index + 1,
                $book->title,
                $book->authors_label ?: '-',
                $book->category?->name ?? '-',
                $book->checkout_count,
            ];
        }

        return [
            'filename' => $this->filename('populer', $filters),
            'headers' => ['Peringkat', 'Judul Buku', 'Penulis', 'Kategori', 'Jumlah Checkout'],
            'rows' => $rows,
        ];
    }

    private function applyLoanRelationFilters(Builder $query, ReportFilters $filters): void
    {
        if ($filters->search !== '') {
            $like = '%'.$filters->search.'%';

            $query->where(function (Builder $query) use ($like): void {
                $query->whereHas('user', fn (Builder $user) => $user
                    ->where('name', 'like', $like)
                    ->orWhere('nomor_induk', 'like', $like))
                    ->orWhereHas('copy', function (Builder $copy) use ($like): void {
                        $copy->where('inventory_code', 'like', $like)
                            ->orWhereHas('book', fn (Builder $book) => $book
                                ->where('title', 'like', $like)
                                ->orWhere('isbn', 'like', $like));
                    });
            });
        }

        if ($filters->categoryId !== null) {
            $query->whereHas('copy.book', fn (Builder $book) => $book
                ->where('category_id', $filters->categoryId));
        }

        if ($filters->role !== null) {
            $query->whereHas('user', fn (Builder $user) => $user
                ->where('role', $filters->role->value));
        }
    }

    private function applyLoanStatusFilter(Builder $query, string $status): void
    {
        match ($status) {
            'dipinjam' => $query
                ->where('status', LoanStatus::Dipinjam)
                ->whereDate('due_at', '>=', today()->toDateString()),
            'terlambat' => $query
                ->where('status', LoanStatus::Dipinjam)
                ->whereDate('due_at', '<', today()->toDateString()),
            'dikembalikan' => $query->where('status', LoanStatus::Dikembalikan),
            default => null,
        };
    }

    private function filename(string $report, ReportFilters $filters): string
    {
        return 'laporan-'.$report.'-'.$filters->from->format('Ymd').'-'.$filters->to->format('Ymd').'.csv';
    }
}
