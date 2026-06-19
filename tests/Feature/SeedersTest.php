<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PatientsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_seeder_creates_initial_addresses_once(): void
    {
        $this->seed(AddressSeeder::class);
        $this->seed(AddressSeeder::class);

        $this->assertDatabaseCount('addresses', 5);
        $this->assertDatabaseHas('addresses', [
            'street' => 'Av. Paulista, 1000',
            'zip_code' => '01310100',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);
    }

    public function test_patients_seeder_creates_patients_with_addresses_once(): void
    {
        $this->seed(PatientsSeeder::class);
        $this->seed(PatientsSeeder::class);

        $this->assertDatabaseCount('addresses', 5);
        $this->assertDatabaseCount('patients', 10);
        $this->assertDatabaseHas('patients', [
            'name' => 'João Carlos Oliveira',
            'cpf' => '98765432100',
            'cns' => '987654321098765',
            'birth_date' => '1972-07-22',
            'gender' => 'M',
        ]);

        Patient::query()->each(function (Patient $patient): void {
            $this->assertTrue(Address::query()->whereKey($patient->address_id)->exists());
        });
    }

    public function test_database_seeder_runs_application_seeders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->assertSame(1, User::count());
        $this->assertSame(5, Address::count());
        $this->assertSame(10, Patient::count());
    }
}
