<?php

use App\Models\Store;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('PKR currency is available in store settings', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('store.edit'));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('settings/Store')
        ->has('currencies')
        ->where('currencies', function ($currencies) {
            return collect($currencies)->contains('code', 'PKR');
        })
    );
});

test('store can be updated with PKR currency', function () {
    $user = User::factory()->create();
    $store = Store::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('store.update'), [
        'name' => 'Test Store',
        'business_name' => 'Test Business LLC',
        'address' => '123 Test Street, Test City, State 12345',
        'phone' => '+1 (555) 123-4567',
        'email' => 'test@example.com',
        'business_hours' => [
            'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'wednesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'thursday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'friday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'saturday' => ['open' => '10:00', 'close' => '16:00', 'closed' => false],
            'sunday' => ['open' => '10:00', 'close' => '16:00', 'closed' => true],
        ],
        'currency' => 'PKR',
        'currency_symbol' => '₨',
        'receipt_footer' => 'Thank you!',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'is_active' => true,
    ]);
    
    $response->assertRedirect(route('store.edit'));
    $response->assertSessionHas('status', 'Store settings updated successfully.');
    
    $this->assertDatabaseHas('stores', [
        'currency' => 'PKR',
        'currency_symbol' => '₨',
    ]);
});

test('PKR currency validation works', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('store.update'), [
        'name' => 'Test Store',
        'business_name' => 'Test Business LLC',
        'address' => '123 Test Street, Test City, State 12345',
        'phone' => '+1 (555) 123-4567',
        'email' => 'test@example.com',
        'business_hours' => [
            'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'wednesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'thursday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'friday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'saturday' => ['open' => '10:00', 'close' => '16:00', 'closed' => false],
            'sunday' => ['open' => '10:00', 'close' => '16:00', 'closed' => true],
        ],
        'currency' => 'PKR',
        'currency_symbol' => '₨',
        'receipt_footer' => 'Thank you!',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'is_active' => true,
    ]);
    
    $response->assertRedirect(route('store.edit'));
    $this->assertDatabaseHas('stores', [
        'currency' => 'PKR',
    ]);
});
