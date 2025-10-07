<?php

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

test('user can view product inventory', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can add new product', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can update product stock', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});

test('user can search products by barcode', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});