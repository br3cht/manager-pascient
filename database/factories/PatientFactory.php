<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###########'),
            'cns' => fake()->unique()->numerify('###############'),
            'birth_date' => fake()->date(),
            'gender' => fake()->randomElement(['M', 'F', 'O']),
            'phone' => fake()->numerify('###########'),
            'address_id' => Address::factory(),
        ];
    }
}
