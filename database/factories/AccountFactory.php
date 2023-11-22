<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'user_id' => Uuid::uuid4()->toString(),
            'currency' => fake()->randomElement(['USD', 'GBP', 'EUR']),
            'balance' => fake()->randomFloat(2, 0.01, 999.99),
        ];
    }
}
