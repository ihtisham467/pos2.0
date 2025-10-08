<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update', \App\Models\Store::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'business_registration_number' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
            'business_hours' => ['nullable', 'array'],
            'business_hours.monday' => ['nullable', 'string'],
            'business_hours.tuesday' => ['nullable', 'string'],
            'business_hours.wednesday' => ['nullable', 'string'],
            'business_hours.thursday' => ['nullable', 'string'],
            'business_hours.friday' => ['nullable', 'string'],
            'business_hours.saturday' => ['nullable', 'string'],
            'business_hours.sunday' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'receipt_settings' => ['nullable', 'array'],
            'receipt_settings.header_text' => ['nullable', 'string', 'max:500'],
            'receipt_settings.footer_text' => ['nullable', 'string', 'max:500'],
            'receipt_settings.show_logo' => ['nullable', 'boolean'],
            'receipt_settings.show_business_info' => ['nullable', 'boolean'],
            'receipt_settings.show_customer_info' => ['nullable', 'boolean'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
            'receipt_number_format' => ['required', 'string', 'max:100'],
            'system_settings' => ['nullable', 'array'],
            'system_settings.date_format' => ['nullable', 'string'],
            'system_settings.time_format' => ['nullable', 'string'],
            'system_settings.number_format' => ['nullable', 'string'],
            'system_settings.theme' => ['nullable', 'string', Rule::in(['light', 'dark', 'system'])],
            'system_settings.language' => ['nullable', 'string', Rule::in(['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ko'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Store name is required.',
            'currency.required' => 'Currency selection is required.',
            'currency_symbol.required' => 'Currency symbol is required.',
            'receipt_number_format.required' => 'Receipt number format is required.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'Logo may not be greater than 2MB.',
            'logo.dimensions' => 'Logo dimensions must not exceed 2000x2000 pixels.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }
}
