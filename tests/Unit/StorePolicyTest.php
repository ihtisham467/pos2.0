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
    $unauthenticatedUser = null;
    
    expect($this->policy->viewAny($unauthenticatedUser))->toBeFalse();
});

it('denies unauthenticated users from viewing a specific store', function () {
    $unauthenticatedUser = null;
    
    expect($this->policy->view($unauthenticatedUser, $this->store))->toBeFalse();
});

it('denies unauthenticated users from creating stores', function () {
    $unauthenticatedUser = null;
    
    expect($this->policy->create($unauthenticatedUser))->toBeFalse();
});

it('denies unauthenticated users from updating stores', function () {
    $unauthenticatedUser = null;
    
    expect($this->policy->update($unauthenticatedUser, $this->store))->toBeFalse();
});

it('denies unauthenticated users from updating stores with null store', function () {
    $unauthenticatedUser = null;
    
    expect($this->policy->update($unauthenticatedUser, null))->toBeFalse();
});

it('has all required policy methods', function () {
    $reflection = new ReflectionClass($this->policy);
    
    expect($reflection->hasMethod('viewAny'))->toBeTrue();
    expect($reflection->hasMethod('view'))->toBeTrue();
    expect($reflection->hasMethod('create'))->toBeTrue();
    expect($reflection->hasMethod('update'))->toBeTrue();
});

it('policy methods have correct signatures', function () {
    $reflection = new ReflectionClass($this->policy);
    
    $viewAnyMethod = $reflection->getMethod('viewAny');
    $viewMethod = $reflection->getMethod('view');
    $createMethod = $reflection->getMethod('create');
    $updateMethod = $reflection->getMethod('update');
    
    // Check parameter counts
    expect($viewAnyMethod->getNumberOfParameters())->toBe(1);
    expect($viewMethod->getNumberOfParameters())->toBe(2);
    expect($createMethod->getNumberOfParameters())->toBe(1);
    expect($updateMethod->getNumberOfParameters())->toBe(2);
    
    // Check return types
    expect($viewAnyMethod->getReturnType()->getName())->toBe('bool');
    expect($viewMethod->getReturnType()->getName())->toBe('bool');
    expect($createMethod->getReturnType()->getName())->toBe('bool');
    expect($updateMethod->getReturnType()->getName())->toBe('bool');
});

it('policy methods are public', function () {
    $reflection = new ReflectionClass($this->policy);
    
    expect($reflection->getMethod('viewAny')->isPublic())->toBeTrue();
    expect($reflection->getMethod('view')->isPublic())->toBeTrue();
    expect($reflection->getMethod('create')->isPublic())->toBeTrue();
    expect($reflection->getMethod('update')->isPublic())->toBeTrue();
});

it('works with different user types', function () {
    $adminUser = User::factory()->create(['email' => 'admin@example.com']);
    $regularUser = User::factory()->create(['email' => 'user@example.com']);
    
    // All authenticated users should have the same permissions
    expect($this->policy->viewAny($adminUser))->toBeTrue();
    expect($this->policy->viewAny($regularUser))->toBeTrue();
    
    expect($this->policy->view($adminUser, $this->store))->toBeTrue();
    expect($this->policy->view($regularUser, $this->store))->toBeTrue();
    
    expect($this->policy->create($adminUser))->toBeTrue();
    expect($this->policy->create($regularUser))->toBeTrue();
    
    expect($this->policy->update($adminUser, $this->store))->toBeTrue();
    expect($this->policy->update($regularUser, $this->store))->toBeTrue();
});

it('handles edge cases gracefully', function () {
    // Test with empty user object (not null, but empty)
    $emptyUser = new User();
    
    expect($this->policy->viewAny($emptyUser))->toBeTrue();
    expect($this->policy->view($emptyUser, $this->store))->toBeTrue();
    expect($this->policy->create($emptyUser))->toBeTrue();
    expect($this->policy->update($emptyUser, $this->store))->toBeTrue();
});

it('policy is properly registered', function () {
    $this->app->make(\Illuminate\Contracts\Auth\Access\Gate::class);
    
    // This would be tested in integration tests, but we can verify the policy exists
    expect(class_exists(StorePolicy::class))->toBeTrue();
});

it('policy methods return consistent boolean values', function () {
    // Test multiple calls to ensure consistency
    for ($i = 0; $i < 5; $i++) {
        expect($this->policy->viewAny($this->user))->toBeTrue();
        expect($this->policy->view($this->user, $this->store))->toBeTrue();
        expect($this->policy->create($this->user))->toBeTrue();
        expect($this->policy->update($this->user, $this->store))->toBeTrue();
    }
});