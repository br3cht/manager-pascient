<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_see_dashboard_totals(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Address::factory()->count(2)->create();
        Patient::factory()->count(3)->create();

        $response = $this->getJson('/api/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('data.patients_total', 3)
            ->assertJsonPath('data.addresses_total', 5);
    }

    public function test_guest_cannot_see_dashboard_totals(): void
    {
        $this->getJson('/api/dashboard')
            ->assertUnauthorized();
    }
}
