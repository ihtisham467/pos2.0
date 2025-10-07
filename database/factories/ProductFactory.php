<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'barcode' => $this->faker->unique()->ean13(),
            'category_id' => \App\Models\Category::factory(),
            'selling_price' => $this->faker->randomFloat(2, 5, 500),
            'cost_price' => $this->faker->randomFloat(2, 2, 200),
            'current_stock' => $this->faker->numberBetween(0, 100),
            'minimum_stock_level' => $this->faker->numberBetween(1, 10),
            'serial_number' => $this->faker->optional()->bothify('SN-########'),
            'image_path' => null,
            'track_serial_numbers' => $this->faker->boolean(20),
            'is_active' => true,
        ];
    }
}
