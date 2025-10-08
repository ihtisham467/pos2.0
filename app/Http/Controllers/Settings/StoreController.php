<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreUpdateRequest;
use App\Models\Store;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    /**
     * Show the store settings page.
     */
    public function edit(): Response
    {
        $store = Store::active() ?? new Store;
        $systemSettings = SystemSetting::getPublicSettings();

        // Force business_hours to be properly initialized as an array
        $defaultBusinessHours = [
            'monday' => '',
            'tuesday' => '',
            'wednesday' => '',
            'thursday' => '',
            'friday' => '',
            'saturday' => '',
            'sunday' => '',
        ];

        // Always ensure we have a proper array structure
        if (empty($store->business_hours) || ! is_array($store->business_hours)) {
            $store->business_hours = $defaultBusinessHours;
        } else {
            // Merge with defaults to ensure all days are present
            $store->business_hours = array_merge($defaultBusinessHours, $store->business_hours);
        }

        return Inertia::render('settings/Store', [
            'store' => $store,
            'systemSettings' => $systemSettings,
            'currencies' => $this->getAvailableCurrencies(),
            'dateFormats' => $this->getDateFormats(),
            'timeFormats' => $this->getTimeFormats(),
            'numberFormats' => $this->getNumberFormats(),
            'receiptFormats' => $this->getReceiptFormats(),
        ]);
    }

    /**
     * Update the store settings.
     */
    public function update(StoreUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Get or create the active store
        $store = Store::active() ?? new Store;
        $oldLogoPath = $store->logo_path;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            $logoPath = $request->file('logo')->store('store-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        $store->fill($data);
        $store->is_active = true;
        $store->save();

        // Update system settings
        if (isset($data['system_settings'])) {
            foreach ($data['system_settings'] as $key => $value) {
                SystemSetting::set($key, $value, 'string', null, true);
            }
        }

        return back()->with('status', 'Store settings updated successfully.');
    }

    /**
     * Get available currencies.
     */
    private function getAvailableCurrencies(): array
    {
        return [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$'],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$'],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF'],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥'],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹'],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$'],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$'],
            ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr'],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr'],
            ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr'],
            ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł'],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽'],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R'],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩'],
        ];
    }

    /**
     * Get available date formats.
     */
    private function getDateFormats(): array
    {
        return [
            ['value' => 'Y-m-d', 'label' => 'YYYY-MM-DD (2024-12-25)'],
            ['value' => 'm/d/Y', 'label' => 'MM/DD/YYYY (12/25/2024)'],
            ['value' => 'd/m/Y', 'label' => 'DD/MM/YYYY (25/12/2024)'],
            ['value' => 'M j, Y', 'label' => 'Month Day, Year (Dec 25, 2024)'],
            ['value' => 'j M Y', 'label' => 'Day Month Year (25 Dec 2024)'],
            ['value' => 'F j, Y', 'label' => 'Full Month Day, Year (December 25, 2024)'],
        ];
    }

    /**
     * Get available time formats.
     */
    private function getTimeFormats(): array
    {
        return [
            ['value' => 'H:i', 'label' => '24-hour (14:30)'],
            ['value' => 'h:i A', 'label' => '12-hour (2:30 PM)'],
            ['value' => 'H:i:s', 'label' => '24-hour with seconds (14:30:45)'],
            ['value' => 'h:i:s A', 'label' => '12-hour with seconds (2:30:45 PM)'],
        ];
    }

    /**
     * Get available number formats.
     */
    private function getNumberFormats(): array
    {
        return [
            ['value' => 'us', 'label' => 'US (1,234.56)'],
            ['value' => 'eu', 'label' => 'European (1.234,56)'],
            ['value' => 'space', 'label' => 'Space (1 234,56)'],
            ['value' => 'none', 'label' => 'No separator (1234.56)'],
        ];
    }

    /**
     * Get available receipt number formats.
     */
    private function getReceiptFormats(): array
    {
        return [
            [
                'value' => 'POS-{YYYY}-{MM}-{DD}-{0000}',
                'label' => 'POS-2024-12-25-0001 (POS-Year-Month-Day-Sequence)',
                'example' => 'POS-'.now()->format('Y-m-d').'-0001',
            ],
            [
                'value' => 'RCP-{YYYY}{MM}{DD}-{0000}',
                'label' => 'RCP-20241225-0001 (RCP-YearMonthDay-Sequence)',
                'example' => 'RCP-'.now()->format('Ymd').'-0001',
            ],
            [
                'value' => 'INV-{0000}',
                'label' => 'INV-0001 (INV-Sequence)',
                'example' => 'INV-0001',
            ],
            [
                'value' => '{YYYY}-{MM}-{DD}-{0000}',
                'label' => '2024-12-25-0001 (Year-Month-Day-Sequence)',
                'example' => now()->format('Y-m-d').'-0001',
            ],
            [
                'value' => 'STORE-{0000}',
                'label' => 'STORE-0001 (STORE-Sequence)',
                'example' => 'STORE-0001',
            ],
            [
                'value' => 'TXN-{YYYY}-{0000}',
                'label' => 'TXN-2024-0001 (TXN-Year-Sequence)',
                'example' => 'TXN-'.now()->format('Y').'-0001',
            ],
        ];
    }
}
