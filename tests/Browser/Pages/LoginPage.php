<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LoginPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs('/login');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@email' => 'input[name="email"]',
            '@password' => 'input[name="password"]',
            '@login-button' => 'button[type="submit"]',
            '@remember-me' => 'input[name="remember"]',
        ];
    }

    /**
     * Login with the given credentials.
     */
    public function login(Browser $browser, string $email, string $password): void
    {
        $browser->type('@email', $email)
                ->type('@password', $password)
                ->click('@login-button');
    }
}
