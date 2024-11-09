<?php

namespace Database\Factories;

use App\Models\Travel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class TravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'applicant_name' => $this->faker->name, // Gera um nome aleatório
            'destiny' => $this->faker->city,
            'departure_date' => $this->faker->date(),
            'return_date' => $this->faker->date(),
            'status' => 'requested', // Pode ser 'requested', 'approved', etc.
        ];
    }
}
