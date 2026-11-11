<?php

namespace Tests\Feature\Admin;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /** @var list<string> */
    private const REPORTS = [
        'ringkasan',
        'peminjaman',
        'pengembalian',
        'keterlambatan',
        'inventaris',
        'populer',
    ];

    public function test_admin_can_open_every_report_page(): void
    {
        $admin = User::factory()->admin()->create();

        $routes = [
            'admin.reports.index',
            'admin.reports.loans',
            'admin.reports.returns',
            'admin.reports.overdue',
            'admin.reports.inventory',
            'admin.reports.popular',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_guests_are_redirected_and_borrowers_are_forbidden(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));

        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.reports.index'))
                ->assertForbidden();
        }
    }

    public function test_default_period_is_the_current_month(): void
    {
        $admin = User::factory()->admin()->create();
        $currentLoan = Loan::factory()->create([
            'borrowed_at' => today(),
            'due_at' => today()->addDays(7),
        ]);
        $oldLoan = Loan::factory()->create([
            'borrowed_at' => today()->subMonthNoOverflow()->startOfMonth(),
            'due_at' => today()->subMonthNoOverflow()->startOfMonth()->addDays(7),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.loans'));

        $response->assertOk()
            ->assertSee($currentLoan->reference)
            ->assertDontSee($oldLoan->reference)
            ->assertSee('value="'.today()->startOfMonth()->toDateString().'"', false)
            ->assertSee('value="'.today()->toDateString().'"', false);
    }

    public function test_loan_report_can_be_filtered_by_role_and_category(): void
    {
        $admin = User::factory()->admin()->create();
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        $bookA = Book::factory()->create(['category_id' => $categoryA->id]);
        $bookB = Book::factory()->create(['category_id' => $categoryB->id]);
        $guru = User::factory()->guru()->create();
        $siswa = User::factory()->siswa()->create();

        $guruLoan = Loan::factory()->create([
            'user_id' => $guru->id,
            'book_copy_id' => BookCopy::factory()->create(['book_id' => $bookA->id])->id,
            'borrowed_at' => today(),
            'due_at' => today()->addDays(14),
        ]);
        $siswaLoan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_copy_id' => BookCopy::factory()->create(['book_id' => $bookB->id])->id,
            'borrowed_at' => today(),
            'due_at' => today()->addDays(7),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.loans', [
                'role' => 'guru',
                'category_id' => $categoryA->id,
            ]))
            ->assertOk()
            ->assertSee($guruLoan->reference)
            ->assertDontSee($siswaLoan->reference);
    }

    public function test_all_reports_stream_csv_with_utf8_bom_and_semicolon_delimiter(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (self::REPORTS as $report) {
            $response = $this->actingAs($admin)
                ->get(route('admin.reports.export', ['report' => $report]));

            $response->assertOk();

            $csv = $response->streamedContent();

            $this->assertStringStartsWith("\xEF\xBB\xBF", $csv, "Laporan {$report} tidak memiliki BOM UTF-8.");
            $this->assertStringContainsString(';', substr($csv, 3), "Laporan {$report} tidak memakai delimiter titik koma.");
        }
    }

    public function test_csv_export_sanitizes_formula_injection(): void
    {
        $admin = User::factory()->admin()->create();
        $borrower = User::factory()->siswa()->create(['name' => '=2+3']);
        $book = Book::factory()->create(['title' => '@SUM(1,1)']);
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);

        Loan::factory()->create([
            'user_id' => $borrower->id,
            'book_copy_id' => $copy->id,
            'processed_by' => $admin->id,
            'borrowed_at' => today(),
            'due_at' => today()->addDays(7),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.export', [
            'report' => 'peminjaman',
            'date_from' => today()->toDateString(),
            'date_to' => today()->toDateString(),
        ]));

        $response->assertOk();

        $csv = $response->streamedContent();
        $spreadsheet = substr($csv, 3);
        $header = explode("\n", $spreadsheet)[0];

        $this->assertStringStartsWith('"No. Transaksi";Peminjam;"Nomor Induk";', $header);
        $this->assertStringContainsString("'=2+3", $spreadsheet);
        $this->assertStringContainsString("'@SUM(1,1)", $spreadsheet);
        $this->assertStringNotContainsString(';=2+3', $spreadsheet);
        $this->assertStringNotContainsString(';@SUM(1,1)', $spreadsheet);
    }

    public function test_every_report_can_be_printed(): void
    {
        $admin = User::factory()->admin()->create();
        $loan = Loan::factory()->create([
            'borrowed_at' => today(),
            'due_at' => today()->addDays(7),
        ]);

        foreach (self::REPORTS as $report) {
            $this->actingAs($admin)
                ->get(route('admin.reports.print', ['report' => $report]))
                ->assertOk()
                ->assertSee('Pratinjau Cetak')
                ->assertSee('Petugas Perpustakaan');
        }

        $this->actingAs($admin)
            ->get(route('admin.reports.print', [
                'report' => 'peminjaman',
                'date_from' => today()->toDateString(),
                'date_to' => today()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($loan->reference);
    }

    public function test_unknown_report_keys_return_not_found(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/reports/tidak-dikenal/export')
            ->assertNotFound();

        $this->actingAs($admin)
            ->get('/admin/reports/tidak-dikenal/print')
            ->assertNotFound();
    }
}
