<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Address::count() > 0) {
            return;
        }

        Address::insert([
            [
                'street' => 'Av. Paulista, 1000',
                'zip_code' =>  '01310100',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],

            [
                'street' => 'Rua das Flores, 250',
                'zip_code' =>  '30130010',
                'neighborhood' => 'Centro',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
            ],

            [
                'street' => 'Rua XV de Novembro, 80',
                'zip_code' =>  '80020310',
                'neighborhood' => 'Centro Civico',
                'city' => 'Curitiba',
                'state' => 'PR',
            ],

            [
                'street' => 'Av. Rio Branco, 45',
                'zip_code' =>  '20040004',
                'neighborhood' => 'Centro',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
            ]
        ]);
    }
}
