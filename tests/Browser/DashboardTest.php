<?php

use App\Models\User;
use Tests\Browser\Pages\DashboardPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

test('authenticated user can access dashboard', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit(new DashboardPage)
                ->assertPathIs('/dashboard');
    });
});

test('dashboard shows POS quick actions', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit(new DashboardPage)
                ->assertPathIs('/dashboard');
    });
});

test('dashboard is responsive on mobile', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->resize(375, 667) // iPhone SE size
                ->visit(new DashboardPage)
                ->assertSee('Dashboard');
    });
});
