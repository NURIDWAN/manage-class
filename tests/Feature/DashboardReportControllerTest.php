<?php

namespace Tests\Feature;

use App\Models\CashPayment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_generates_pdf_stream_for_completed_profile(): void
    {
        Carbon::setTestNow('2024-11-15');

        $user = User::factory()->create();

        CashPayment::factory()->confirmed()->create([
            'user_id' => $user->id,
            'amount' => 20000,
            'date' => Carbon::today()->toDateString(),
        ]);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.cash-payments-report', \Mockery::type('array'))
            ->andReturnSelf();

        Pdf::shouldReceive('setPaper')
            ->once()
            ->with('a4', 'portrait')
            ->andReturnSelf();

        Pdf::shouldReceive('output')
            ->zeroOrMoreTimes()
            ->andReturn('pdf-content');

        $response = $this->actingAs($user)
            ->get(route('dashboard.reports.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString(
            'laporan-kas-' . Carbon::today()->format('Y-m') . '.pdf',
            $response->headers->get('content-disposition')
        );

        Carbon::setTestNow();
    }
}
