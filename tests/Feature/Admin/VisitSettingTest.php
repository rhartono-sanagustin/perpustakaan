<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\LibrarySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_visit_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.visits.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Kunjungan')
            ->assertSee('Kiosk Kunjungan Mandiri')
            ->assertSee('Aktif')
            ->assertSee(route('visits.kiosk'));
    }

    public function test_non_admin_cannot_access_visit_settings(): void
    {
        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.settings.visits.edit'))
                ->assertForbidden();

            $this->actingAs($borrower)
                ->put(route('admin.settings.visits.update'), [])
                ->assertForbidden();
        }
    }

    public function test_admin_can_disable_and_enable_the_kiosk_with_audit(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.visits.update'), [])
            ->assertRedirect(route('admin.settings.visits.edit'))
            ->assertSessionHas('success', 'Kiosk kunjungan mandiri berhasil dinonaktifkan.');

        $this->assertDatabaseHas('library_settings', [
            'key' => 'visit_kiosk_enabled',
            'value' => '0',
            'type' => 'boolean',
            'updated_by' => $admin->id,
        ]);

        $this->get(route('visits.kiosk'))->assertNotFound();

        $this->actingAs($admin)
            ->put(route('admin.settings.visits.update'), ['visit_kiosk_enabled' => '1'])
            ->assertRedirect(route('admin.settings.visits.edit'))
            ->assertSessionHas('success', 'Kiosk kunjungan mandiri berhasil diaktifkan.');

        $this->assertDatabaseHas('library_settings', [
            'key' => 'visit_kiosk_enabled',
            'value' => '1',
            'type' => 'boolean',
            'updated_by' => $admin->id,
        ]);

        $this->get(route('visits.kiosk'))->assertOk();

        $this->assertSame(2, AuditLog::query()->where('action', 'library_setting.updated')->count());

        $logs = AuditLog::query()
            ->where('action', 'library_setting.updated')
            ->orderBy('id')
            ->get();

        $this->assertNull($logs[0]->old_values['value']);
        $this->assertFalse($logs[0]->new_values['value']);
        $this->assertFalse($logs[1]->old_values['value']);
        $this->assertTrue($logs[1]->new_values['value']);
    }

    public function test_unchanged_visit_setting_is_not_audited(): void
    {
        LibrarySetting::query()->create([
            'key' => 'visit_kiosk_enabled',
            'value' => '1',
            'type' => 'boolean',
            'group_name' => 'kunjungan',
            'label' => 'Kiosk kunjungan mandiri',
            'description' => 'Status kiosk.',
        ]);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.visits.update'), ['visit_kiosk_enabled' => '1'])
            ->assertRedirect(route('admin.settings.visits.edit'))
            ->assertSessionHas('info', 'Tidak ada perubahan pengaturan yang disimpan.');

        $this->assertSame(0, AuditLog::query()->where('action', 'library_setting.updated')->count());
    }
}
