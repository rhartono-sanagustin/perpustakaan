<?php

namespace Tests\Feature;

use App\Models\CarouselSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingCarouselTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_only_active_slides_in_order(): void
    {
        CarouselSlide::factory()->create([
            'title' => 'Slide Kedua',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        CarouselSlide::factory()->create([
            'title' => 'Slide Pertama',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        CarouselSlide::factory()->inactive()->create([
            'title' => 'Slide Nonaktif',
            'sort_order' => 3,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('landingCarousel', false)
            ->assertSee('Sorotan perpustakaan')
            ->assertSeeInOrder(['Slide Pertama', 'Slide Kedua'])
            ->assertDontSee('Slide Nonaktif');
    }

    public function test_landing_page_hides_carousel_when_no_active_slides_exist(): void
    {
        CarouselSlide::factory()->inactive()->create(['title' => 'Slide Nonaktif']);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('landingCarousel', false)
            ->assertDontSee('Sorotan perpustakaan');
    }

    public function test_slide_text_is_escaped_on_the_landing_page(): void
    {
        CarouselSlide::factory()->create([
            'title' => '<script>alert(1)</script>',
            'caption' => '<img src=x onerror=alert(2)>',
            'alt_text' => '"><script>alert(3)</script>',
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(2)>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertSee('&lt;img src=x onerror=alert(2)&gt;', false);
    }

    public function test_external_slide_links_open_in_a_new_tab_while_internal_links_do_not(): void
    {
        CarouselSlide::factory()->create([
            'title' => 'Eksternal',
            'link_url' => 'https://contoh.sch.id/berita',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="https://contoh.sch.id/berita"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_internal_slide_links_stay_in_the_same_tab(): void
    {
        CarouselSlide::factory()->create([
            'title' => 'Internal',
            'link_url' => '/katalog',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="/katalog"', false)
            ->assertDontSee('target="_blank"', false);
    }

    public function test_single_slide_does_not_render_navigation_controls(): void
    {
        CarouselSlide::factory()->create(['title' => 'Satu-satunya', 'is_active' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Satu-satunya')
            ->assertDontSee('carousel-control-prev', false)
            ->assertDontSee('carousel-indicators', false);
    }

    public function test_authenticated_user_is_redirected_from_landing_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertRedirect(route('dashboard'));
    }
}
