<?php

use App\Models\Store;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('store settings page can be rendered', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('store.edit'));
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('settings/Store')
        ->has('store')
        ->has('currencies')
        ->has('receiptNumberFormats')
        ->has('businessHours')
    );
});

test('store settings can be updated', function () {
    $user = User::factory()->create();
    $store = Store::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('store.update'), [
        'name' => 'Updated Store Name',
        'business_name' => 'Updated Business LLC',
        'address' => '456 New Street, New City, State 54321',
        'phone' => '+1 (555) 987-6543',
        'email' => 'updated@example.com',
        'business_registration_number' => '987654321',
        'business_hours' => [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'saturday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'sunday' => ['open' => '09:00', 'close' => '17:00', 'closed' => true],
        ],
        'currency' => 'EUR',
        'currency_symbol' => '€',
        'receipt_footer' => 'Thank you for shopping with us!',
        'receipt_number_format' => 'INV-{YYYY}{MM}{DD}-{0000}',
        'is_active' => true,
    ]);
    
    $response->assertRedirect(route('store.edit'));
    $response->assertSessionHas('status', 'Store settings updated successfully.');
    
    $this->assertDatabaseHas('stores', [
        'name' => 'Updated Store Name',
        'business_name' => 'Updated Business LLC',
        'email' => 'updated@example.com',
        'currency' => 'EUR',
    ]);
});

test('store settings validation works', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->patch(route('store.update'), [
        'name' => '',
        'business_name' => '',
        'address' => '',
        'phone' => '',
        'email' => 'invalid-email',
        'currency' => 'INVALID',
    ]);
    
    $response->assertSessionHasErrors([
        'name',
        'business_name',
        'address',
        'phone',
        'email',
        'currency',
    ]);
});

test('store can be created if none exists', function () {
    $user = User::factory()->create();
    
    // Ensure no store exists
    Store::query()->delete();
    
    $response = $this->actingAs($user)->patch(route('store.update'), [
        'name' => 'New Store',
        'business_name' => 'New Business LLC',
        'address' => '789 Test Street, Test City, State 11111',
        'phone' => '+1 (555) 111-2222',
        'email' => 'newstore@example.com',
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
        'receipt_footer' => 'Thank you!',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'is_active' => true,
    ]);
    
    $response->assertRedirect(route('store.edit'));
    
    $this->assertDatabaseHas('stores', [
        'name' => 'New Store',
        'email' => 'newstore@example.com',
        'is_active' => true,
    ]);
});
