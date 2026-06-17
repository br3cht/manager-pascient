<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_paginated_addresses(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Address::factory()->create([
            'street' => 'Rua Alfa',
            'zip_code' => '11111111',
            'neighborhood' => 'Centro',
            'city' => 'Curitiba',
            'state' => 'PR',
        ]);

        Address::factory()->create([
            'street' => 'Rua Beta',
            'zip_code' => '22222222',
            'neighborhood' => 'Centro',
            'city' => 'Sao Paulo',
            'state' => 'SP',
        ]);

        $response = $this->getJson('/api/addresses?page=1&per_page=1&sort_by=street&sort_dir=desc');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.street', 'Rua Beta')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2);
    }

    public function test_authenticated_user_can_filter_addresses_by_state_and_search(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Address::factory()->create([
            'street' => 'Av Paulista',
            'zip_code' => '01310100',
            'neighborhood' => 'Bela Vista',
            'city' => 'Sao Paulo',
            'state' => 'SP',
        ]);

        Address::factory()->create([
            'street' => 'Rua XV de Novembro',
            'zip_code' => '80020310',
            'neighborhood' => 'Centro Civico',
            'city' => 'Curitiba',
            'state' => 'PR',
        ]);

        $response = $this->getJson('/api/addresses?page=1&per_page=15&state=sp&search=Paulista');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.street', 'Av Paulista')
            ->assertJsonPath('data.0.state', 'SP');
    }

    public function test_guest_cannot_list_addresses(): void
    {
        $this->getJson('/api/addresses?page=1&per_page=15')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_store_address(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/addresses', [
            'street' => 'Rua Nova',
            'zip_code' => '12345678',
            'neighborhood' => 'Centro',
            'city' => 'Santos',
            'state' => 'SP',
        ]);

        $response
            ->assertOk()
            ->assertJson(['message' => 'Endereco cadastrado com sucesso']);

        $this->assertDatabaseHas('addresses', [
            'street' => 'Rua Nova',
            'zip_code' => '12345678',
            'state' => 'SP',
        ]);
    }
}
