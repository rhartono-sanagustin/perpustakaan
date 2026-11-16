<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_print_a_student_member_card(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->siswa()->create([
            'name' => 'Budi Santoso',
            'nomor_induk' => 'NIS12345',
            'kelas' => 'XI IPA 1',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.card', $student))
            ->assertOk()
            ->assertSee('Kartu Anggota Perpustakaan')
            ->assertSee('Budi Santoso')
            ->assertSee('NIS12345')
            ->assertSee('XI IPA 1')
            ->assertSee('member-card-barcode', false);
    }

    public function test_teacher_card_shows_unit_kerja(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->guru()->create([
            'name' => 'Ibu Sari',
            'nomor_induk' => 'NIP99887',
            'unit_kerja' => 'Matematika',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.card', $teacher))
            ->assertOk()
            ->assertSee('Ibu Sari')
            ->assertSee('NIP99887')
            ->assertSee('Matematika')
            ->assertSee('Unit Kerja');
    }

    public function test_card_is_only_available_for_active_borrowers(): void
    {
        $admin = User::factory()->admin()->create();
        $inactive = User::factory()->siswa()->inactive()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.card', $inactive))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->get(route('admin.users.card', $otherAdmin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');
    }

    public function test_non_admin_cannot_access_member_cards(): void
    {
        $student = User::factory()->siswa()->create();

        $this->actingAs($student)
            ->get(route('admin.users.card', $student))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.member-cards.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.member-cards.print'))
            ->assertForbidden();
    }

    public function test_batch_print_only_includes_active_borrowers_matching_the_filter(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->siswa()->create(['name' => 'Siswa Aktif', 'kelas' => 'X IPA 1']);
        User::factory()->siswa()->inactive()->create(['name' => 'Siswa Nonaktif']);
        User::factory()->guru()->create(['name' => 'Guru Aktif']);
        User::factory()->admin()->create(['name' => 'Admin Lain']);

        $this->actingAs($admin)
            ->get(route('admin.member-cards.print', ['role' => 'siswa']))
            ->assertOk()
            ->assertSee('Siswa Aktif')
            ->assertDontSee('Siswa Nonaktif')
            ->assertDontSee('Guru Aktif')
            ->assertDontSee('Admin Lain');
    }

    public function test_batch_print_redirects_when_no_member_matches(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.member-cards.print', ['role' => 'guru']))
            ->assertRedirect(route('admin.member-cards.index', ['role' => 'guru']))
            ->assertSessionHas('error');
    }

    public function test_member_card_index_lists_and_filters_borrowers(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->siswa()->create(['name' => 'Siswa Satu', 'kelas' => 'X IPA 1']);
        User::factory()->guru()->create(['name' => 'Guru Satu', 'unit_kerja' => 'Matematika']);

        $this->actingAs($admin)
            ->get(route('admin.member-cards.index'))
            ->assertOk()
            ->assertSee('Siswa Satu')
            ->assertSee('Guru Satu')
            ->assertSee('Cetak Massal');

        $this->actingAs($admin)
            ->get(route('admin.member-cards.index', ['role' => 'guru']))
            ->assertOk()
            ->assertSee('Guru Satu')
            ->assertDontSee('Siswa Satu');
    }
}
