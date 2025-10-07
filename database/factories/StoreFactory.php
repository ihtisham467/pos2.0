<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'business_name' => $this->faker->company() . ' LLC',
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'business_registration_number' => $this->faker->optional()->numerify('##########'),
            'logo_path' => null,
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                'wednesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                'thursday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                'friday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                'saturday' => ['open' => '10:00', 'close' => '16:00', 'closed' => false],
                'sunday' => ['open' => '10:00', 'close' => '16:00', 'closed' => true],
            ],
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'CAD']),
            'currency_symbol' => $this->faker->randomElement(['$', '€', '£', 'C$']),
            'receipt_settings' => [
                'header' => 'Thank you for your business!',
                'show_logo' => true,
                'show_business_info' => true,
            ],
            'receipt_footer' => 'Thank you for your business!',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the store is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
