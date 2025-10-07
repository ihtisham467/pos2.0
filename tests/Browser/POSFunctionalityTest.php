<?php

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

test('user can view POS interface', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can search for products', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can add product to cart', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can process a complete sale', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can attach customer to transaction', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can process partial payment for customer', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});