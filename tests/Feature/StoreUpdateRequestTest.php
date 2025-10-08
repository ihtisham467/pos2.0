<?php

use App\Http\Requests\Settings\StoreUpdateRequest;
use App\Models\Store;
use App\Models\User;
use App\Policies\StorePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can be instantiated', function () {
    $request = new StoreUpdateRequest();
    
    expect($request)->toBeInstanceOf(StoreUpdateRequest::class);
});

it('has correct validation rules', function () {
    $request = new StoreUpdateRequest();
    $rules = $request->rules();

    expect($rules)->toBeArray();
    expect($rules)->toHaveKey('name');
    expect($rules)->toHaveKey('email');
    expect($rules)->toHaveKey('phone');
    expect($rules)->toHaveKey('address');
    expect($rules)->toHaveKey('city');
    expect($rules)->toHaveKey('state');
    expect($rules)->toHaveKey('postal_code');
    expect($rules)->toHaveKey('country');
    expect($rules)->toHaveKey('business_registration_number');
    expect($rules)->toHaveKey('currency');
    expect($rules)->toHaveKey('currency_symbol');
    expect($rules)->toHaveKey('logo');
    expect($rules)->toHaveKey('business_hours');
    expect($rules)->toHaveKey('receipt_settings.header_text');
    expect($rules)->toHaveKey('receipt_footer');
    expect($rules)->toHaveKey('receipt_number_format');
});

it('validates required fields', function () {
    $request = new StoreUpdateRequest();
    $rules = $request->rules();

    expect($rules['name'])->toContain('required');
    expect($rules['email'])->toContain('nullable');
    expect($rules['currency'])->toContain('required');
    expect($rules['currency_symbol'])->toContain('required');
});

it('validates email format', function () {
    $request = new StoreUpdateRequest();
    $rules = $request->rules();

    expect($rules['email'])->toContain('email');
});

it('validates logo file type', function () {
    $request = new StoreUpdateRequest();
    $rules = $request->rules();

    expect($rules['logo'])->toContain('image');
    expect($rules['logo'])->toContain('mimes:jpeg,png,jpg,gif,svg');
    expect($rules['logo'])->toContain('max:2048');
    expect($rules['logo'])->toContain('dimensions:max_width=2000,max_height=2000');
});

it('validates business hours structure', function () {
    $request = new StoreUpdateRequest();
    $rules = $request->rules();

    expect($rules['business_hours'])->toContain('array');
    expect($rules['business_hours.monday'])->toContain('string');
    expect($rules['business_hours.tuesday'])->toContain('string');
    expect($rules['business_hours.wednesday'])->toContain('string');
    expect($rules['business_hours.thursday'])->toContain('string');
    expect($rules['business_hours.friday'])->toContain('string');
    expect($rules['business_hours.saturday'])->toContain('string');
    expect($rules['business_hours.sunday'])->toContain('string');
});

it('validates receipt settings structure', function () {
    $request = new StoreUpdateRequest();
    $rules = $request->rules();

    expect($rules['receipt_settings'])->toContain('array');
    expect($rules['receipt_settings.header_text'])->toContain('string');
    expect($rules['receipt_settings.footer_text'])->toContain('string');
    expect($rules['receipt_settings.show_logo'])->toContain('boolean');
    expect($rules['receipt_settings.show_business_info'])->toContain('boolean');
});

it('has custom error messages', function () {
    $request = new StoreUpdateRequest();
    $messages = $request->messages();

    expect($messages)->toBeArray();
    expect($messages)->toHaveKey('logo.dimensions');
    expect($messages['logo.dimensions'])->toBe('Logo dimensions must not exceed 2000x2000 pixels.');
});

it('authorizes authenticated users', function () {
    $request = new StoreUpdateRequest();
    
    expect($request->authorize())->toBeTrue();
});

it('authorizes users with store update permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $request = new StoreUpdateRequest();
    
    expect($request->authorize())->toBeTrue();
});

it('validates logo dimensions correctly', function () {
    Storage::fake('public');
    
    $validFile = UploadedFile::fake()->image('logo.jpg', 1000, 1000);
    $invalidFile = UploadedFile::fake()->image('logo.jpg', 2500, 2500);
    
    $validData = [
        'name' => 'Test Store',
        'email' => 'test@store.com',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'logo' => $validFile,
    ];
    
    $invalidData = [
        'name' => 'Test Store',
        'email' => 'test@store.com',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'logo' => $invalidFile,
    ];
    
    $request = new StoreUpdateRequest();
    $rules = $request->rules();
    
    $validValidator = Validator::make($validData, $rules);
    $invalidValidator = Validator::make($invalidData, $rules);
    
    expect($validValidator->passes())->toBeTrue();
    expect($invalidValidator->fails())->toBeTrue();
    expect($invalidValidator->errors()->has('logo'))->toBeTrue();
});

it('validates business hours with valid data', function () {
    $validData = [
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
    ];
    
    $request = new StoreUpdateRequest();
    $rules = $request->rules();
    $validator = Validator::make($validData, $rules);
    
    expect($validator->passes())->toBeTrue();
});

it('validates receipt settings with valid data', function () {
    $validData = [
        'name' => 'Test Store',
        'email' => 'test@store.com',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'receipt_settings' => [
            'header_text' => 'Welcome to our store',
            'footer_text' => 'Thank you for your business',
            'show_logo' => true,
            'show_business_info' => true,
        ],
    ];
    
    $request = new StoreUpdateRequest();
    $rules = $request->rules();
    $validator = Validator::make($validData, $rules);
    
    expect($validator->passes())->toBeTrue();
});

it('rejects invalid email format', function () {
    $invalidData = [
        'name' => 'Test Store',
        'email' => 'invalid-email',
        'currency' => 'USD',
        'currency_symbol' => '$',
    ];
    
    $request = new StoreUpdateRequest();
    $rules = $request->rules();
    $validator = Validator::make($invalidData, $rules);
    
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

it('rejects invalid logo file type', function () {
    Storage::fake('public');
    
    $invalidFile = UploadedFile::fake()->create('document.pdf', 100);
    
    $invalidData = [
        'name' => 'Test Store',
        'email' => 'test@store.com',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'logo' => $invalidFile,
    ];
    
    $request = new StoreUpdateRequest();
    $rules = $request->rules();
    $validator = Validator::make($invalidData, $rules);
    
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('logo'))->toBeTrue();
});

it('accepts optional fields as null', function () {
    $dataWithNulls = [
        'name' => 'Test Store',
        'email' => 'test@store.com',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'receipt_number_format' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
        'phone' => null,
        'address' => null,
        'city' => null,
        'state' => null,
        'postal_code' => null,
        'country' => null,
        'business_registration_number' => null,
        'logo' => null,
        'business_hours' => null,
        'receipt_footer' => null,
    ];
    
    $request = new StoreUpdateRequest();
    $rules = $request->rules();
    $validator = Validator::make($dataWithNulls, $rules);
    
    expect($validator->passes())->toBeTrue();
});