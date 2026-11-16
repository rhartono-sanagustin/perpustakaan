<?php

namespace Tests\Feature;

use App\Enums\BookCopyStatus;
use App\Enums\LoanRequestStatus;
use App\Enums\LoanStatus;
use App\Enums\ReturnCondition;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibraryVisit;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MvpFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_mvp_flow_from_catalog_request_to_return_and_reporting(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@perpus.test',
        ]);
        $student = User::factory()->siswa()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@perpus.test',
            'nomor_induk' => 'NIS12345',
            'kelas' => 'XI IPA 1',
        ]);

        $book = Book::factory()->create([
            'title' => 'Laskar Pelangi',
            'slug' => 'laskar-pelangi',
        ]);
        $copy = BookCopy::factory()->for($book)->create([
            'inventory_code' => 'INV-001',
            'status' => BookCopyStatus::Tersedia,
        ]);

        // 1. Tamu dapat menelusuri katalog tanpa login.
        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Laskar Pelangi');

        $this->get(route('catalog.show', $book))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertSee('Tersedia 1 eksemplar');

        // 2. Siswa login memakai nomor induk.
        $this->post(route('login'), [
            'login' => 'NIS12345',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student);

        // 3. Siswa mengajukan peminjaman per judul; eksemplar belum ditetapkan.
        $this->get(route('borrower.requests.create', $book))
            ->assertOk()
            ->assertSee('Laskar Pelangi');

        $this->post(route('borrower.requests.store', $book), [
            'notes' => 'Untuk tugas Bahasa Indonesia.',
        ])
            ->assertRedirect(route('borrower.loans.index', ['tab' => 'menunggu']))
            ->assertSessionHas('success');

        $loanRequest = LoanRequest::query()->where('user_id', $student->id)->firstOrFail();

        $this->assertSame(LoanRequestStatus::Menunggu, $loanRequest->status);
        $this->assertSame(BookCopyStatus::Tersedia, $copy->fresh()->status);
        $this->assertDatabaseCount('loans', 0);

        $this->get(route('borrower.loans.index', ['tab' => 'menunggu']))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertSee('Menunggu');

        // 4. Admin login dan menyetujui pengajuan.
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login'), [
            'login' => 'admin@perpus.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->get(route('admin.requests.index'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Laskar Pelangi');

        $this->post(route('admin.requests.approve', $loanRequest))
            ->assertRedirect(route('admin.requests.show', $loanRequest))
            ->assertSessionHas('success');

        $loanRequest->refresh();

        $this->assertSame(LoanRequestStatus::Disetujui, $loanRequest->status);
        $this->assertSame($admin->id, $loanRequest->processed_by);
        $this->assertNotNull($loanRequest->expires_at);
        $this->assertDatabaseCount('loans', 0);
        $this->assertSame(BookCopyStatus::Tersedia, $copy->fresh()->status);

        // 5. Admin mencatat checkout dengan eksemplar tertentu.
        $this->get(route('admin.requests.checkout', $loanRequest))
            ->assertOk()
            ->assertSee('INV-001');

        $this->post(route('admin.requests.checkout.store', $loanRequest), [
            'book_copy_id' => $copy->id,
            'confirm' => '1',
        ])
            ->assertRedirect(route('admin.requests.show', $loanRequest))
            ->assertSessionHas('success');

        $loan = Loan::query()->firstOrFail();

        $this->assertSame($student->id, $loan->user_id);
        $this->assertSame($copy->id, $loan->book_copy_id);
        $this->assertSame($admin->id, $loan->processed_by);
        $this->assertSame(LoanStatus::Dipinjam, $loan->status);
        $this->assertSame(7, $loan->duration_days);
        $this->assertSame(2, $loan->quota_snapshot);
        $this->assertTrue($loan->due_at->isSameDay(today()->addDays(7)));
        $this->assertSame(BookCopyStatus::Dipinjam, $copy->fresh()->status);
        $this->assertSame(LoanRequestStatus::Dipinjam, $loanRequest->fresh()->status);

        $this->get(route('admin.loans.index'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('INV-001');

        // 6. Admin mencatat pengembalian dan kondisi buku.
        $this->get(route('admin.loans.return', $loan))
            ->assertOk()
            ->assertSee('INV-001');

        $this->post(route('admin.loans.return.store', $loan), [
            'return_condition' => ReturnCondition::Baik->value,
            'return_notes' => 'Buku kembali lengkap.',
        ])
            ->assertRedirect(route('admin.loans.history'))
            ->assertSessionHas('success');

        $loan->refresh();

        $this->assertSame(LoanStatus::Dikembalikan, $loan->status);
        $this->assertTrue($loan->returned_at->isToday());
        $this->assertSame(ReturnCondition::Baik, $loan->return_condition);
        $this->assertSame(BookCopyStatus::Tersedia, $copy->fresh()->status);
        $this->assertSame(LoanRequestStatus::Dikembalikan, $loanRequest->fresh()->status);

        // 7. Riwayat admin, laporan, dan ekspor CSV memuat transaksi.
        $this->get(route('admin.loans.history'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Laskar Pelangi');

        $this->get(route('admin.reports.loans'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Laskar Pelangi');

        $export = $this->get(route('admin.reports.export', ['report' => 'peminjaman']))
            ->assertOk();

        $this->assertStringContainsString('Laskar Pelangi', $export->streamedContent());

        // 8. Peminjam melihat riwayat setelah login kembali.
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'login' => 'NIS12345',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('borrower.loans.history'))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertSee('Dikembalikan');

        // 9. Kartu anggota, kunjungan, dan grafik dashboard.
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'login' => 'admin@perpus.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('admin.users.card', $student))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('NIS12345')
            ->assertSee('member-card-barcode', false);

        $this->post(route('admin.visits.store'), ['nomor_induk' => 'NIS12345'])
            ->assertRedirect(route('admin.visits.index', ['anggota' => $student->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('library_visits', [
            'user_id' => $student->id,
            'recorded_by' => $admin->id,
        ]);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kunjungan Perpustakaan')
            ->assertSee('1 kunjungan')
            ->assertSee('hari ini 1');

        // 10. Jejak audit tindakan penting tersimpan.
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'loan.checked_out',
            'entity_id' => $loan->id,
            'actor_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'loan.returned',
            'entity_id' => $loan->id,
            'actor_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'visit.recorded',
            'actor_id' => $admin->id,
        ]);
        $this->assertSame(1, LibraryVisit::query()->count());
    }
}
