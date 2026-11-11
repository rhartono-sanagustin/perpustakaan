<?php

namespace Tests\Feature\Circulation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BorrowerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_can_update_contact_details(): void
    {
        $siswa = User::factory()->siswa()->create([
            'email' => 'lama@sekolah.test',
            'no_telepon' => '081200000001',
        ]);

        $response = $this->actingAs($siswa)->put(route('borrower.profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@sekolah.test',
            'no_telepon' => '081200000002',
        ]);

        $response->assertRedirect(route('borrower.profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $siswa->id,
            'name' => 'Nama Baru',
            'email' => 'baru@sekolah.test',
            'no_telepon' => '081200000002',
        ]);
    }

    public function test_borrower_can_change_password_with_current_password(): void
    {
        $siswa = User::factory()->siswa()->create();

        $response = $this->actingAs($siswa)->put(route('borrower.profile.update'), [
            'name' => $siswa->name,
            'email' => $siswa->email,
            'current_password' => 'password',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ]);

        $response->assertRedirect(route('borrower.profile.edit'));
        $this->assertTrue(Hash::check('kata-sandi-baru', $siswa->refresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $siswa = User::factory()->siswa()->create();
        $originalHash = $siswa->password;

        $response = $this->actingAs($siswa)->put(route('borrower.profile.update'), [
            'name' => $siswa->name,
            'email' => $siswa->email,
            'current_password' => 'kata-sandi-salah',
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertSame($originalHash, $siswa->refresh()->password);
    }
}
