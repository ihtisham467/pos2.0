<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'business_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'business_registration_number' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'business_hours' => ['required', 'array'],
            'business_hours.*.open' => ['required_with:business_hours.*.closed,false', 'date_format:H:i'],
            'business_hours.*.close' => ['required_with:business_hours.*.closed,false', 'date_format:H:i', 'after:business_hours.*.open'],
            'business_hours.*.closed' => ['required', 'boolean'],
            'currency' => ['required', 'string', 'in:USD,EUR,GBP,JPY,CAD,AUD,CHF,CNY,INR,PKR,BRL,MXN,KRW,SGD,HKD,NZD'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'receipt_settings' => ['nullable', 'array'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
            'receipt_number_format' => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_hours.*.close.after' => 'The closing time must be after the opening time.',
            'logo.max' => 'The logo file size must not exceed 2MB.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, gif.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values for business hours if not provided
        if (!$this->has('business_hours')) {
            $this->merge([
                'business_hours' => [
                    'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                    'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                    'wednesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                    'thursday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                    'friday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
                    'saturday' => ['open' => '10:00', 'close' => '16:00', 'closed' => false],
                    'sunday' => ['open' => '10:00', 'close' => '16:00', 'closed' => true],
                ]
            ]);
        }

        // Set is_active to true by default
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
