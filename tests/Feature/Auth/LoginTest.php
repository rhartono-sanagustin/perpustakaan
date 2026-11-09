<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Masuk ke Perpustakaan');
        $response->assertSee('Email atau Nomor Induk');
    }

    public function test_admin_can_login_with_email(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@sekolah.test',
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@sekolah.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_borrower_can_login_with_nomor_induk(): void
    {
        $siswa = User::factory()->siswa()->create([
            'nomor_induk' => 'NIS12345',
        ]);

        $response = $this->post('/login', [
            'login' => 'NIS12345',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($siswa);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->siswa()->inactive()->create([
            'nomor_induk' => 'NIS99999',
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'NIS99999',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->siswa()->create([
            'nomor_induk' => 'NIS11111',
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'NIS11111',
            'password' => 'kata-sandi-salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login_page(): void
    {
        $user = User::factory()->siswa()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->siswa()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_inactive_user_is_logged_out_by_active_middleware(): void
    {
        $user = User::factory()->siswa()->inactive()->create();

        $response = $this->actingAs($user)->get(route('borrower.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        User::factory()->siswa()->create([
            'nomor_induk' => 'NIS22222',
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'login' => 'NIS22222',
                'password' => 'salah',
            ])->assertSessionHasErrors('login');
        }

        $response = $this->from('/login')->post('/login', [
            'login' => 'NIS22222',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_borrower_is_redirected_to_borrower_dashboard(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('dashboard'))
            ->assertRedirect(route('borrower.dashboard'));
    }

    public function test_role_middleware_blocks_siswa_from_admin_dashboard(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_open_borrower_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('borrower.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan operasional perpustakaan');
    }

    public function test_guru_can_open_borrower_dashboard(): void
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru)
            ->get(route('borrower.dashboard'))
            ->assertOk()
            ->assertSee('Selamat datang');
    }

    public function test_user_role_enum_is_cast_correctly(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->isActive());
    }
}
