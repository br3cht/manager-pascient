<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PatientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Patient::count() > 0) {
            return;
        }

        $addressIds = Address::pluck('id');

        if ($addressIds->isEmpty()) {
            $this->call(AddressSeeder::class);
            $addressIds = Address::pluck('id');
        }

        Patient::insert([
            [
                'name' => 'Maria da Silva',
                'cpf' =>  '12345678901',
                'cns' => '123456789012345',
                'birth_date' => Carbon::parse('1985-03-15'),
                'gender' => 'F',
                'address_id' => $addressIds->random(),
            ],
            [
                'name' => 'João Carlos Oliveira',
                'cpf' =>  '98765432100',
                'cns' => '987654321098765',
                'birth_date' => Carbon::parse('1972-07-22')->format('Y-m-d'),
                'gender' => 'M',
                'address_id' => $addressIds->random(),
            ],

            [
                'name' => 'Maria da Silva',
                'cpf' =>  '45678912300',
                'cns' => '456789123004567',
                'birth_date' => Carbon::parse('1990-11-08')->format('Y-m-d'),
                'gender' => 'F',
                'address_id' => $addressIds->random(),
            ],

            [
                'name' => 'Carlos E. Mendes',
                'cpf' =>  '32165498700',
                'cns' => '321654987003216',
                'birth_date' => Carbon::parse('1968-01-30')->format('Y-m-d'),
                'gender' => 'M',
                'address_id' => $addressIds->random(),
            ],
        ]);
    }
}
