<?php

namespace Tests\Feature\Admin;

use App\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_user_with_hashed_password(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Guru Baru',
            'email' => 'guru.baru@example.test',
            'nomor_induk' => 'NIP999001',
            'role' => 'guru',
            'status' => 'aktif',
            'kelas' => '',
            'unit_kerja' => 'Sejarah',
            'no_telepon' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $user = User::query()->where('nomor_induk', 'NIP999001')->firstOrFail();

        $this->assertSame('Guru Baru', $user->name);
        $this->assertSame('guru', $user->role->value);
        $this->assertSame(UserStatus::Aktif, $user->status);
        $this->assertTrue(Hash::check('rahasia123', $user->password));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.created',
            'entity_type' => User::class,
            'entity_id' => $user->id,
        ]);
    }

    public function test_user_validation_rejects_duplicate_nomor_induk_and_mismatched_password(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->siswa()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Siswa Duplikat',
            'nomor_induk' => $existing->nomor_induk,
            'role' => 'siswa',
            'status' => 'aktif',
            'password' => 'rahasia123',
            'password_confirmation' => 'beda12345',
        ])->assertSessionHasErrors(['nomor_induk', 'password']);
    }

    public function test_admin_can_update_user_data_and_the_change_is_audited(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => 'Nama Baru',
            'email' => $target->email,
            'nomor_induk' => $target->nomor_induk,
            'role' => 'siswa',
            'status' => 'aktif',
            'kelas' => 'XII IPA 2',
            'unit_kerja' => '',
            'no_telepon' => $target->no_telepon,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $target->refresh();
        $this->assertSame('Nama Baru', $target->name);
        $this->assertSame('XII IPA 2', $target->kelas);

        $log = AuditLog::query()
            ->where('action', 'user.updated')
            ->where('entity_id', $target->id)
            ->firstOrFail();

        $this->assertSame('Nama Lama', $log->old_values['name']);
        $this->assertSame('Nama Baru', $log->new_values['name']);
    }

    public function test_admin_can_reset_password_without_exposing_it_in_the_interface(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.reset-password', $target), [
            'password' => 'sandiBaru123',
            'password_confirmation' => 'sandiBaru123',
        ]);

        $response->assertRedirect(route('admin.users.edit', $target))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('sandiBaru123', $target->fresh()->password));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.password_reset',
            'entity_id' => $target->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $target))
            ->assertOk()
            ->assertDontSee('sandiBaru123');
    }

    public function test_reset_password_requires_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        $this->actingAs($admin)->post(route('admin.users.reset-password', $target), [
            'password' => 'sandiBaru123',
            'password_confirmation' => 'berbeda123',
        ])->assertSessionHasErrors('password');
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-status', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(UserStatus::Nonaktif, $target->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.status_toggled',
            'entity_id' => $target->id,
        ]);
    }

    public function test_admin_can_delete_a_user_without_history(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.deleted',
            'entity_id' => $target->id,
        ]);
    }

    public function test_user_with_loan_history_is_deactivated_instead_of_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();
        Loan::factory()->create([
            'user_id' => $target->id,
            'processed_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $target->id]);
        $this->assertSame(UserStatus::Nonaktif, $target->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.deactivated',
            'entity_id' => $target->id,
        ]);
    }

    public function test_user_with_audit_trail_is_deactivated_instead_of_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        AuditLog::factory()->create([
            'actor_id' => $target->id,
            'entity_type' => 'user',
            'entity_id' => $target->id,
        ]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $target));

        $this->assertDatabaseHas('users', ['id' => $target->id]);
        $this->assertSame(UserStatus::Nonaktif, $target->fresh()->status);
    }

    public function test_admin_cannot_manage_their_own_account_through_user_management(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $admin))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-status', $admin))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $admin), [
                'password' => 'sandiBaru123',
                'password_confirmation' => 'sandiBaru123',
            ])
            ->assertForbidden();
    }

    public function test_borrowers_cannot_access_user_management(): void
    {
        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.users.index'))
                ->assertForbidden();
        }
    }
}
