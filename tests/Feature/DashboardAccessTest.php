<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirects_user_without_profile_to_complete_page(): void
    {
        $user = User::factory()->create([
            'nim' => null,
        ]);

        $this->actingAs($user);

        $this->get(route('dashboard.cash'))
            ->assertRedirect(route('profile.complete'))
            ->assertSessionHas('status', 'profile-required');

        $this->get(route('dashboard.reports'))
            ->assertRedirect(route('profile.complete'))
            ->assertSessionHas('status', 'profile-required');
    }

    public function test_dashboard_pages_load_for_completed_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.cash'))
            ->assertOk()
            ->assertViewIs('dashboard.cash');

        $this->actingAs($user)
            ->get(route('dashboard.reports'))
            ->assertOk()
            ->assertViewIs('dashboard.reports');
    }
}
