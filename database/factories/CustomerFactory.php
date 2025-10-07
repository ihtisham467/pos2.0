<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_code' => \App\Models\Customer::generateCustomerCode(),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'outstanding_balance' => $this->faker->randomFloat(2, 0, 1000),
            'credit_limit' => $this->faker->randomFloat(2, 500, 5000),
            'credit_status' => $this->faker->randomElement(['active', 'suspended', 'inactive']),
            'last_payment_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'total_purchases' => $this->faker->randomFloat(2, 0, 10000),
            'total_transactions' => $this->faker->numberBetween(0, 100),
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
