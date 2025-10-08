<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a store', function () {
    $store = Store::factory()->create([
        'name' => 'Test Store',
        'email' => 'test@store.com',
    ]);

    expect($store->name)->toBe('Test Store');
    expect($store->email)->toBe('test@store.com');
    expect($store->is_active)->toBeTrue();
});

it('has default business hours when created', function () {
    $store = Store::factory()->create();

    expect($store->business_hours)->toBeArray();
    expect($store->business_hours)->toHaveKeys(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
});

it('can generate receipt numbers with default format', function () {
    $store = Store::factory()->create([
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
    ]);

    $receiptNumber = $store->generateReceiptNumber();

    expect($receiptNumber)->toStartWith('POS-');
    expect($receiptNumber)->toMatch('/^POS-\d{4}-\d{2}-\d{2}-\d{6}$/');
});

it('can generate receipt numbers with custom format', function () {
    $store = Store::factory()->create([
        'receipt_number_format' => 'INV-{0000}',
    ]);

    $receiptNumber = $store->generateReceiptNumber();

    expect($receiptNumber)->toStartWith('INV-');
    expect($receiptNumber)->toMatch('/^INV-\d{6}$/');
});

it('generates unique receipt numbers', function () {
    $store = Store::factory()->create();

    $receiptNumbers = collect(range(1, 10))->map(fn () => $store->generateReceiptNumber());

    expect($receiptNumbers->unique()->count())->toBe(10);
});

it('can format currency amounts', function () {
    $store = Store::factory()->create([
        'currency' => 'USD',
        'currency_symbol' => '$',
    ]);

    expect($store->formatCurrency(123.45))->toBe('$123.45');
    expect($store->formatCurrency(0))->toBe('$0.00');
    expect($store->formatCurrency(1000))->toBe('$1,000.00');
});

it('can format currency with different symbols', function () {
    $store = Store::factory()->create([
        'currency' => 'EUR',
        'currency_symbol' => '€',
    ]);

    expect($store->formatCurrency(123.45))->toBe('€123.45');
});

it('has active scope', function () {
    // Clear any existing stores first
    Store::query()->delete();
    
    $activeStore = Store::factory()->create(['is_active' => true]);
    $inactiveStore = Store::factory()->create(['is_active' => false]);

    // Debug: let's see what we actually have
    $allStores = Store::all();
    expect($allStores)->toHaveCount(2);
    
    // Check individual stores
    expect($activeStore->is_active)->toBeTrue();
    expect($inactiveStore->is_active)->toBeFalse();
    
    $activeStores = Store::query()->active()->get();
    
    expect($activeStores)->toHaveCount(1);
    expect($activeStores->first()->is_active)->toBeTrue();
});

it('can have users relationship', function () {
    $store = Store::factory()->create();
    $user = User::factory()->create(['store_id' => $store->id]);

    expect($store->users)->toHaveCount(1);
    expect($store->users->first()->id)->toBe($user->id);
});

it('casts business_hours to array', function () {
    $store = Store::factory()->create([
        'business_hours' => [
            'monday' => '9:00 AM - 6:00 PM',
            'tuesday' => '9:00 AM - 6:00 PM',
        ],
    ]);

    expect($store->business_hours)->toBeArray();
    expect($store->business_hours['monday'])->toBe('9:00 AM - 6:00 PM');
});

it('casts receipt_settings to array', function () {
    $store = Store::factory()->create([
        'receipt_settings' => [
            'header_text' => 'Welcome',
            'footer_text' => 'Thank you',
            'show_logo' => true,
        ],
    ]);

    expect($store->receipt_settings)->toBeArray();
    expect($store->receipt_settings['header_text'])->toBe('Welcome');
});

it('casts is_active to boolean', function () {
    $store = Store::factory()->create(['is_active' => 1]);

    expect($store->is_active)->toBeTrue();
    expect($store->is_active)->toBeBool();
});

it('can be created with factory states', function () {
    $activeStore = Store::factory()->active()->create();
    $inactiveStore = Store::factory()->inactive()->create();

    expect($activeStore->is_active)->toBeTrue();
    expect($inactiveStore->is_active)->toBeFalse();
});

it('has fillable attributes', function () {
    $store = new Store();

    $fillable = [
        'name',
        'business_name',
        'address',
        'phone',
        'email',
        'city',
        'state',
        'postal_code',
        'country',
        'business_registration_number',
        'logo_path',
        'business_hours',
        'currency',
        'currency_symbol',
        'receipt_settings',
        'receipt_footer',
        'receipt_number_format',
        'is_active',
    ];

    expect($store->getFillable())->toBe($fillable);
});

it('can handle null business hours gracefully', function () {
    $store = Store::factory()->create(['business_hours' => null]);

    expect($store->business_hours)->toBeNull();
});

it('can handle null receipt settings gracefully', function () {
    $store = Store::factory()->create(['receipt_settings' => null]);

    expect($store->receipt_settings)->toBeNull();
});
