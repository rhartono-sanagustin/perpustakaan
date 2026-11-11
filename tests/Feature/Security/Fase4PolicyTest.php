<?php

namespace Tests\Feature\Security;

use App\Models\Announcement;
use App\Models\LibrarySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class Fase4PolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_view_reports(): void
    {
        $admin = User::factory()->admin()->create();
        $guru = User::factory()->guru()->create();
        $siswa = User::factory()->siswa()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('view-reports'));
        $this->assertFalse(Gate::forUser($guru)->allows('view-reports'));
        $this->assertFalse(Gate::forUser($siswa)->allows('view-reports'));
    }

    public function test_announcement_policy_is_admin_only(): void
    {
        $admin = User::factory()->admin()->create();
        $guru = User::factory()->guru()->create();
        $announcement = Announcement::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Announcement::class));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Announcement::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $announcement));
        $this->assertTrue(Gate::forUser($admin)->allows('toggle', $announcement));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $announcement));

        $this->assertFalse(Gate::forUser($guru)->allows('viewAny', Announcement::class));
        $this->assertFalse(Gate::forUser($guru)->allows('create', Announcement::class));
        $this->assertFalse(Gate::forUser($guru)->allows('update', $announcement));
        $this->assertFalse(Gate::forUser($guru)->allows('toggle', $announcement));
        $this->assertFalse(Gate::forUser($guru)->allows('delete', $announcement));
    }

    public function test_library_setting_policy_is_admin_only(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $setting = LibrarySetting::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', LibrarySetting::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $setting));

        $this->assertFalse(Gate::forUser($siswa)->allows('viewAny', LibrarySetting::class));
        $this->assertFalse(Gate::forUser($siswa)->allows('update', $setting));
    }

    public function test_admin_cannot_manage_their_own_account_via_user_policy(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->assertFalse(Gate::forUser($admin)->allows('update', $admin));
        $this->assertFalse(Gate::forUser($admin)->allows('delete', $admin));
        $this->assertFalse(Gate::forUser($admin)->allows('resetPassword', $admin));
        $this->assertFalse(Gate::forUser($admin)->allows('toggleStatus', $admin));

        $this->assertTrue(Gate::forUser($admin)->allows('update', $other));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $other));
        $this->assertTrue(Gate::forUser($admin)->allows('resetPassword', $other));
        $this->assertTrue(Gate::forUser($admin)->allows('toggleStatus', $other));
    }
}
