<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\LibrarySetting;
use App\Models\User;
use App\Services\LibraryIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibraryIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_identity_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.identity.edit'))
            ->assertOk()
            ->assertSee('Identitas Sekolah')
            ->assertSee('Nama sekolah')
            ->assertSee('Logo Sekolah');
    }

    public function test_non_admin_cannot_access_identity_settings(): void
    {
        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.settings.identity.edit'))
                ->assertForbidden();

            $this->actingAs($borrower)
                ->put(route('admin.settings.identity.update'), [])
                ->assertForbidden();
        }
    }

    public function test_guest_is_redirected_from_identity_settings(): void
    {
        $this->get(route('admin.settings.identity.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_update_identity_and_changes_are_audited(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.identity.update'), [
                'school_name' => 'SMA Negeri 2',
                'library_name' => 'Perpustakaan Cendekia',
                'tagline' => 'Membaca untuk masa depan.',
            ])
            ->assertRedirect(route('admin.settings.identity.edit'))
            ->assertSessionHas('success', 'Identitas sekolah berhasil diperbarui.');

        $this->assertDatabaseHas('library_settings', [
            'key' => 'school_name',
            'value' => 'SMA Negeri 2',
            'type' => 'string',
            'group_name' => 'Identitas Sekolah',
        ]);
        $this->assertDatabaseHas('library_settings', [
            'key' => 'library_name',
            'value' => 'Perpustakaan Cendekia',
        ]);
        $this->assertDatabaseHas('library_settings', [
            'key' => 'tagline',
            'value' => 'Membaca untuk masa depan.',
        ]);

        $this->assertSame(3, AuditLog::query()->where('action', 'library_identity.updated')->count());

        $log = AuditLog::query()
            ->where('action', 'library_identity.updated')
            ->get()
            ->first(fn (AuditLog $log): bool => ($log->new_values['key'] ?? null) === 'school_name');

        $this->assertNotNull($log);
        $this->assertSame('SMA Negeri 2', $log->new_values['value']);
    }

    public function test_identity_is_rendered_on_login_page(): void
    {
        $this->createSetting('school_name', 'SMA Negeri 2');
        $this->createSetting('library_name', 'Perpustakaan Cendekia');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('SMA Negeri 2')
            ->assertSee('Perpustakaan Cendekia');
    }

    public function test_identity_is_rendered_on_admin_sidebar(): void
    {
        $this->createSetting('school_name', 'SMA Negeri 2');
        $this->createSetting('library_name', 'Perpustakaan Cendekia');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('SMA Negeri 2')
            ->assertSee('Perpustakaan Cendekia');
    }

    public function test_admin_can_upload_and_replace_logo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.identity.update'), [
                'school_name' => 'SMA Negeri 1',
                'library_name' => 'Perpustakaan Sekolah',
                'tagline' => 'Tagline sekolah.',
                'logo' => UploadedFile::fake()->image('logo.png', 512, 512),
            ])
            ->assertRedirect(route('admin.settings.identity.edit'));

        $oldPath = LibrarySetting::query()->where('key', 'logo_path')->value('value');

        $this->assertNotNull($oldPath);
        Storage::disk('public')->assertExists($oldPath);

        $this->actingAs($admin)
            ->put(route('admin.settings.identity.update'), [
                'school_name' => 'SMA Negeri 1',
                'library_name' => 'Perpustakaan Sekolah',
                'tagline' => 'Tagline sekolah.',
                'logo' => UploadedFile::fake()->image('logo-baru.png', 512, 512),
            ])
            ->assertRedirect(route('admin.settings.identity.edit'));

        $newPath = LibrarySetting::query()->where('key', 'logo_path')->value('value');

        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_admin_can_remove_logo_and_revert_to_default(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $path = UploadedFile::fake()->image('logo.png', 512, 512)->store('branding', 'public');

        $this->createSetting('logo_path', $path);

        $this->actingAs($admin)
            ->put(route('admin.settings.identity.update'), [
                'school_name' => config('library.school_name'),
                'library_name' => config('library.name'),
                'tagline' => config('library.tagline'),
                'remove_logo' => '1',
            ])
            ->assertRedirect(route('admin.settings.identity.edit'));

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseHas('library_settings', [
            'key' => 'logo_path',
            'value' => null,
        ]);
        $this->assertFalse(app(LibraryIdentity::class)->hasCustomLogo());
    }

    public function test_identity_validation_rejects_missing_names_and_svg_logo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.identity.update'), [
                'school_name' => '',
                'library_name' => '',
                'logo' => UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'),
            ])
            ->assertSessionHasErrors(['school_name', 'library_name', 'logo']);
    }

    public function test_unchanged_identity_is_not_audited(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.identity.update'), [
                'school_name' => config('library.school_name'),
                'library_name' => config('library.name'),
                'tagline' => config('library.tagline'),
            ])
            ->assertRedirect(route('admin.settings.identity.edit'))
            ->assertSessionHas('success', 'Tidak ada perubahan identitas yang disimpan.');

        $this->assertSame(0, AuditLog::query()->where('action', 'library_identity.updated')->count());
    }

    private function createSetting(string $key, ?string $value): LibrarySetting
    {
        return LibrarySetting::query()->create([
            'key' => $key,
            'value' => $value,
            'type' => 'string',
            'group_name' => 'Identitas Sekolah',
            'label' => $key,
            'description' => null,
        ]);
    }
}
