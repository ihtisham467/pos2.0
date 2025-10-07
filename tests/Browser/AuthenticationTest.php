<?php

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

test('user can login successfully', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->pause(1000); // Wait for form submission
    });
});

test('user cannot login with invalid credentials', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
                ->type('email', 'invalid@example.com')
                ->type('password', 'wrong-password')
                ->press('button[type="submit"]')
                ->assertPathIs('/login');
    });
});

test('user is redirected to login when not authenticated', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/dashboard')
                ->assertPathIs('/login');
    });
});

test('authenticated user can access dashboard', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});
