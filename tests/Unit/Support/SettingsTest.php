<?php

namespace Tests\Unit\Support;

use App\Models\PaymentPoster;
use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_database_value_before_default(): void
    {
        Setting::factory()->create([
            'key' => 'app_name',
            'value' => 'Manajemen Kelas Test',
        ]);

        $this->assertSame('Manajemen Kelas Test', Settings::appName());
    }

    public function test_it_returns_default_when_setting_missing(): void
    {
        $this->assertSame(
            config('classmanager.defaults.weekly_cash_amount'),
            Settings::weeklyCashAmount()
        );
    }

    public function test_it_resolves_active_payment_poster_url(): void
    {
        Storage::fake('public');

        $poster = PaymentPoster::factory()->active()->create([
            'file_path' => 'posters/poster-1.pdf',
        ]);

        Storage::disk('public')->put($poster->file_path, 'pdf-content');

        $this->assertSame(
            Storage::disk('public')->url($poster->file_path),
            Settings::cashPaymentPosterUrl()
        );
    }

    public function test_it_returns_null_when_no_active_poster_or_file_missing(): void
    {
        Storage::fake('public');

        PaymentPoster::factory()->active()->create([
            'file_path' => 'posters/missing.pdf',
        ]);

        $this->assertNull(Settings::cashPaymentPosterUrl());
    }
}
