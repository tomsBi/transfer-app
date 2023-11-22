<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'creditor_account_id' => Uuid::uuid4()->toString(),
            'debtor_account_id' => Uuid::uuid4()->toString(),
            'amount' => fake()->randomFloat(2, 0.01, 999.99),
            'currency' => fake()->randomElement(['USD', 'GBP', 'EUR']),
            'reference' => fake()->word(),
        ];
    }
}
