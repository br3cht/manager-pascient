<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Psr\Log\LoggerInterface;
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

    public function test_address_index_validation_returns_readable_portuguese_messages(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/addresses?page=abc&per_page=abc&sort_by=invalid&sort_dir=invalid&state=SPP');

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.page.0', 'O campo página deve ser um número inteiro.')
            ->assertJsonPath('errors.per_page.0', 'O campo itens por página deve ser um número inteiro.')
            ->assertJsonPath('errors.sort_by.0', 'O campo campo de ordenação selecionado é inválido.')
            ->assertJsonPath('errors.sort_dir.0', 'O campo direção da ordenação selecionado é inválido.')
            ->assertJsonPath('errors.state.0', 'O campo estado deve ter no máximo 2 caracteres.');
    }

    public function test_guest_cannot_list_addresses(): void
    {
        $this->getJson('/api/addresses?page=1&per_page=15')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_store_address(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $logger = Mockery::mock(LoggerInterface::class);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturn($logger);

        $logger
            ->shouldReceive('info')
            ->once()
            ->with('Endereco criado com sucesso', Mockery::on(function (array $context): bool {
                return $context['action'] === 'created'
                    && $context['entity'] === 'address'
                    && is_int($context['entity_id'])
                    && $context['street'] === 'Rua Nova'
                    && $context['zip_code'] === '12345678'
                    && $context['city'] === 'Santos'
                    && $context['state'] === 'SP';
            }));

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

    public function test_address_store_validation_returns_readable_portuguese_messages(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/addresses', [
            'street' => '',
            'zip_code' => '123',
            'neighborhood' => '',
            'city' => '',
            'state' => 'SPP',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.street.0', 'O campo logradouro é obrigatório.')
            ->assertJsonPath('errors.zip_code.0', 'O campo CEP deve conter exatamente 8 dígitos.')
            ->assertJsonPath('errors.neighborhood.0', 'O campo bairro é obrigatório.')
            ->assertJsonPath('errors.city.0', 'O campo cidade é obrigatório.')
            ->assertJsonPath('errors.state.0', 'O campo estado deve ter no máximo 2 caracteres.');
    }

    public function test_authenticated_user_can_update_address_with_daily_log(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();
        $logger = Mockery::mock(LoggerInterface::class);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturn($logger);

        $logger
            ->shouldReceive('info')
            ->once()
            ->with('Endereco atualizado com sucesso', [
                'action' => 'updated',
                'entity' => 'address',
                'entity_id' => $address->id,
                'changes' => [
                    'street' => 'Rua Atualizada',
                    'zip_code' => '87654321',
                    'neighborhood' => 'Boqueirao',
                    'city' => 'Praia Grande',
                    'state' => 'SP',
                ],
            ]);

        $response = $this->putJson("/api/addresses/{$address->id}", [
            'street' => 'Rua Atualizada',
            'zip_code' => '87654321',
            'neighborhood' => 'Boqueirao',
            'city' => 'Praia Grande',
            'state' => 'SP',
        ]);

        $response
            ->assertOk()
            ->assertJson(['message' => 'Endereco atualizado com sucesso']);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street' => 'Rua Atualizada',
            'zip_code' => '87654321',
        ]);
    }

    public function test_authenticated_user_can_delete_address_with_daily_log(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();
        $logger = Mockery::mock(LoggerInterface::class);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturn($logger);

        $logger
            ->shouldReceive('info')
            ->once()
            ->with('Endereco deletado com sucesso', [
                'action' => 'deleted',
                'entity' => 'address',
                'entity_id' => $address->id,
            ]);

        $response = $this->deleteJson("/api/addresses/{$address->id}");

        $response
            ->assertOk()
            ->assertJson(['message' => 'Endereco deletado com sucesso']);

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_authenticated_user_cannot_delete_address_linked_to_patient(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();

        Patient::factory()->create([
            'address_id' => $address->id,
        ]);

        Log::shouldReceive('channel')->never();

        $response = $this->deleteJson("/api/addresses/{$address->id}");

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.address.0', 'Este endereço não pode ser deletado porque está vinculado a um paciente.');

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
        ]);
    }
}
