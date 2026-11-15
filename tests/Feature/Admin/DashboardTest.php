<?php

namespace Tests\Feature\Admin;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_aggregates_loan_activity_for_the_last_seven_days(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        foreach ([0, 0, 3, 6, 7] as $daysAgo) {
            Loan::factory()->create([
                'user_id' => $siswa->id,
                'book_copy_id' => BookCopy::factory()->borrowed()->create(),
                'borrowed_at' => now()->subDays($daysAgo),
                'due_at' => now()->subDays($daysAgo)->addDays(7),
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Aktivitas Peminjaman')
            ->assertSee('4 peminjaman')
            ->assertViewHas('activityTotal', 4)
            ->assertViewHas('activityMax', 2);

        $activity = $response->viewData('loanActivity');
        $counts = $activity->mapWithKeys(fn (array $day): array => [$day['date']->toDateString() => $day['count']]);

        $this->assertCount(7, $activity);
        $this->assertSame(2, $counts[now()->toDateString()]);
        $this->assertSame(1, $counts[now()->subDays(3)->toDateString()]);
        $this->assertSame(1, $counts[now()->subDays(6)->toDateString()]);
        $this->assertArrayNotHasKey(now()->subDays(7)->toDateString(), $counts->all());
    }

    public function test_dashboard_shows_an_empty_state_when_there_is_no_loan_activity(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Belum ada aktivitas')
            ->assertViewHas('activityTotal', 0)
            ->assertDontSee('activity-chart', false);
    }
}
