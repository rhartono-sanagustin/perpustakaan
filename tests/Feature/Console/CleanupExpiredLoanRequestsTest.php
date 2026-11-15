<?php

namespace Tests\Feature\Console;

use App\Enums\LoanRequestStatus;
use App\Models\AuditLog;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CleanupExpiredLoanRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-11 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dry_run_counts_expired_requests_without_changing_data(): void
    {
        $admin = User::factory()->admin()->create();

        $expired = LoanRequest::factory()->approved()->create([
            'processed_by' => $admin->id,
            'expires_at' => now()->subHour(),
        ]);
        LoanRequest::factory()->approved()->create([
            'processed_by' => $admin->id,
            'expires_at' => now()->addHour(),
        ]);

        $this->artisan('library:cleanup', ['--dry-run' => true])
            ->expectsOutputToContain('1 pengajuan kedaluwarsa akan dibatalkan')
            ->assertSuccessful();

        $this->assertSame(LoanRequestStatus::Disetujui, $expired->fresh()->status);
        $this->assertNull($expired->fresh()->cancelled_at);
        $this->assertDatabaseMissing('audit_logs', ['action' => 'loan_request.expired']);
    }

    public function test_expired_approved_requests_are_cancelled_and_audited(): void
    {
        $admin = User::factory()->admin()->create();

        $first = LoanRequest::factory()->approved()->create([
            'processed_by' => $admin->id,
            'expires_at' => now()->subDay(),
        ]);
        $second = LoanRequest::factory()->approved()->create([
            'processed_by' => $admin->id,
            'expires_at' => now()->subHour(),
        ]);
        $future = LoanRequest::factory()->approved()->create([
            'processed_by' => $admin->id,
            'expires_at' => now()->addDay(),
        ]);
        $pending = LoanRequest::factory()->create();

        $this->artisan('library:cleanup')
            ->expectsOutputToContain('2 pengajuan kedaluwarsa berhasil dibatalkan')
            ->assertSuccessful();

        foreach ([$first, $second] as $request) {
            $fresh = $request->fresh();

            $this->assertSame(LoanRequestStatus::Dibatalkan, $fresh->status);
            $this->assertNotNull($fresh->cancelled_at);

            $log = AuditLog::query()
                ->where('action', 'loan_request.expired')
                ->where('entity_id', $request->id)
                ->firstOrFail();

            $this->assertSame('dibatalkan', $log->new_values['status']);
            $this->assertNotNull($log->new_values['cancelled_at']);
        }

        $this->assertSame(LoanRequestStatus::Disetujui, $future->fresh()->status);
        $this->assertSame(LoanRequestStatus::Menunggu, $pending->fresh()->status);
        $this->assertSame(2, AuditLog::query()->where('action', 'loan_request.expired')->count());
    }

    public function test_cleanup_is_scheduled_daily_at_one_am(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event): bool => str_contains((string) $event->command, 'library:cleanup'));

        $this->assertNotNull($event);
        $this->assertSame('0 1 * * *', $event->expression);
    }
}
