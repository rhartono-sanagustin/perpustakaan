<?php

namespace Tests\Feature\Admin;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_an_announcement(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Perpanjangan Jam Layanan',
            'body' => "Perpustakaan buka sampai pukul 16.00.\nHarap menjaga ketenangan.",
            'audience' => 'semua',
            'is_active' => '1',
            'published_at' => '',
        ]);

        $response->assertRedirect(route('admin.announcements.index'))
            ->assertSessionHas('success');

        $announcement = Announcement::query()->where('title', 'Perpanjangan Jam Layanan')->firstOrFail();

        $this->assertSame($admin->id, $announcement->created_by);
        $this->assertTrue($announcement->is_active);
        $this->assertNotNull($announcement->published_at);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'announcement.created',
            'entity_id' => $announcement->id,
        ]);
    }

    public function test_announcement_validation_requires_title_body_and_audience(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), [])
            ->assertSessionHasErrors(['title', 'body', 'audience']);
    }

    public function test_admin_can_update_an_announcement_and_the_change_is_audited(): void
    {
        $admin = User::factory()->admin()->create();
        $announcement = Announcement::factory()->create([
            'title' => 'Judul Lama',
            'audience' => AnnouncementAudience::Semua,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->put(route('admin.announcements.update', $announcement), [
            'title' => 'Judul Baru',
            'body' => 'Isi pengumuman yang diperbarui.',
            'audience' => 'guru',
            'is_active' => '0',
            'published_at' => '',
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement->refresh();

        $this->assertSame('Judul Baru', $announcement->title);
        $this->assertSame(AnnouncementAudience::Guru, $announcement->audience);
        $this->assertFalse($announcement->is_active);

        $log = AuditLog::query()
            ->where('action', 'announcement.updated')
            ->where('entity_id', $announcement->id)
            ->firstOrFail();

        $this->assertSame('Judul Lama', $log->old_values['title']);
        $this->assertSame('Judul Baru', $log->new_values['title']);
    }

    public function test_admin_can_toggle_an_announcement(): void
    {
        $admin = User::factory()->admin()->create();
        $announcement = Announcement::factory()->create([
            'is_active' => true,
            'published_at' => now()->subDay(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.announcements.toggle', $announcement))
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertFalse($announcement->fresh()->is_active);

        $this->actingAs($admin)
            ->post(route('admin.announcements.toggle', $announcement));

        $this->assertTrue($announcement->fresh()->is_active);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'announcement.toggled',
            'entity_id' => $announcement->id,
        ]);
    }

    public function test_admin_can_delete_an_announcement(): void
    {
        $admin = User::factory()->admin()->create();
        $announcement = Announcement::factory()->create(['created_by' => $admin->id]);

        $this->actingAs($admin)
            ->delete(route('admin.announcements.destroy', $announcement))
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'announcement.deleted',
            'entity_id' => $announcement->id,
        ]);
    }

    public function test_borrowers_cannot_manage_announcements(): void
    {
        $announcement = Announcement::factory()->create();

        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.announcements.index'))
                ->assertForbidden();

            $this->actingAs($borrower)
                ->post(route('admin.announcements.toggle', $announcement))
                ->assertForbidden();
        }
    }

    public function test_borrower_dashboard_shows_only_active_visible_announcements(): void
    {
        $siswa = User::factory()->siswa()->create();

        Announcement::factory()->create([
            'title' => 'Info Umum Sekolah',
            'audience' => AnnouncementAudience::Semua,
            'is_active' => true,
            'published_at' => now()->subMinutes(5),
        ]);
        Announcement::factory()->create([
            'title' => 'Info Untuk Siswa',
            'audience' => AnnouncementAudience::Siswa,
            'is_active' => true,
            'published_at' => now()->subMinutes(4),
        ]);
        Announcement::factory()->create([
            'title' => 'Info Untuk Guru',
            'audience' => AnnouncementAudience::Guru,
            'is_active' => true,
            'published_at' => now()->subMinutes(3),
        ]);
        Announcement::factory()->draft()->create(['title' => 'Draf Belum Terbit']);
        Announcement::factory()->create([
            'title' => 'Terbit Besok',
            'audience' => AnnouncementAudience::Semua,
            'is_active' => true,
            'published_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($siswa)->get(route('borrower.dashboard'));

        $response->assertOk()
            ->assertSee('Info Umum Sekolah')
            ->assertSee('Info Untuk Siswa')
            ->assertDontSee('Info Untuk Guru')
            ->assertDontSee('Draf Belum Terbit')
            ->assertDontSee('Terbit Besok');
    }

    public function test_borrower_dashboard_escapes_announcement_html(): void
    {
        $siswa = User::factory()->siswa()->create();

        Announcement::factory()->create([
            'title' => 'Pengumuman Aman',
            'body' => "<script>alert('xss')</script>\nBaris kedua tetap tampil.",
            'audience' => AnnouncementAudience::Semua,
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($siswa)->get(route('borrower.dashboard'));

        $response->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee("<script>alert('xss')</script>", false)
            ->assertSee('Baris kedua tetap tampil.')
            ->assertSee('announcement-body', false);
    }
}
