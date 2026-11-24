<?php

namespace Tests\Feature;

use App\Enums\VisitSource;
use App\Models\AuditLog;
use App\Models\LibrarySetting;
use App\Models\LibraryVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitKioskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_public_visit_kiosk(): void
    {
        $response = $this->get(route('visits.kiosk'));

        $response->assertOk()
            ->assertSee('Selamat Datang')
            ->assertSee('Scan kartu anggota')
            ->assertSee('Catat Kunjungan')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_authenticated_member_can_still_view_the_public_visit_kiosk(): void
    {
        $student = User::factory()->siswa()->create();

        $this->actingAs($student)
            ->get(route('visits.kiosk'))
            ->assertOk()
            ->assertSee('Selamat Datang');
    }

    public function test_guest_can_record_a_visit_through_the_kiosk(): void
    {
        $student = User::factory()->siswa()->create([
            'name' => 'Siti Aminah',
            'nomor_induk' => 'NIS12345',
        ]);

        $this->post(route('visits.kiosk.store'), ['nomor_induk' => 'NIS12345'])
            ->assertRedirect(route('visits.kiosk'))
            ->assertSessionHas('visit_result', fn (array $result): bool => $result['status'] === 'recorded'
                && $result['name'] === 'Siti Aminah'
                && $result['nomor_induk'] === 'NIS12345');

        $visit = LibraryVisit::query()->firstOrFail();

        $this->assertSame($student->id, $visit->user_id);
        $this->assertNull($visit->recorded_by);
        $this->assertSame(VisitSource::Mandiri, $visit->source);
        $this->assertTrue($visit->visit_date->isToday());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'visit.recorded',
            'entity_type' => $visit->getMorphClass(),
            'entity_id' => $visit->id,
            'actor_id' => null,
        ]);

        $log = AuditLog::query()->where('action', 'visit.recorded')->firstOrFail();
        $this->assertSame('mandiri', $log->new_values['source']);
    }

    public function test_kiosk_shows_the_confirmation_panel_after_recording(): void
    {
        User::factory()->siswa()->create([
            'name' => 'Siti Aminah',
            'nomor_induk' => 'NIS12345',
        ]);

        $this->post(route('visits.kiosk.store'), ['nomor_induk' => 'NIS12345'])
            ->assertRedirect(route('visits.kiosk'));

        $this->get(route('visits.kiosk'))
            ->assertOk()
            ->assertSee('Kunjungan Tercatat')
            ->assertSee('Siti Aminah')
            ->assertSee('NIS12345')
            ->assertSee('Selamat membaca');
    }

    public function test_duplicate_scan_within_thirty_minutes_is_not_recorded_at_the_kiosk(): void
    {
        $student = User::factory()->siswa()->create([
            'name' => 'Siti Aminah',
            'nomor_induk' => 'NIS12345',
        ]);

        LibraryVisit::factory()->for($student)->mandiri()->create([
            'visit_date' => now()->toDateString(),
            'visited_at' => now()->subMinutes(10),
        ]);

        $this->post(route('visits.kiosk.store'), ['nomor_induk' => 'NIS12345'])
            ->assertRedirect(route('visits.kiosk'))
            ->assertSessionHas('visit_result', fn (array $result): bool => $result['status'] === 'duplicate'
                && $result['name'] === 'Siti Aminah');

        $this->assertSame(1, LibraryVisit::query()->count());
    }

    public function test_kiosk_rejects_unknown_inactive_and_admin_codes(): void
    {
        $inactive = User::factory()->siswa()->inactive()->create(['nomor_induk' => 'NIS99999']);
        $admin = User::factory()->admin()->create();

        $this->post(route('visits.kiosk.store'), ['nomor_induk' => 'NIS99999'])
            ->assertSessionHas('visit_result', fn (array $result): bool => $result['status'] === 'inactive'
                && $result['name'] === $inactive->name);

        $this->get(route('visits.kiosk'))
            ->assertOk()
            ->assertSee('Akun Nonaktif');

        $this->post(route('visits.kiosk.store'), ['nomor_induk' => $admin->nomor_induk])
            ->assertSessionHas('visit_result', fn (array $result): bool => $result['status'] === 'not_borrower');

        $this->get(route('visits.kiosk'))
            ->assertOk()
            ->assertSee('Bukan Kartu Anggota');

        $this->post(route('visits.kiosk.store'), ['nomor_induk' => 'TIDAK-ADA'])
            ->assertSessionHas('visit_result', fn (array $result): bool => $result['status'] === 'not_found');

        $this->get(route('visits.kiosk'))
            ->assertOk()
            ->assertSee('Kartu Tidak Dikenali');

        $this->assertSame(0, LibraryVisit::query()->count());
    }

    public function test_kiosk_requires_nomor_induk(): void
    {
        $this->post(route('visits.kiosk.store'), ['nomor_induk' => ''])
            ->assertSessionHasErrors('nomor_induk');

        $this->assertSame(0, LibraryVisit::query()->count());
    }

    public function test_kiosk_returns_not_found_and_landing_link_is_hidden_when_disabled(): void
    {
        LibrarySetting::query()->create([
            'key' => 'visit_kiosk_enabled',
            'value' => '0',
            'type' => 'boolean',
            'group_name' => 'kunjungan',
            'label' => 'Kiosk kunjungan mandiri',
            'description' => 'Status kiosk.',
        ]);

        $this->get(route('visits.kiosk'))->assertNotFound();
        $this->post(route('visits.kiosk.store'), ['nomor_induk' => 'NIS12345'])->assertNotFound();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Buka Kiosk Kunjungan')
            ->assertDontSee(route('visits.kiosk'));
    }

    public function test_landing_page_shows_the_kiosk_link_when_enabled(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Buka Kiosk Kunjungan')
            ->assertSee(route('visits.kiosk'));
    }

    public function test_kiosk_is_rate_limited(): void
    {
        for ($attempt = 1; $attempt <= 60; $attempt++) {
            $this->get(route('visits.kiosk'))->assertOk();
        }

        $this->get(route('visits.kiosk'))->assertStatus(429);
    }
}
