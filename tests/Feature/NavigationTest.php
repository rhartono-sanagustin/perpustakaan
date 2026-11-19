<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_shows_grouped_navigation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Katalog Buku')
            ->assertSee('Sirkulasi')
            ->assertSee('Laporan')
            ->assertSee('Manajemen')
            ->assertSee('data-flux-sidebar-group', false);
    }

    public function test_borrower_sidebar_shows_loan_and_account_groups(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('borrower.dashboard'))
            ->assertOk()
            ->assertSee('Peminjaman Saya')
            ->assertSee('Akun Saya')
            ->assertSee('Katalog Buku');
    }

    public function test_public_header_offers_catalog_and_theme_switcher(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Katalog')
            ->assertSee('data-theme-switcher', false);
    }
}
