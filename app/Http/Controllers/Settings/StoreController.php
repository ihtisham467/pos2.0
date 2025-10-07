<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreUpdateRequest;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    /**
     * Show the store settings page.
     */
    public function edit(Request $request): Response
    {
        $store = Store::active() ?? new Store();
        
        return Inertia::render('settings/Store', [
            'store' => $store,
            'currencies' => $this->getCurrencies(),
            'receiptNumberFormats' => $this->getReceiptNumberFormats(),
            'businessHours' => $this->getBusinessHours(),
        ]);
    }

    /**
     * Update the store settings.
     */
    public function update(StoreUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('store/logos', 'public');
            $validated['logo_path'] = $logoPath;
        }
        
        // Ensure only one store is active
        if ($validated['is_active'] ?? false) {
            Store::where('is_active', true)->update(['is_active' => false]);
        }
        
        $store = Store::active();
        
        if ($store) {
            $store->update($validated);
        } else {
            $validated['is_active'] = true;
            Store::create($validated);
        }

        return to_route('store.edit')->with('status', 'Store settings updated successfully.');
    }

    /**
     * Get available currencies.
     */
    private function getCurrencies(): array
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
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨'],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$'],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$'],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$'],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$'],
        ];
    }

    /**
     * Get available receipt number formats.
     */
    private function getReceiptNumberFormats(): array
    {
        return [
            ['value' => 'POS-{YYYY}-{MM}-{DD}-{0000}', 'label' => 'POS-2024-01-15-0001'],
            ['value' => 'INV-{YYYY}{MM}{DD}-{0000}', 'label' => 'INV-20240115-0001'],
            ['value' => 'RCP-{0000}', 'label' => 'RCP-0001'],
            ['value' => 'SALE-{YYYY}-{0000}', 'label' => 'SALE-2024-0001'],
            ['value' => 'TXN-{MM}{DD}-{0000}', 'label' => 'TXN-0115-0001'],
        ];
    }

    /**
     * Get business hours structure.
     */
    private function getBusinessHours(): array
    {
        return [
            'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'wednesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'thursday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'friday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
            'saturday' => ['open' => '10:00', 'close' => '16:00', 'closed' => false],
            'sunday' => ['open' => '10:00', 'close' => '16:00', 'closed' => true],
        ];
    }
}
