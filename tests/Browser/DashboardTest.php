<?php

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

test('dashboard is responsive on mobile', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->resize(375, 667) // iPhone SE size
                ->visit('/dashboard')
                ->assertPathIs('/dashboard');
    });
});
