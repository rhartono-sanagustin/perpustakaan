<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;
use Tests\TestCase;

class UserImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_users_as_csv_with_filters(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->siswa()->create([
            'name' => 'Siswa Ekspor',
            'nomor_induk' => '00123456',
            'kelas' => 'XI IPA 1',
        ]);
        User::factory()->guru()->create(['name' => 'Guru Lain']);

        $response = $this->actingAs($admin)->get(route('admin.users.export', [
            'format' => 'csv',
            'role' => 'siswa',
            'q' => 'Siswa Ekspor',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));

        $content = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Nama;"Nomor Induk";Email;Peran;Status', $content);
        $this->assertStringContainsString('"Siswa Ekspor";00123456', $content);
        $this->assertStringNotContainsString('Guru Lain', $content);

        $log = AuditLog::query()->where('action', 'user.exported')->firstOrFail();

        $this->assertSame(User::class, $log->entity_type);
        $this->assertNull($log->entity_id);
        $this->assertSame('csv', $log->new_values['format']);
        $this->assertSame(1, $log->new_values['count']);
        $this->assertSame('Siswa Ekspor', $log->new_values['filters']['q']);
        $this->assertSame('siswa', $log->new_values['filters']['role']);
    }

    public function test_admin_can_export_users_as_excel(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->guru()->create([
            'name' => 'Guru Excel',
            'nomor_induk' => '00998877',
            'email' => 'guru.excel@example.test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.export', ['format' => 'xlsx']));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $xlsx = SimpleXLSX::parseData($response->getContent());

        $this->assertNotFalse($xlsx);

        $rows = $xlsx->rows(0);

        $this->assertSame('Nama', $rows[0][0]);

        $exported = collect($rows)->firstWhere(1, '00998877');

        $this->assertNotNull($exported);
        $this->assertSame('Guru Excel', $exported[0]);
        $this->assertSame('guru.excel@example.test', $exported[2]);
    }

    public function test_admin_can_import_users_from_csv_with_partial_success(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->siswa()->create([
            'name' => 'Nama Lama',
            'nomor_induk' => 'NIS0001',
            'password' => 'sandi-lama-123',
        ]);

        $csv = implode("\n", [
            'Nama;Nomor Induk;Email;Peran;Status;Kelas;Unit Kerja;Nomor Telepon;Kata Sandi',
            'Siswa Baru;NIS0002;siswa.baru@example.test;siswa;aktif;X IPA 1;;081200000001;rahasia123',
            'Nama Baru;NIS0001;;siswa;;XI IPA 2;;;',
            'Tanpa Sandi;NIS0003;;siswa;aktif;;;;',
            'Peran Salah;NIS0004;;kepala;; ;;;rahasia123',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('pengguna.csv', $csv),
        ]);

        $response->assertRedirect(route('admin.users.import'))
            ->assertSessionHas('warning');

        $report = session('import_report');

        $this->assertSame(1, $report['created']);
        $this->assertSame(1, $report['updated']);
        $this->assertSame(2, $report['failed']);
        $this->assertCount(2, $report['errors']);

        $created = User::query()->where('nomor_induk', 'NIS0002')->firstOrFail();

        $this->assertSame('Siswa Baru', $created->name);
        $this->assertTrue(Hash::check('rahasia123', $created->password));

        $existing->refresh();

        $this->assertSame('Nama Baru', $existing->name);
        $this->assertSame('XI IPA 2', $existing->kelas);
        $this->assertTrue(Hash::check('sandi-lama-123', $existing->password));

        $this->assertDatabaseMissing('users', ['nomor_induk' => 'NIS0003']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.imported',
            'entity_type' => User::class,
            'entity_id' => null,
        ]);
    }

    public function test_user_import_requires_a_password_for_new_accounts(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = implode("\n", [
            'Nama;Nomor Induk;Peran;Kata Sandi',
            'Siswa Tanpa Sandi;NIS0100;siswa;',
        ]);

        $this->actingAs($admin)->post(route('admin.users.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('pengguna.csv', $csv),
        ])->assertRedirect(route('admin.users.import'));

        $this->assertDatabaseMissing('users', ['nomor_induk' => 'NIS0100']);
        $this->assertStringContainsString(
            'Kata sandi wajib diisi untuk akun baru',
            session('import_report')['errors'][0]['message']
        );
    }

    public function test_user_import_can_read_excel_and_preserves_leading_zeros(): void
    {
        $admin = User::factory()->admin()->create();

        $xlsx = SimpleXLSXGen::fromArray([
            ['Nama', 'Nomor Induk', 'Peran', 'Kata Sandi'],
            ['Siswa Excel', "\0".'00112233', 'siswa', 'rahasia123'],
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('pengguna.xlsx', (string) $xlsx),
        ]);

        $response->assertRedirect(route('admin.users.import'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'nomor_induk' => '00112233',
            'name' => 'Siswa Excel',
        ]);
    }

    public function test_user_import_cannot_modify_the_currently_logged_in_admin(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Utama',
            'nomor_induk' => 'ADM001',
        ]);

        $csv = implode("\n", [
            'Nama;Nomor Induk;Peran',
            'Admin Lain;ADM001;siswa',
        ]);

        $this->actingAs($admin)->post(route('admin.users.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('pengguna.csv', $csv),
        ])->assertRedirect(route('admin.users.import'));

        $this->assertSame('Admin Utama', $admin->refresh()->name);
        $this->assertSame(1, session('import_report')['failed']);
        $this->assertStringContainsString(
            'Akun yang sedang digunakan',
            session('import_report')['errors'][0]['message']
        );
    }

    public function test_user_import_rejects_duplicate_nomor_induk_in_the_file(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = implode("\n", [
            'Nama;Nomor Induk;Peran;Kata Sandi',
            'Siswa A;NIS0200;siswa;rahasia123',
            'Siswa B;NIS0200;siswa;rahasia123',
        ]);

        $this->actingAs($admin)->post(route('admin.users.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('pengguna.csv', $csv),
        ])->assertRedirect(route('admin.users.import'));

        $this->assertSame(1, session('import_report')['created']);
        $this->assertSame(1, session('import_report')['failed']);
        $this->assertStringContainsString(
            'muncul lebih dari sekali',
            session('import_report')['errors'][0]['message']
        );
    }

    public function test_user_export_and_import_are_forbidden_for_borrowers(): void
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru)->get(route('admin.users.export'))->assertForbidden();
        $this->actingAs($guru)->get(route('admin.users.import'))->assertForbidden();
        $this->actingAs($guru)->post(route('admin.users.import.store'), [
            'file' => UploadedFile::fake()->createWithContent('pengguna.csv', 'Nama;Nomor Induk;Peran'),
        ])->assertForbidden();
    }

    public function test_admin_can_open_user_import_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.import'))
            ->assertOk()
            ->assertSee('Impor Pengguna')
            ->assertSee('Template CSV');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Impor');
    }

    public function test_admin_can_download_user_import_templates(): void
    {
        $admin = User::factory()->admin()->create();

        $csv = $this->actingAs($admin)->get(route('admin.users.import.template', ['format' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('Kata Sandi', $csv->streamedContent());

        $xlsx = $this->actingAs($admin)->get(route('admin.users.import.template', ['format' => 'xlsx']));
        $xlsx->assertOk();

        $parsed = SimpleXLSX::parseData($xlsx->getContent());

        $this->assertNotFalse($parsed);
        $this->assertSame('Kata Sandi', $parsed->rows(0)[0][8]);
    }
}
