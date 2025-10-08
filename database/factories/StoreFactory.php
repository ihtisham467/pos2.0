<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Test Store ' . $this->faker->randomNumber(3),
            'business_name' => 'Test Business ' . $this->faker->randomNumber(3) . ' Inc.',
            'address' => '123 Test Street',
            'phone' => '+1234567890',
            'email' => 'test@store.com',
            'business_registration_number' => 'REG-1234-5678',
            'logo_path' => null,
            'business_hours' => [
                'monday' => '9:00 AM - 6:00 PM',
                'tuesday' => '9:00 AM - 6:00 PM',
                'wednesday' => '9:00 AM - 6:00 PM',
                'thursday' => '9:00 AM - 6:00 PM',
                'friday' => '9:00 AM - 6:00 PM',
                'saturday' => '10:00 AM - 4:00 PM',
                'sunday' => 'Closed',
            ],
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_settings' => [
                'header_text' => 'Thank you for your business!',
                'footer_text' => 'Visit us again soon!',
                'show_logo' => true,
                'show_business_info' => true,
                'show_customer_info' => true,
            ],
            'receipt_footer' => 'Thank you for shopping with us!',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'is_active' => true,
        ];
    }

    /**
     * Create an active store.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive store.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
