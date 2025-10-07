<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default store if none exists
        if (Store::count() === 0) {
            Store::create([
                'name' => 'My Store',
                'business_name' => 'My Business LLC',
                'address' => '123 Main Street, City, State 12345',
                'phone' => '+1 (555) 123-4567',
                'email' => 'store@example.com',
                'business_registration_number' => null,
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
                'currency' => 'USD',
                'currency_symbol' => '$',
                'receipt_settings' => [
                    'header' => 'Thank you for your business!',
                    'show_logo' => true,
                    'show_business_info' => true,
                ],
                'receipt_footer' => 'Thank you for your business!',
                'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
                'is_active' => true,
            ]);
        }
    }
}
