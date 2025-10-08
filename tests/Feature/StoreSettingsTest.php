<?php

use App\Models\Store;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can view store settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('store.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/Store')
            ->has('store')
            ->has('currencies')
            ->has('dateFormats')
            ->has('timeFormats')
            ->has('numberFormats')
            ->has('receiptFormats')
        );
});

it('can update store settings', function () {
    $user = User::factory()->create();

    $storeData = [
        'name' => 'Test Store',
        'business_name' => 'Test Business Inc.',
        'address' => '123 Test Street, Test City',
        'phone' => '+1234567890',
        'email' => 'test@example.com',
        'business_registration_number' => 'REG123456',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'business_hours' => [
            'monday' => '9:00 AM - 6:00 PM',
            'tuesday' => '9:00 AM - 6:00 PM',
            'wednesday' => '9:00 AM - 6:00 PM',
            'thursday' => '9:00 AM - 6:00 PM',
            'friday' => '9:00 AM - 6:00 PM',
            'saturday' => '10:00 AM - 4:00 PM',
            'sunday' => 'Closed',
        ],
        'receipt_settings' => [
            'header_text' => 'Thank you for your business!',
            'footer_text' => 'Visit us again soon!',
            'show_logo' => true,
            'show_business_info' => true,
            'show_customer_info' => true,
        ],
        'receipt_footer' => 'Thank you for shopping with us!',
        'system_settings' => [
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'number_format' => 'us',
            'theme' => 'light',
            'language' => 'en',
        ],
    ];

    $this->actingAs($user)
        ->patch(route('store.update'), $storeData)
        ->assertRedirect()
        ->assertSessionHas('status', 'Store settings updated successfully.');

    $store = Store::active();
    expect($store)->not->toBeNull();
    expect($store->name)->toBe('Test Store');
    expect($store->business_name)->toBe('Test Business Inc.');
    expect($store->currency)->toBe('USD');
    expect($store->currency_symbol)->toBe('$');
    expect($store->business_hours)->toBe($storeData['business_hours']);
    expect($store->receipt_settings)->toBe($storeData['receipt_settings']);
});

it('validates required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('store.update'), [])
        ->assertSessionHasErrors(['name', 'currency', 'currency_symbol', 'receipt_number_format']);
});

it('validates email format', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'email' => 'invalid-email',
        ])
        ->assertSessionHasErrors(['email']);
});

it('validates logo file type', function () {
    $user = User::factory()->create();

    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1000);

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'logo' => $file,
        ])
        ->assertSessionHasErrors(['logo']);
});

it('can handle logo upload', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('logo.jpg', 100, 100);

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'logo' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Store settings updated successfully.');

    $store = Store::active();
    expect($store->logo_path)->not->toBeNull();
    expect($store->logo_path)->toContain('store-logos/');

    // Verify file was stored
    Storage::disk('public')->assertExists($store->logo_path);
});

it('deletes old logo when uploading new one', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    // Create store with existing logo
    $store = Store::factory()->create([
        'logo_path' => 'store-logos/old-logo.jpg',
        'is_active' => true,
    ]);

    // Create the old logo file
    Storage::disk('public')->put('store-logos/old-logo.jpg', 'fake-image-content');

    $newFile = UploadedFile::fake()->image('new-logo.jpg', 100, 100);

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Updated Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'logo' => $newFile,
        ])
        ->assertRedirect();

    // Verify old logo was deleted
    Storage::disk('public')->assertMissing('store-logos/old-logo.jpg');

    // Verify new logo was stored
    $updatedStore = Store::active();
    Storage::disk('public')->assertExists($updatedStore->logo_path);
});

it('validates logo dimensions', function () {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->image('logo.jpg', 3000, 3000);

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'logo' => $file,
        ])
        ->assertSessionHasErrors(['logo']);
});

it('updates system settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'system_settings' => [
                'date_format' => 'm/d/Y',
                'time_format' => 'h:i A',
                'number_format' => 'eu',
                'theme' => 'dark',
                'language' => 'es',
            ],
        ])
        ->assertRedirect();

    expect(SystemSetting::get('date_format'))->toBe('m/d/Y');
    expect(SystemSetting::get('time_format'))->toBe('h:i A');
    expect(SystemSetting::get('number_format'))->toBe('eu');
    expect(SystemSetting::get('theme'))->toBe('dark');
    expect(SystemSetting::get('language'))->toBe('es');
});

it('generates unique receipt numbers', function () {
    $store = Store::factory()->create([
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
    ]);

    $receipt1 = $store->generateReceiptNumber();
    $receipt2 = $store->generateReceiptNumber();

    expect($receipt1)->not->toBe($receipt2);
    expect($receipt1)->toContain('POS-');
    expect($receipt1)->toContain(now()->format('Y'));
    expect($receipt1)->toContain(now()->format('m'));
    expect($receipt1)->toContain(now()->format('d'));
});

it('requires authentication to update store settings', function () {
    $this->patch(route('store.update'), [
        'name' => 'Test Store',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
    ])
        ->assertRedirect('/login');
});

it('can format currency amounts', function () {
    $store = Store::factory()->create([
        'currency_symbol' => '$',
    ]);

    expect($store->formatCurrency(123.45))->toBe('$123.45');
    expect($store->formatCurrency(0))->toBe('$0.00');
    expect($store->formatCurrency(1000))->toBe('$1,000.00');
});

it('can handle business hours data', function () {
    $user = User::factory()->create();

    $businessHours = [
        'monday' => '9:00 AM - 6:00 PM',
        'tuesday' => '9:00 AM - 6:00 PM',
        'wednesday' => '9:00 AM - 6:00 PM',
        'thursday' => '9:00 AM - 6:00 PM',
        'friday' => '9:00 AM - 6:00 PM',
        'saturday' => '10:00 AM - 4:00 PM',
        'sunday' => 'Closed',
    ];

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'business_hours' => $businessHours,
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Store settings updated successfully.');

    $store = Store::active();
    expect($store->business_hours)->toBe($businessHours);
});

it('initializes business hours with empty strings for new store', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->get(route('store.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/Store')
            ->has('store.business_hours')
            ->where('store.business_hours.monday', '')
            ->where('store.business_hours.tuesday', '')
            ->where('store.business_hours.wednesday', '')
            ->where('store.business_hours.thursday', '')
            ->where('store.business_hours.friday', '')
            ->where('store.business_hours.saturday', '')
            ->where('store.business_hours.sunday', '')
        );
});

it('can update store with all fields', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Complete Store',
            'email' => 'complete@store.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'business_registration_number' => 'REG123456',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'receipt_number_format' => 'INV-{0000}',
            'business_hours' => [
                'monday' => '9:00 AM - 6:00 PM',
                'tuesday' => '9:00 AM - 6:00 PM',
                'wednesday' => '9:00 AM - 6:00 PM',
                'thursday' => '9:00 AM - 6:00 PM',
                'friday' => '9:00 AM - 6:00 PM',
                'saturday' => '10:00 AM - 4:00 PM',
                'sunday' => 'Closed',
            ],
            'receipt_settings' => [
                'header_text' => 'Welcome to Complete Store',
                'footer_text' => 'Thank you for your business!',
                'show_logo' => true,
                'show_business_info' => true,
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Store settings updated successfully.');

    $store = Store::active();
    expect($store->name)->toBe('Complete Store');
    expect($store->email)->toBe('complete@store.com');
    expect($store->phone)->toBe('+1234567890');
    expect($store->address)->toBe('123 Main St');
    expect($store->city)->toBe('New York');
    expect($store->state)->toBe('NY');
    expect($store->postal_code)->toBe('10001');
    expect($store->country)->toBe('USA');
    expect($store->business_registration_number)->toBe('REG123456');
    expect($store->currency)->toBe('EUR');
    expect($store->currency_symbol)->toBe('€');
    expect($store->receipt_number_format)->toBe('INV-{0000}');
    expect($store->business_hours['monday'])->toBe('9:00 AM - 6:00 PM');
    expect($store->receipt_settings['header_text'])->toBe('Welcome to Complete Store');
});

it('can update store with minimal required fields only', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Minimal Store',
            'email' => 'minimal@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Store settings updated successfully.');

    $store = Store::active();
    expect($store)->not->toBeNull();
    expect($store->name)->toBe('Minimal Store');
    expect($store->email)->toBe('minimal@store.com');
    expect($store->currency)->toBe('USD');
    expect($store->currency_symbol)->toBe('$');
});

it('validates business hours structure', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'email' => 'test@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'business_hours' => [
                'monday' => '9:00 AM - 6:00 PM',
                'tuesday' => '9:00 AM - 6:00 PM',
                'wednesday' => '9:00 AM - 6:00 PM',
                'thursday' => '9:00 AM - 6:00 PM',
                'friday' => '9:00 AM - 6:00 PM',
                'saturday' => '10:00 AM - 4:00 PM',
                'sunday' => 'Closed',
            ],
        ])
        ->assertRedirect();

    $store = Store::active();
    expect($store)->not->toBeNull();
    expect($store->business_hours)->toBeArray();
    expect($store->business_hours)->toHaveKeys(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
});

it('validates receipt settings structure', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'email' => 'test@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'receipt_settings' => [
                'header_text' => 'Welcome',
                'footer_text' => 'Thank you',
                'show_logo' => true,
                'show_business_info' => false,
            ],
        ])
        ->assertRedirect();

    $store = Store::active();
    expect($store)->not->toBeNull();
    expect($store->receipt_settings)->toBeArray();
    expect($store->receipt_settings)->toHaveKeys(['header_text', 'footer_text', 'show_logo', 'show_business_info']);
    expect($store->receipt_settings['show_logo'])->toBeTrue();
    expect($store->receipt_settings['show_business_info'])->toBeFalse();
});

it('can handle different currency formats', function () {
    $user = User::factory()->create();
    
    $currencies = [
        ['code' => 'USD', 'symbol' => '$'],
        ['code' => 'EUR', 'symbol' => '€'],
        ['code' => 'GBP', 'symbol' => '£'],
        ['code' => 'JPY', 'symbol' => '¥'],
    ];

    foreach ($currencies as $currency) {
        $this->actingAs($user)
            ->patch(route('store.update'), [
                'name' => 'Test Store',
                'email' => 'test@store.com',
                'currency' => $currency['code'],
                'currency_symbol' => $currency['symbol'],
                'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            ])
            ->assertRedirect();

        $store = Store::active();
        expect($store)->not->toBeNull();
        expect($store->currency)->toBe($currency['code']);
        expect($store->currency_symbol)->toBe($currency['symbol']);
    }
});

it('can handle different receipt number formats', function () {
    $user = User::factory()->create();
    
    $formats = [
        'POS-{YYYY}-{MM}-{DD}-{0000}',
        'RCP-{YYYY}{MM}{DD}-{0000}',
        'INV-{0000}',
        '{YYYY}-{MM}-{DD}-{0000}',
        'STORE-{0000}',
        'TXN-{YYYY}-{0000}',
    ];

    foreach ($formats as $format) {
        $this->actingAs($user)
            ->patch(route('store.update'), [
                'name' => 'Test Store',
                'email' => 'test@store.com',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'receipt_number_format' => $format,
            ])
            ->assertRedirect();

        $store = Store::active();
        expect($store->receipt_number_format)->toBe($format);
    }
});

it('can update existing store settings', function () {
    $user = User::factory()->create();
    $store = Store::factory()->create([
        'name' => 'Original Store',
        'email' => 'original@store.com',
        'currency' => 'USD',
        'currency_symbol' => '$',
    ]);

    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Updated Store',
            'email' => 'updated@store.com',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Store settings updated successfully.');

    $updatedStore = Store::active();
    expect($updatedStore)->not->toBeNull();
    expect($updatedStore->id)->toBe($store->id);
    expect($updatedStore->name)->toBe('Updated Store');
    expect($updatedStore->email)->toBe('updated@store.com');
    expect($updatedStore->currency)->toBe('EUR');
    expect($updatedStore->currency_symbol)->toBe('€');
});

it('can handle logo upload with different file types', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    
    $fileTypes = ['jpg', 'png', 'gif', 'svg'];
    
    foreach ($fileTypes as $type) {
        $file = UploadedFile::fake()->image("logo.{$type}", 100, 100);
        
        $this->actingAs($user)
            ->patch(route('store.update'), [
                'name' => 'Test Store',
                'email' => 'test@store.com',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
                'logo' => $file,
            ])
            ->assertRedirect();

        $store = Store::active();
        expect($store)->not->toBeNull();
        expect($store->logo_path)->not->toBeNull();
        expect(Storage::disk('public')->exists($store->logo_path))->toBeTrue();
    }
});

it('can handle multiple logo updates', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    
    // Upload first logo
    $firstFile = UploadedFile::fake()->image('logo1.jpg', 100, 100);
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'email' => 'test@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'logo' => $firstFile,
        ])
        ->assertRedirect();

    $store = Store::active();
    expect($store)->not->toBeNull();
    $firstLogoPath = $store->logo_path;
    expect($firstLogoPath)->not->toBeNull();
    expect(Storage::disk('public')->exists($firstLogoPath))->toBeTrue();

    // Upload second logo
    $secondFile = UploadedFile::fake()->image('logo2.png', 100, 100);
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'email' => 'test@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'logo' => $secondFile,
        ])
        ->assertRedirect();

    $updatedStore = Store::active();
    expect($updatedStore)->not->toBeNull();
    $secondLogoPath = $updatedStore->logo_path;
    expect($secondLogoPath)->not->toBe($firstLogoPath);
    expect(Storage::disk('public')->exists($secondLogoPath))->toBeTrue();
    expect(Storage::disk('public')->exists($firstLogoPath))->toBeFalse(); // Old logo should be deleted
});

it('can handle empty business hours', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'email' => 'test@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'business_hours' => [
                'monday' => '',
                'tuesday' => '',
                'wednesday' => '',
                'thursday' => '',
                'friday' => '',
                'saturday' => '',
                'sunday' => '',
            ],
        ])
        ->assertRedirect();

    $store = Store::active();
    expect($store)->not->toBeNull();
    expect($store->business_hours)->toBeArray();
    foreach ($store->business_hours as $day => $hours) {
        expect($hours)->toBeNull(); // Empty strings are converted to null
    }
});

it('can handle empty receipt settings', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->patch(route('store.update'), [
            'name' => 'Test Store',
            'email' => 'test@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
            'receipt_settings' => [
                'header_text' => '',
                'footer_text' => '',
                'show_logo' => false,
                'show_business_info' => false,
            ],
        ])
        ->assertRedirect();

    $store = Store::active();
    expect($store)->not->toBeNull();
    expect($store->receipt_settings)->toBeArray();
    expect($store->receipt_settings['header_text'])->toBeNull(); // Empty strings are converted to null
    expect($store->receipt_settings['footer_text'])->toBeNull(); // Empty strings are converted to null
    expect($store->receipt_settings['show_logo'])->toBeFalse();
    expect($store->receipt_settings['show_business_info'])->toBeFalse();
});
