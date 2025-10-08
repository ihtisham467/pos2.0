<?php

use App\Models\User;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

uses(RefreshDatabase::class);

class StoreSettingsBrowserTest extends DuskTestCase
{
    public function test_can_view_store_settings_page(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->assertSee('Store Settings')
                ->assertSee('Store Information')
                ->assertSee('Business Hours')
                ->assertSee('Currency Settings')
                ->assertSee('Receipt Configuration')
                ->assertSee('Save Settings');
        });
    }

    public function test_can_fill_and_submit_store_form(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->type('name', 'Test Store')
                ->type('email', 'test@store.com')
                ->type('phone', '+1234567890')
                ->type('address', '123 Test Street')
                ->select('currency', 'USD')
                ->type('currency_symbol', '$')
                ->select('receipt_number_format', 'POS-{YYYY}-{MM}-{DD}-{0000}')
                ->press('Save Settings')
                ->assertPathIs('/settings/store')
                ->assertSee('Saved.');
        });
    }

    public function test_can_fill_business_hours(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->type('business_hours.monday', '9:00 AM - 6:00 PM')
                ->type('business_hours.tuesday', '9:00 AM - 6:00 PM')
                ->type('business_hours.wednesday', '9:00 AM - 6:00 PM')
                ->type('business_hours.thursday', '9:00 AM - 6:00 PM')
                ->type('business_hours.friday', '9:00 AM - 6:00 PM')
                ->type('business_hours.saturday', '10:00 AM - 4:00 PM')
                ->type('business_hours.sunday', 'Closed')
                ->press('Save Settings')
                ->assertPathIs('/settings/store')
                ->assertSee('Saved.');
        });
    }

    public function test_can_fill_receipt_settings(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->type('receipt_settings.header_text', 'Welcome to our store')
                ->type('receipt_footer', 'Thank you for your business')
                ->press('Save Settings')
                ->assertPathIs('/settings/store')
                ->assertSee('Saved.');
        });
    }

    public function test_can_upload_logo(): void
    {
        $user = User::factory()->create();

        // Create a dummy image file
        $dummyImagePath = base_path('tests/Browser/Fixtures/dummy-logo.png');
        if (!file_exists(dirname($dummyImagePath))) {
            mkdir(dirname($dummyImagePath), 0777, true);
        }
        file_put_contents($dummyImagePath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));

        $this->browse(function (Browser $browser) use ($user, $dummyImagePath) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->attach('logo', $dummyImagePath)
                ->press('Save Settings')
                ->assertPathIs('/settings/store')
                ->assertSee('Saved.');
        });
    }

    public function test_can_select_different_currencies(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->select('currency', 'EUR')
                ->assertInputValue('currency_symbol', '€')
                ->select('currency', 'GBP')
                ->assertInputValue('currency_symbol', '£')
                ->select('currency', 'USD')
                ->assertInputValue('currency_symbol', '$');
        });
    }

    public function test_can_select_different_receipt_formats(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->select('receipt_number_format', 'INV-{0000}')
                ->assertSee('INV-0001')
                ->select('receipt_number_format', 'RCP-{YYYY}{MM}{DD}-{0000}')
                ->assertSee('RCP-20241225-0001');
        });
    }

    public function test_shows_validation_errors_for_required_fields(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->clear('name')
                ->clear('currency')
                ->clear('currency_symbol')
                ->clear('receipt_number_format')
                ->press('Save Settings')
                ->assertSee('Store name is required')
                ->assertSee('Currency selection is required')
                ->assertSee('Currency symbol is required')
                ->assertSee('Receipt number format is required');
        });
    }

    public function test_shows_validation_error_for_invalid_email(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->type('email', 'invalid-email')
                ->press('Save Settings')
                ->assertSee('Please enter a valid email address');
        });
    }

    public function test_shows_validation_error_for_invalid_logo_file(): void
    {
        $user = User::factory()->create();

        // Create a dummy PDF file
        $dummyPdfPath = base_path('tests/Browser/Fixtures/document.pdf');
        if (!file_exists(dirname($dummyPdfPath))) {
            mkdir(dirname($dummyPdfPath), 0777, true);
        }
        file_put_contents($dummyPdfPath, '%PDF-1.4 dummy content');

        $this->browse(function (Browser $browser) use ($user, $dummyPdfPath) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->attach('logo', $dummyPdfPath)
                ->press('Save Settings')
                ->assertSee('Logo must be an image file');
        });
    }

    public function test_can_update_existing_store_settings(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create([
            'name' => 'Original Store',
            'email' => 'original@store.com',
            'currency' => 'USD',
            'currency_symbol' => '$',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->assertInputValue('name', 'Original Store')
                ->assertInputValue('email', 'original@store.com')
                ->assertInputValue('currency', 'USD')
                ->assertInputValue('currency_symbol', '$')
                ->clear('name')
                ->type('name', 'Updated Store')
                ->clear('email')
                ->type('email', 'updated@store.com')
                ->select('currency', 'EUR')
                ->press('Save Settings')
                ->assertPathIs('/settings/store')
                ->assertSee('Saved.')
                ->assertInputValue('name', 'Updated Store')
                ->assertInputValue('email', 'updated@store.com')
                ->assertInputValue('currency', 'EUR')
                ->assertInputValue('currency_symbol', '€');
        });
    }

    public function test_business_hours_inputs_are_empty_by_default(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->assertInputValue('business_hours.monday', '')
                ->assertInputValue('business_hours.tuesday', '')
                ->assertInputValue('business_hours.wednesday', '')
                ->assertInputValue('business_hours.thursday', '')
                ->assertInputValue('business_hours.friday', '')
                ->assertInputValue('business_hours.saturday', '')
                ->assertInputValue('business_hours.sunday', '');
        });
    }

    public function test_receipt_settings_checkboxes_work_correctly(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create([
            'receipt_settings' => [
                'show_logo' => true,
                'show_business_info' => true,
                'show_customer_info' => false,
            ],
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->assertChecked('receipt_settings.show_logo')
                ->assertChecked('receipt_settings.show_business_info')
                ->assertNotChecked('receipt_settings.show_customer_info')
                ->uncheck('receipt_settings.show_logo')
                ->uncheck('receipt_settings.show_business_info')
                ->check('receipt_settings.show_customer_info')
                ->assertNotChecked('receipt_settings.show_logo')
                ->assertNotChecked('receipt_settings.show_business_info')
                ->assertChecked('receipt_settings.show_customer_info');
        });
    }

    public function test_form_resets_after_successful_submission(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/store')
                ->type('name', 'Test Store')
                ->type('email', 'test@store.com')
                ->select('currency', 'USD')
                ->type('currency_symbol', '$')
                ->press('Save Settings')
                ->assertPathIs('/settings/store')
                ->assertSee('Saved.')
                ->assertInputValue('name', 'Test Store')
                ->assertInputValue('email', 'test@store.com');
        });
    }

    public function test_requires_authentication(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/settings/store')
                ->assertPathIs('/login');
        });
    }
}