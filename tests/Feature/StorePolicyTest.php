<?php

use App\Models\Store;
use App\Models\User;
use App\Policies\StorePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new StorePolicy();
    $this->user = User::factory()->create();
    $this->store = Store::factory()->create();
});

it('allows authenticated users to view any store', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows authenticated users to view a specific store', function () {
    expect($this->policy->view($this->user, $this->store))->toBeTrue();
});

it('allows authenticated users to create stores', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

it('allows authenticated users to update stores', function () {
    expect($this->policy->update($this->user, $this->store))->toBeTrue();
});

it('allows authenticated users to update stores with null store', function () {
    expect($this->policy->update($this->user, null))->toBeTrue();
});

it('denies unauthenticated users from viewing any store', function () {
    expect($this->policy->viewAny(null))->toBeFalse();
});

it('denies unauthenticated users from viewing a specific store', function () {
    expect($this->policy->view(null, $this->store))->toBeFalse();
});

it('denies unauthenticated users from creating stores', function () {
    expect($this->policy->create(null))->toBeFalse();
});

it('denies unauthenticated users from updating stores', function () {
    expect($this->policy->update(null, $this->store))->toBeFalse();
});

it('denies unauthenticated users from updating stores with null store', function () {
    expect($this->policy->update(null, null))->toBeFalse();
});

it('has all required policy methods', function () {
    $reflection = new ReflectionClass(StorePolicy::class);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(fn($method) => $method->getName(), $methods);
    
    expect($methodNames)->toContain('viewAny');
    expect($methodNames)->toContain('view');
    expect($methodNames)->toContain('create');
    expect($methodNames)->toContain('update');
});

it('policy methods have correct signatures', function () {
    $reflection = new ReflectionClass(StorePolicy::class);
    
    $viewAnyMethod = $reflection->getMethod('viewAny');
    expect($viewAnyMethod->getNumberOfParameters())->toBe(1);
    
    $viewMethod = $reflection->getMethod('view');
    expect($viewMethod->getNumberOfParameters())->toBe(2);
    
    $createMethod = $reflection->getMethod('create');
    expect($createMethod->getNumberOfParameters())->toBe(1);
    
    $updateMethod = $reflection->getMethod('update');
    expect($updateMethod->getNumberOfParameters())->toBe(2);
});

it('policy methods are public', function () {
    $reflection = new ReflectionClass(StorePolicy::class);
    
    expect($reflection->getMethod('viewAny')->isPublic())->toBeTrue();
    expect($reflection->getMethod('view')->isPublic())->toBeTrue();
    expect($reflection->getMethod('create')->isPublic())->toBeTrue();
    expect($reflection->getMethod('update')->isPublic())->toBeTrue();
});

it('works with different user types', function () {
    $adminUser = User::factory()->create(['email' => 'admin@example.com']);
    $regularUser = User::factory()->create(['email' => 'user@example.com']);
    
    expect($this->policy->viewAny($adminUser))->toBeTrue();
    expect($this->policy->viewAny($regularUser))->toBeTrue();
    expect($this->policy->create($adminUser))->toBeTrue();
    expect($this->policy->create($regularUser))->toBeTrue();
    expect($this->policy->update($adminUser, $this->store))->toBeTrue();
    expect($this->policy->update($regularUser, $this->store))->toBeTrue();
});

it('handles edge cases gracefully', function () {
    // Test with empty store
    $emptyStore = new Store();
    expect($this->policy->view($this->user, $emptyStore))->toBeTrue();
    expect($this->policy->update($this->user, $emptyStore))->toBeTrue();
    
    // Test with null user
    expect($this->policy->view(null, $this->store))->toBeFalse();
    expect($this->policy->update(null, null))->toBeFalse();
});

it('policy is properly registered', function () {
    $policies = app('Illuminate\Contracts\Auth\Access\Gate')->policies();
    expect($policies)->toHaveKey(Store::class);
    expect($policies[Store::class])->toBe(StorePolicy::class);
});

it('policy methods return consistent boolean values', function () {
    // Test multiple calls return same result
    $result1 = $this->policy->viewAny($this->user);
    $result2 = $this->policy->viewAny($this->user);
    expect($result1)->toBe($result2);
    
    $result3 = $this->policy->update($this->user, $this->store);
    $result4 = $this->policy->update($this->user, $this->store);
    expect($result3)->toBe($result4);
});
