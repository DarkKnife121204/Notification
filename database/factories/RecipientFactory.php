<?php

namespace Database\Factories;

use App\Models\Recipient;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->numberBetween(1, 100000),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+7' . fake()->unique()->numerify('##########'),
        ];
    }
}
