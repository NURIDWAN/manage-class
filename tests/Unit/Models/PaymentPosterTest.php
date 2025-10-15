<?php

namespace Tests\Unit\Models;

use App\Models\PaymentPoster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentPosterTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_active_poster_is_kept(): void
    {
        $first = PaymentPoster::factory()->active()->create();
        $second = PaymentPoster::factory()->active()->create();

        $first->refresh();
        $second->refresh();

        $this->assertFalse($first->is_active);
        $this->assertTrue($second->is_active);
    }

    public function test_file_url_returns_null_when_storage_missing_file(): void
    {
        Storage::fake('public');

        $poster = PaymentPoster::factory()->create([
            'file_path' => 'posters/unavailable.pdf',
        ]);

        $this->assertNull($poster->file_url);
    }

    public function test_file_url_returns_signed_path_when_exists(): void
    {
        Storage::fake('public');

        $poster = PaymentPoster::factory()->create([
            'file_path' => 'posters/available.pdf',
        ]);

        Storage::disk('public')->put($poster->file_path, 'pdf');

        $this->assertSame(
            Storage::disk('public')->url($poster->file_path),
            $poster->fresh()->file_url
        );
    }
}
