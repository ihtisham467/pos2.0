<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default store if it doesn't exist
        $store = Store::firstOrCreate(
            ['name' => 'Main Store'],
            [
                'business_name' => 'POS System Store',
                'address' => '123 Main Street, City, State 12345',
                'phone' => '+1 (555) 123-4567',
                'email' => 'admin@pos.test',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
                'is_active' => true,
            ]
        );

        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@pos.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'store_id' => $store->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@pos.test');
        $this->command->info('Password: password');
    }
}
