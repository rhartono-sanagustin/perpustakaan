<?php

namespace Tests\Feature\Admin;

use App\Models\CarouselSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarouselSlideTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_carousel_management_page(): void
    {
        $admin = User::factory()->admin()->create();
        CarouselSlide::factory()->create(['title' => 'Selamat Datang']);

        $this->actingAs($admin)
            ->get(route('admin.settings.carousel.index'))
            ->assertOk()
            ->assertSee('Carousel Beranda')
            ->assertSee('Selamat Datang')
            ->assertSee('Tambah Slide');
    }

    public function test_admin_can_view_empty_carousel_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.carousel.index'))
            ->assertOk()
            ->assertSee('Belum ada slide carousel');
    }

    public function test_guest_and_non_admin_cannot_manage_carousel(): void
    {
        $this->get(route('admin.settings.carousel.index'))
            ->assertRedirect(route('login'));

        foreach ([User::factory()->guru()->create(), User::factory()->siswa()->create()] as $borrower) {
            $this->actingAs($borrower)
                ->get(route('admin.settings.carousel.index'))
                ->assertForbidden();

            $this->actingAs($borrower)
                ->post(route('admin.settings.carousel.store'), [])
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_slide_with_image_and_audit_it(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.settings.carousel.store'), [
            'title' => 'Koleksi Terbaru',
            'caption' => 'Temukan buku baru di rak depan.',
            'alt_text' => 'Rak buku perpustakaan',
            'link_url' => '/katalog',
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('slide.jpg', 1600, 900),
        ]);

        $response->assertRedirect(route('admin.settings.carousel.index'))
            ->assertSessionHas('success', 'Slide carousel berhasil ditambahkan.');

        $slide = CarouselSlide::query()->firstOrFail();

        $this->assertSame('Koleksi Terbaru', $slide->title);
        $this->assertSame('/katalog', $slide->link_url);
        $this->assertTrue($slide->is_active);
        $this->assertSame(1, $slide->sort_order);
        Storage::disk('public')->assertExists($slide->image_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'carousel_slide.created',
            'entity_type' => $slide->getMorphClass(),
            'entity_id' => $slide->id,
            'actor_id' => $admin->id,
        ]);
    }

    public function test_slide_creation_validates_image_and_link(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.store'), [
                'link_url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors(['image', 'link_url']);

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.store'), [
                'image' => UploadedFile::fake()->create('slide.svg', 10, 'image/svg+xml'),
                'link_url' => 'https://contoh.sch.id',
            ])
            ->assertSessionHasErrors(['image']);

        $this->assertSame(0, CarouselSlide::query()->count());
    }

    public function test_slide_creation_is_rejected_when_active_limit_is_reached(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $max = (int) config('library.carousel.max_active', 5);

        CarouselSlide::factory()->count($max)->create(['is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.store'), [
                'title' => 'Slide Keenam',
                'is_active' => '1',
                'image' => UploadedFile::fake()->image('slide.jpg', 1600, 900),
            ])
            ->assertSessionHasErrors('is_active');

        $this->assertSame($max, CarouselSlide::query()->count());
    }

    public function test_admin_can_update_slide_and_replace_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $oldPath = UploadedFile::fake()->image('lama.jpg', 1600, 900)->store('carousel', 'public');
        $slide = CarouselSlide::factory()->create([
            'image_path' => $oldPath,
            'title' => 'Judul Lama',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.carousel.update', $slide), [
                'title' => 'Judul Baru',
                'caption' => 'Keterangan baru.',
                'link_url' => 'https://contoh.sch.id/berita',
                'is_active' => '1',
                'image' => UploadedFile::fake()->image('baru.jpg', 1600, 900),
            ])
            ->assertRedirect(route('admin.settings.carousel.index'))
            ->assertSessionHas('success', 'Slide carousel berhasil diperbarui.');

        $slide->refresh();

        $this->assertSame('Judul Baru', $slide->title);
        $this->assertSame('https://contoh.sch.id/berita', $slide->link_url);
        $this->assertNotSame($oldPath, $slide->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($slide->image_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'carousel_slide.updated',
            'entity_id' => $slide->id,
        ]);
    }

    public function test_updating_without_image_keeps_existing_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $path = UploadedFile::fake()->image('slide.jpg', 1600, 900)->store('carousel', 'public');
        $slide = CarouselSlide::factory()->create(['image_path' => $path]);

        $this->actingAs($admin)
            ->put(route('admin.settings.carousel.update', $slide), [
                'title' => 'Judul Saja',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.settings.carousel.index'));

        $slide->refresh();

        $this->assertSame($path, $slide->image_path);
        $this->assertSame('Judul Saja', $slide->title);
        $this->assertFalse($slide->is_active);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_toggle_slide_visibility(): void
    {
        $admin = User::factory()->admin()->create();
        $slide = CarouselSlide::factory()->inactive()->create();

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.toggle', $slide))
            ->assertSessionHas('success', 'Slide diaktifkan.');

        $this->assertTrue($slide->refresh()->is_active);

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.toggle', $slide))
            ->assertSessionHas('success', 'Slide dinonaktifkan.');

        $this->assertFalse($slide->refresh()->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'carousel_slide.activated',
            'entity_id' => $slide->id,
        ]);
    }

    public function test_toggle_is_rejected_when_active_limit_is_reached(): void
    {
        $admin = User::factory()->admin()->create();
        $max = (int) config('library.carousel.max_active', 5);

        CarouselSlide::factory()->count($max)->create(['is_active' => true]);
        $inactive = CarouselSlide::factory()->inactive()->create();

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.toggle', $inactive))
            ->assertSessionHasErrors('is_active');

        $this->assertFalse($inactive->refresh()->is_active);
    }

    public function test_admin_can_reorder_slides(): void
    {
        $admin = User::factory()->admin()->create();

        $first = CarouselSlide::factory()->create(['title' => 'Pertama', 'sort_order' => 1]);
        $second = CarouselSlide::factory()->create(['title' => 'Kedua', 'sort_order' => 2]);
        $third = CarouselSlide::factory()->create(['title' => 'Ketiga', 'sort_order' => 3]);

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.move', $third), ['direction' => 'up'])
            ->assertSessionHas('success', 'Urutan slide diperbarui.');

        $this->assertSame(1, $first->refresh()->sort_order);
        $this->assertSame(3, $second->refresh()->sort_order);
        $this->assertSame(2, $third->refresh()->sort_order);

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.move', $third), ['direction' => 'up'])
            ->assertSessionHas('success', 'Urutan slide diperbarui.');

        $this->assertSame(1, $third->refresh()->sort_order);
        $this->assertSame(2, $first->refresh()->sort_order);
        $this->assertSame(3, $second->refresh()->sort_order);

        $this->actingAs($admin)
            ->post(route('admin.settings.carousel.move', $third), ['direction' => 'up'])
            ->assertSessionHas('info', 'Posisi slide sudah berada di batas urutan.');

        $this->assertSame(1, $third->refresh()->sort_order);
    }

    public function test_admin_can_delete_slide_and_its_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $path = UploadedFile::fake()->image('slide.jpg', 1600, 900)->store('carousel', 'public');
        $slide = CarouselSlide::factory()->create(['image_path' => $path]);

        $this->actingAs($admin)
            ->delete(route('admin.settings.carousel.destroy', $slide))
            ->assertRedirect(route('admin.settings.carousel.index'))
            ->assertSessionHas('success', 'Slide carousel berhasil dihapus.');

        $this->assertDatabaseMissing('carousel_slides', ['id' => $slide->id]);
        Storage::disk('public')->assertMissing($path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'carousel_slide.deleted',
            'entity_id' => $slide->id,
        ]);
    }

    public function test_edit_form_is_prefilled(): void
    {
        $admin = User::factory()->admin()->create();
        $slide = CarouselSlide::factory()->create([
            'title' => 'Judul Slide',
            'caption' => 'Keterangan slide',
            'alt_text' => 'Teks alternatif',
            'link_url' => '/katalog',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.carousel.edit', $slide))
            ->assertOk()
            ->assertSee('Edit Slide Carousel')
            ->assertSee('Judul Slide')
            ->assertSee('Keterangan slide')
            ->assertSee('Teks alternatif');
    }
}
