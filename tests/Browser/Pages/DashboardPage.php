<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class DashboardPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/dashboard';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs('/dashboard');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@dashboard-title' => 'h1',
            '@pos-section' => '[data-testid="pos-section"]',
            '@quick-actions' => '[data-testid="quick-actions"]',
            '@recent-transactions' => '[data-testid="recent-transactions"]',
        ];
    }
}
