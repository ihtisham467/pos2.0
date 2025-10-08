<?php

use App\Http\Controllers\Settings\StoreController;
use App\Models\Store;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->controller = new StoreController();
});

it('has required public methods', function () {
    expect(method_exists($this->controller, 'edit'))->toBeTrue();
    expect(method_exists($this->controller, 'update'))->toBeTrue();
});

it('can be instantiated', function () {
    expect($this->controller)->toBeInstanceOf(StoreController::class);
});

it('has proper method visibility', function () {
    $reflection = new ReflectionClass($this->controller);
    
    // Public methods
    expect($reflection->getMethod('edit')->isPublic())->toBeTrue();
    expect($reflection->getMethod('update')->isPublic())->toBeTrue();
    
    // Private methods
    expect($reflection->getMethod('getAvailableCurrencies')->isPrivate())->toBeTrue();
    expect($reflection->getMethod('getDateFormats')->isPrivate())->toBeTrue();
    expect($reflection->getMethod('getTimeFormats')->isPrivate())->toBeTrue();
    expect($reflection->getMethod('getNumberFormats')->isPrivate())->toBeTrue();
    expect($reflection->getMethod('getReceiptFormats')->isPrivate())->toBeTrue();
});

it('controller extends base controller', function () {
    expect($this->controller)->toBeInstanceOf(\App\Http\Controllers\Controller::class);
});