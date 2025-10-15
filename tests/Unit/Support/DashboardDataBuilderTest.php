<?php

namespace Tests\Unit\Support;

use App\Models\Announcement;
use App\Models\CashExpense;
use App\Models\CashPayment;
use App\Models\Event;
use App\Models\User;
use App\Support\DashboardDataBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardDataBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2024-11-15');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_builds_statistics_and_cash_summary(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        CashPayment::factory()->confirmed()->create([
            'user_id' => $user->id,
            'amount' => 15000,
            'date' => Carbon::today()->toDateString(),
        ]);

        CashPayment::factory()->confirmed()->create([
            'amount' => 20000,
            'date' => Carbon::today()->copy()->subMonth()->toDateString(),
        ]);

        CashPayment::factory()->pending()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'date' => Carbon::today()->toDateString(),
        ]);

        CashExpense::factory()->create([
            'amount' => 5000,
            'date' => Carbon::today()->toDateString(),
            'status' => 'confirmed',
        ]);

        Announcement::factory()->create();

        Event::factory()->create([
            'date' => Carbon::today()->addWeek()->toDateString(),
        ]);

        $builder = new DashboardDataBuilder($user);

        $stats = $builder->stats();
        $summary = $builder->cashSummary();
        $charts = $builder->chartData();

        $this->assertSame(1, $stats['announcements']);
        $this->assertSame(1, $stats['events']);
        $this->assertSame(35000, $stats['cash_total']);
        $this->assertSame(5000, $stats['cash_out_total']);
        $this->assertSame(30000, $stats['fund_balance']); // cash_in - cash_out

        $this->assertSame(4, $summary['weeksInMonth']);
        $this->assertSame(config('classmanager.defaults.weekly_cash_amount'), $summary['weeklyTargetAmount']);
        $this->assertSame(15000, $summary['currentUserSummary']['paid']);
        $this->assertSame(40000, $summary['currentUserSummary']['target']);
        $this->assertSame(25000, $summary['currentUserSummary']['remaining']);
        $this->assertGreaterThan(0, $summary['progress']);

        $this->assertCount(6, $charts['monthly']['labels']);
        $this->assertCount(6, $charts['monthly']['data']);
        $this->assertCount(8, $charts['weekly']['labels']);
        $this->assertCount(8, $charts['weekly']['data']);
    }

    public function test_it_provides_announcements_events_and_report_data(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $member = User::factory()->create(['role' => 'user']);

        Announcement::factory()->create([
            'content' => '<p>Pengumuman penting</p>',
        ]);

        Event::factory()->create([
            'date' => Carbon::today()->addDays(3)->toDateString(),
        ]);

        CashPayment::factory()->confirmed()->create([
            'user_id' => $admin->id,
            'amount' => 30000,
            'date' => Carbon::today()->toDateString(),
        ]);

        CashPayment::factory()->confirmed()->create([
            'user_id' => $member->id,
            'amount' => 20000,
            'date' => Carbon::today()->toDateString(),
        ]);

        CashExpense::factory()->create([
            'amount' => 10000,
            'date' => Carbon::today()->toDateString(),
            'status' => 'confirmed',
        ]);

        $builder = new DashboardDataBuilder($admin);

        $announcements = $builder->announcementsAndEvents();
        $reportData = $builder->cashReportData();
        $options = $builder->monthOptions();
        $banner = $builder->latestAnnouncementBanner();

        $this->assertCount(1, $announcements['upcomingEvents']);
        $this->assertCount(1, $announcements['recentAnnouncements']);

        $this->assertSame(2, $reportData['payments']->count());
        $this->assertSame(50000, $reportData['totalIn']);
        $this->assertSame(10000, $reportData['totalOut']);
        $this->assertSame(40000, $reportData['net']);

        $this->assertNotEmpty($options);
        $this->assertSame(Carbon::today()->format('Y-m'), $options[0]['value']);

        $this->assertNotNull($banner);
        $this->assertStringContainsString('Pengumuman', $banner['summary'] ?? '');
    }
}
