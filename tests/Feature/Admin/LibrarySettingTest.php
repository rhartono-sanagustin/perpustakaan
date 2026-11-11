<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\LibrarySetting;
use App\Models\User;
use App\Services\CirculationService;
use Database\Seeders\LibrarySettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibrarySettingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, int>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'max_loans_siswa' => 2,
            'max_loans_guru' => 5,
            'loan_duration_siswa' => 7,
            'loan_duration_guru' => 14,
            'pickup_deadline_days' => 1,
        ], $overrides);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Sirkulasi')
            ->assertSee('Maksimal pinjaman siswa')
            ->assertSee('Batas pengambilan permintaan');
    }

    public function test_borrowers_cannot_access_settings(): void
    {
        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.settings.index'))
                ->assertForbidden();

            $this->actingAs($borrower)
                ->put(route('admin.settings.update'), $this->payload())
                ->assertForbidden();
        }
    }

    public function test_admin_can_update_settings_and_changes_are_audited(): void
    {
        $this->seed(LibrarySettingSeeder::class);

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $this->payload([
            'max_loans_siswa' => 4,
            'max_loans_guru' => 8,
            'loan_duration_siswa' => 10,
            'loan_duration_guru' => 21,
            'pickup_deadline_days' => 3,
        ]));

        $response->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success', '5 pengaturan berhasil diperbarui. Perubahan berlaku untuk transaksi baru.');

        $this->assertDatabaseHas('library_settings', [
            'key' => 'max_loans_siswa',
            'value' => '4',
            'updated_by' => $admin->id,
        ]);

        $this->assertSame(4, app(CirculationService::class)->maxLoans(User::factory()->siswa()->create()));
        $this->assertSame(21, app(CirculationService::class)->loanDurationDays(User::factory()->guru()->create()));

        $this->assertSame(5, AuditLog::query()->where('action', 'library_setting.updated')->count());

        $log = AuditLog::query()
            ->where('action', 'library_setting.updated')
            ->where('entity_type', LibrarySetting::class)
            ->get()
            ->first(fn (AuditLog $log): bool => ($log->new_values['key'] ?? null) === 'max_loans_siswa');

        $this->assertNotNull($log);
        $this->assertSame(4, $log->new_values['value']);
    }

    public function test_unchanged_settings_are_not_audited(): void
    {
        $this->seed(LibrarySettingSeeder::class);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), $this->payload())
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success', 'Tidak ada perubahan pengaturan yang disimpan.');

        $this->assertSame(0, AuditLog::query()->where('action', 'library_setting.updated')->count());
    }

    public function test_settings_validation_rejects_out_of_range_values(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), $this->payload([
                'max_loans_siswa' => 0,
                'loan_duration_guru' => 91,
            ]))
            ->assertSessionHasErrors(['max_loans_siswa', 'loan_duration_guru']);

        $this->assertDatabaseMissing('library_settings', ['key' => 'max_loans_siswa']);
    }
}
