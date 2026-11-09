<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_user_factory_states_create_expected_roles_and_statuses(): void
    {
        $this->assertSame(UserRole::Admin, User::factory()->admin()->create()->role);
        $this->assertSame(UserRole::Guru, User::factory()->guru()->create()->role);
        $this->assertSame(UserRole::Siswa, User::factory()->siswa()->create()->role);
        $this->assertSame(UserStatus::Nonaktif, User::factory()->inactive()->create()->status);
    }
}
