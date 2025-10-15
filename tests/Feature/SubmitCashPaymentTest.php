<?php

namespace Tests\Feature;

use App\Models\CashPayment;
use App\Models\ClassFund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmitCashPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->post(route('dashboard.cash.payments.store'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_completed_profile_is_redirected(): void
    {
        $user = User::factory()->create([
            'nim' => null,
        ]);

        $this->actingAs($user)
            ->post(route('dashboard.cash.payments.store'), [])
            ->assertRedirect(route('profile.complete'))
            ->assertSessionHas('status', 'profile-required');
    }

    public function test_user_can_submit_cash_payment_with_proof(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.cash.payments.store'), [
            'amount' => 20000,
            'date' => now()->toDateString(),
            'payment_method' => 'transfer',
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $response->assertRedirect(route('dashboard.cash'))
            ->assertSessionHas('status', 'cash-payment-submitted');

        $payment = CashPayment::first();

        $this->assertNotNull($payment);
        $this->assertSame($user->id, $payment->user_id);
        $this->assertSame(20000, $payment->amount);
        $this->assertSame('pending', $payment->status);
        $this->assertSame('transfer', $payment->payment_method);
        $this->assertNotNull($payment->proof_path);
        Storage::disk('public')->assertExists($payment->proof_path);

        $fund = ClassFund::first();
        $this->assertSame(0, $fund->cash_in_total);
    }
}
