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

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_paginated_patients(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Patient::factory()->create([
            'name' => 'Ana Silva',
            'cpf' => '52998224725',
            'cns' => '111111111111111',
            'gender' => 'F',
        ]);

        Patient::factory()->create([
            'name' => 'Bruno Santos',
            'cpf' => '11144477735',
            'cns' => '222222222222222',
            'gender' => 'M',
        ]);

        $response = $this->getJson('/api/patients?page=1&per_page=1&sort_by=name&sort_dir=desc');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Bruno Santos')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2);
    }

    public function test_authenticated_user_can_filter_patients_by_gender_and_search(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Patient::factory()->create([
            'name' => 'Ana Silva',
            'cpf' => '52998224725',
            'cns' => '111111111111111',
            'gender' => 'F',
        ]);

        Patient::factory()->create([
            'name' => 'Bruno Santos',
            'cpf' => '11144477735',
            'cns' => '222222222222222',
            'gender' => 'M',
        ]);

        $response = $this->getJson('/api/patients?page=1&per_page=15&gender=F&search=Ana');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Ana Silva')
            ->assertJsonPath('data.0.gender', 'F');
    }

    public function test_patient_index_validation_returns_readable_portuguese_messages(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/patients?page=abc&per_page=abc&sort_by=invalid&sort_dir=invalid&gender=X');

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.page.0', 'O campo página deve ser um número inteiro.')
            ->assertJsonPath('errors.per_page.0', 'O campo itens por página deve ser um número inteiro.')
            ->assertJsonPath('errors.sort_by.0', 'O campo campo de ordenação selecionado é inválido.')
            ->assertJsonPath('errors.sort_dir.0', 'O campo direção da ordenação selecionado é inválido.')
            ->assertJsonPath('errors.gender.0', 'O campo gênero selecionado é inválido.');
    }

    public function test_authenticated_user_can_store_patient_with_daily_log(): void
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
            ->with('Paciente criado com sucesso', Mockery::on(function (array $context) use ($address): bool {
                return $context['action'] === 'created'
                    && $context['entity'] === 'patient'
                    && is_int($context['entity_id'])
                    && $context['name'] === 'Ana Silva'
                    && $context['cpf'] === '52998224725'
                    && $context['cns'] === '111111111111111'
                    && $context['address_id'] === $address->id;
            }));

        $response = $this->postJson('/api/patients', [
            'name' => 'Ana Silva',
            'cpf' => '52998224725',
            'cns' => '111111111111111',
            'birth_date' => '1990-01-10',
            'gender' => 'F',
            'phone' => '11999999999',
            'address_id' => $address->id,
        ]);

        $response
            ->assertOk()
            ->assertJson(['message' => 'Paciente cadastrado com sucesso']);

        $this->assertDatabaseHas('patients', [
            'name' => 'Ana Silva',
            'cpf' => '52998224725',
            'address_id' => $address->id,
        ]);
    }

    public function test_patient_store_validation_returns_readable_portuguese_messages(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/patients', [
            'name' => '',
            'cpf' => '123',
            'cns' => '123',
            'birth_date' => 'invalid-date',
            'gender' => 'X',
            'phone' => '123',
            'address_id' => 999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.name.0', 'O campo nome é obrigatório.')
            ->assertJsonPath('errors.cpf.0', 'O campo CPF deve conter exatamente 11 dígitos.')
            ->assertJsonPath('errors.cns.0', 'O campo CNS deve conter exatamente 15 dígitos.')
            ->assertJsonPath('errors.birth_date.0', 'O campo data de nascimento deve ser uma data válida.')
            ->assertJsonPath('errors.gender.0', 'O campo gênero selecionado é inválido.')
            ->assertJsonPath('errors.phone.0', 'O campo telefone deve conter 11 dígitos, incluindo o DDD.')
            ->assertJsonPath('errors.address_id.0', 'O campo endereço selecionado é inválido.');
    }

    public function test_authenticated_user_cannot_store_patient_with_duplicate_cpf(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();

        Patient::factory()->create([
            'cpf' => '52998224725',
        ]);

        $response = $this->postJson('/api/patients', [
            'name' => 'Ana Silva',
            'cpf' => '52998224725',
            'cns' => '111111111111111',
            'birth_date' => '1990-01-10',
            'gender' => 'F',
            'phone' => '11999999999',
            'address_id' => $address->id,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.cpf.0', 'Este CPF já está cadastrado.');
    }

    public function test_authenticated_user_cannot_store_patient_with_duplicate_cns(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();

        Patient::factory()->create([
            'cns' => '111111111111111',
        ]);

        $response = $this->postJson('/api/patients', [
            'name' => 'Ana Silva',
            'cpf' => '52998224725',
            'cns' => '111111111111111',
            'birth_date' => '1990-01-10',
            'gender' => 'F',
            'phone' => '11999999999',
            'address_id' => $address->id,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.cns.0', 'Este CNS já está cadastrado.');
    }

    public function test_authenticated_user_can_update_patient_with_daily_log(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();
        $patient = Patient::factory()->create([
            'cpf' => '52998224725',
        ]);
        $logger = Mockery::mock(LoggerInterface::class);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturn($logger);

        $logger
            ->shouldReceive('info')
            ->once()
            ->with('Paciente atualizado com sucesso', [
                'action' => 'updated',
                'entity' => 'patient',
                'entity_id' => $patient->id,
                'changes' => [
                    'name' => 'Ana Atualizada',
                    'cpf' => '52998224725',
                    'cns' => '333333333333333',
                    'birth_date' => '1991-02-20',
                    'gender' => 'F',
                    'phone' => '11888888888',
                    'address_id' => $address->id,
                ],
            ]);

        $response = $this->putJson("/api/patients/{$patient->id}", [
            'name' => 'Ana Atualizada',
            'cpf' => '52998224725',
            'cns' => '333333333333333',
            'birth_date' => '1991-02-20',
            'gender' => 'F',
            'phone' => '11888888888',
            'address_id' => $address->id,
        ]);

        $response
            ->assertOk()
            ->assertJson(['message' => 'Paciente atualizado com sucesso']);

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'name' => 'Ana Atualizada',
            'cpf' => '52998224725',
            'address_id' => $address->id,
        ]);
    }

    public function test_authenticated_user_cannot_update_patient_with_cns_from_another_patient(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $address = Address::factory()->create();
        $patient = Patient::factory()->create([
            'cpf' => '52998224725',
            'cns' => '111111111111111',
        ]);

        Patient::factory()->create([
            'cpf' => '11144477735',
            'cns' => '222222222222222',
        ]);

        $response = $this->putJson("/api/patients/{$patient->id}", [
            'name' => 'Ana Atualizada',
            'cpf' => '52998224725',
            'cns' => '222222222222222',
            'birth_date' => '1991-02-20',
            'gender' => 'F',
            'phone' => '11888888888',
            'address_id' => $address->id,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('errors.cns.0', 'Este CNS já está cadastrado.');
    }

    public function test_authenticated_user_can_delete_patient_with_daily_log(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $patient = Patient::factory()->create();
        $logger = Mockery::mock(LoggerInterface::class);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturn($logger);

        $logger
            ->shouldReceive('info')
            ->once()
            ->with('Paciente deletado com sucesso', [
                'action' => 'deleted',
                'entity' => 'patient',
                'entity_id' => $patient->id,
            ]);

        $response = $this->deleteJson("/api/patients/{$patient->id}");

        $response
            ->assertOk()
            ->assertJson(['message' => 'Paciente deletado com sucesso']);

        $this->assertDatabaseMissing('patients', [
            'id' => $patient->id,
        ]);
    }
}
