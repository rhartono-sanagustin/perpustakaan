<?php

namespace Tests\Feature\Admin;

use App\Enums\VisitSource;
use App\Models\LibraryVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_visit_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.visits.index'))
            ->assertOk()
            ->assertSee('Kunjungan Perpustakaan')
            ->assertSee('Scan Kartu Anggota')
            ->assertSee('Kunjungan Hari Ini');
    }

    public function test_admin_can_record_a_visit_by_scanning_nomor_induk(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create(['nomor_induk' => 'NIS12345']);

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), ['nomor_induk' => 'NIS12345'])
            ->assertRedirect(route('admin.visits.index', ['anggota' => $student->id]))
            ->assertSessionHas('success');

        $visit = LibraryVisit::query()->firstOrFail();

        $this->assertSame($student->id, $visit->user_id);
        $this->assertSame($admin->id, $visit->recorded_by);
        $this->assertSame(VisitSource::Admin, $visit->source);
        $this->assertTrue($visit->visit_date->isToday());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'visit.recorded',
            'entity_type' => $visit->getMorphClass(),
            'entity_id' => $visit->id,
            'actor_id' => $admin->id,
        ]);
    }

    public function test_duplicate_scan_within_thirty_minutes_is_not_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create(['nomor_induk' => 'NIS12345']);

        LibraryVisit::factory()->for($student)->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now()->subMinutes(10),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), ['nomor_induk' => 'NIS12345'])
            ->assertRedirect(route('admin.visits.index', ['anggota' => $student->id]))
            ->assertSessionHas('info');

        $this->assertSame(1, LibraryVisit::query()->count());
    }

    public function test_scan_after_thirty_minutes_creates_a_new_visit(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create(['nomor_induk' => 'NIS12345']);

        LibraryVisit::factory()->for($student)->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now()->subMinutes(31),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), ['nomor_induk' => 'NIS12345'])
            ->assertSessionHas('success');

        $this->assertSame(2, LibraryVisit::query()->count());
    }

    public function test_inactive_admin_and_unknown_members_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $inactive = User::factory()->siswa()->inactive()->create(['nomor_induk' => 'NIS99999']);

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), ['nomor_induk' => 'NIS99999'])
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), ['nomor_induk' => $admin->nomor_induk])
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), ['nomor_induk' => 'TIDAK-ADA'])
            ->assertSessionHas('error');

        $this->assertSame(0, LibraryVisit::query()->count());
        $this->assertTrue($inactive->isActive() === false);
    }

    public function test_non_admin_cannot_manage_visits(): void
    {
        $student = User::factory()->siswa()->create(['nomor_induk' => 'NIS12345']);

        $this->actingAs($student)
            ->get(route('admin.visits.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('admin.visits.store'), ['nomor_induk' => 'NIS12345'])
            ->assertForbidden();
    }

    public function test_admin_can_cancel_todays_visit_only(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create();

        $today = LibraryVisit::factory()->for($student)->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.visits.destroy', $today))
            ->assertRedirect(route('admin.visits.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($today);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'visit.deleted',
            'entity_id' => $today->id,
            'actor_id' => $admin->id,
        ]);

        $old = LibraryVisit::factory()->for($student)->create([
            'visit_date' => now()->subDay()->toDateString(),
            'visited_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.visits.destroy', $old))
            ->assertRedirect(route('admin.visits.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($old);
    }

    public function test_dashboard_shows_the_visit_chart(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create();

        LibraryVisit::factory()->for($student)->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kunjungan Perpustakaan')
            ->assertSee('1 kunjungan')
            ->assertSee('hari ini 1');
    }

    public function test_visit_page_shows_the_scanned_member_summary(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create(['name' => 'Siti Aminah']);

        $this->actingAs($admin)
            ->get(route('admin.visits.index', ['anggota' => $student->id]))
            ->assertOk()
            ->assertSee('Anggota yang Baru Dipindai')
            ->assertSee('Siti Aminah')
            ->assertSee('Pinjaman aktif');
    }

    public function test_admin_can_filter_visits_by_source(): void
    {
        $admin = User::factory()->admin()->create();
        $adminScanned = User::factory()->siswa()->create(['name' => 'Anggota Scan Admin']);
        $selfService = User::factory()->siswa()->create(['name' => 'Anggota Scan Mandiri']);

        LibraryVisit::factory()->for($adminScanned)->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now()->subMinutes(2),
        ]);

        LibraryVisit::factory()->for($selfService)->mandiri()->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now()->subMinute(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.visits.index', ['sumber' => 'mandiri']))
            ->assertOk()
            ->assertSee('Anggota Scan Mandiri')
            ->assertDontSee('Anggota Scan Admin')
            ->assertSee('text-bg-info', false);
    }
}
