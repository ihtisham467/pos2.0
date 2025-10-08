<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
// Textarea will be replaced with HTML textarea
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { update as updateStore } from '@/routes/store/index';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Store {
    id?: number;
    name: string;
    business_name?: string;
    address?: string;
    phone?: string;
    email?: string;
    business_registration_number?: string;
    logo_path?: string;
    business_hours?: Record<string, string>;
    currency: string;
    currency_symbol: string;
    receipt_settings?: Record<string, any>;
    receipt_footer?: string;
    receipt_number_format: string;
}

interface Currency {
    code: string;
    name: string;
    symbol: string;
}

interface FormatOption {
    value: string;
    label: string;
}

interface ReceiptFormat {
    value: string;
    label: string;
    example: string;
}

interface Props {
    store: Store;
    systemSettings: Record<string, any>;
    currencies: Currency[];
    dateFormats: FormatOption[];
    timeFormats: FormatOption[];
    numberFormats: FormatOption[];
    receiptFormats: ReceiptFormat[];
}

const props = defineProps<Props>();

const logoPreview = ref<string | null>(props.store.logo_path || null);

// Form data for proper binding
const formData = ref({
    name: props.store.name || '',
    business_name: props.store.business_name || '',
    address: props.store.address || '',
    phone: props.store.phone || '',
    email: props.store.email || '',
    business_registration_number: props.store.business_registration_number || '',
    currency: props.store.currency || '',
    currency_symbol: props.store.currency_symbol || '',
    receipt_number_format: props.store.receipt_number_format || 'POS-{YYYY}-{MM}-{DD}-{0000}',
    business_hours: {
        monday: '',
        tuesday: '',
        wednesday: '',
        thursday: '',
        friday: '',
        saturday: '',
        sunday: '',
    },
    receipt_settings: {
        header_text: props.store.receipt_settings?.header_text || '',
        footer_text: props.store.receipt_settings?.footer_text || '',
        show_logo: props.store.receipt_settings?.show_logo ?? true,
        show_business_info: props.store.receipt_settings?.show_business_info ?? true,
        show_customer_info: props.store.receipt_settings?.show_customer_info ?? true,
    },
    receipt_footer: props.store.receipt_footer || '',
    system_settings: {
        date_format: props.systemSettings.date_format || 'Y-m-d',
        time_format: props.systemSettings.time_format || 'H:i',
        number_format: props.systemSettings.number_format || 'us',
        theme: props.systemSettings.theme || 'light',
        language: props.systemSettings.language || 'en',
    },
});

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Store settings',
        href: updateStore.url(),
    },
];

// Form data will be handled by Inertia Form component

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const handleCurrencyChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const currencyCode = target.value;
    const currency = props.currencies.find(c => c.code === currencyCode);
    if (currency) {
        formData.value.currency_symbol = currency.symbol;
    }
};

// Initialize business hours data properly on mount
const initializeBusinessHours = () => {
    const defaultHours = {
        monday: '',
        tuesday: '',
        wednesday: '',
        thursday: '',
        friday: '',
        saturday: '',
        sunday: '',
    };

    // Always start with defaults
    formData.value.business_hours = { ...defaultHours };

    // If we have valid business hours data, merge it
    if (props.store.business_hours && typeof props.store.business_hours === 'object') {
        Object.keys(defaultHours).forEach(day => {
            const value = props.store.business_hours[day];
            if (value !== null && value !== undefined) {
                formData.value.business_hours[day] = String(value);
            }
        });
    }
};

// Initialize on component mount
initializeBusinessHours();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Store settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Store Configuration"
                    description="Configure your store's information, currency, and system preferences"
                />

                <Form
                    :action="updateStore.url()"
                    method="patch"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
            <!-- Store Information -->
            <Card>
                <CardHeader>
                    <CardTitle>Store Information</CardTitle>
                    <CardDescription>
                        Configure your store's basic information and contact details.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="name">Store Name *</Label>
                            <Input
                                id="name"
                                name="name"
                                v-model="formData.name"
                                placeholder="Enter store name"
                                required
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="business_name">Business Name</Label>
                            <Input
                                id="business_name"
                                name="business_name"
                                v-model="formData.business_name"
                                placeholder="Enter business name"
                            />
                            <InputError :message="errors.business_name" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="address">Address</Label>
                        <textarea
                            id="address"
                            name="address"
                            v-model="formData.address"
                            placeholder="Enter store address"
                            rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <InputError :message="errors.address" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="phone">Phone</Label>
                            <Input
                                id="phone"
                                name="phone"
                                v-model="formData.phone"
                                placeholder="Enter phone number"
                            />
                            <InputError :message="errors.phone" />
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                name="email"
                                type="email"
                                v-model="formData.email"
                                placeholder="Enter email address"
                            />
                            <InputError :message="errors.email" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="business_registration_number">Business Registration Number</Label>
                        <Input
                            id="business_registration_number"
                            name="business_registration_number"
                            v-model="formData.business_registration_number"
                            placeholder="Enter registration number"
                        />
                        <InputError :message="errors.business_registration_number" />
                    </div>

                    <!-- Logo Upload -->
                    <div class="space-y-4">
                        <Label>Store Logo</Label>
                        <div class="flex items-center space-x-4">
                            <div v-if="logoPreview" class="w-20 h-20 border rounded-lg overflow-hidden">
                                <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover" />
                            </div>
                            <div class="space-y-2">
                                <Input
                                    type="file"
                                    name="logo"
                                    accept="image/*"
                                    @change="handleLogoChange"
                                    class="file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/80"
                                />
                                <p class="text-sm text-muted-foreground">
                                    Upload a logo for your store. Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF, SVG
                                </p>
                            </div>
                        </div>
                        <InputError :message="errors.logo" />
                    </div>
                </CardContent>
            </Card>

            <!-- Business Hours -->
            <Card>
                <CardHeader>
                    <CardTitle>Business Hours</CardTitle>
                    <CardDescription>
                        Set your store's operating hours for each day of the week.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="business_hours.monday" class="capitalize">Monday</Label>
                            <Input
                                id="business_hours.monday"
                                name="business_hours[monday]"
                                v-model="formData.business_hours.monday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="business_hours.tuesday" class="capitalize">Tuesday</Label>
                            <Input
                                id="business_hours.tuesday"
                                name="business_hours[tuesday]"
                                v-model="formData.business_hours.tuesday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="business_hours.wednesday" class="capitalize">Wednesday</Label>
                            <Input
                                id="business_hours.wednesday"
                                name="business_hours[wednesday]"
                                v-model="formData.business_hours.wednesday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="business_hours.thursday" class="capitalize">Thursday</Label>
                            <Input
                                id="business_hours.thursday"
                                name="business_hours[thursday]"
                                v-model="formData.business_hours.thursday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="business_hours.friday" class="capitalize">Friday</Label>
                            <Input
                                id="business_hours.friday"
                                name="business_hours[friday]"
                                v-model="formData.business_hours.friday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="business_hours.saturday" class="capitalize">Saturday</Label>
                            <Input
                                id="business_hours.saturday"
                                name="business_hours[saturday]"
                                v-model="formData.business_hours.saturday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="business_hours.sunday" class="capitalize">Sunday</Label>
                            <Input
                                id="business_hours.sunday"
                                name="business_hours[sunday]"
                                v-model="formData.business_hours.sunday"
                                placeholder="e.g., 9:00 AM - 6:00 PM"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Currency Settings -->
            <Card>
                <CardHeader>
                    <CardTitle>Currency Settings</CardTitle>
                    <CardDescription>
                        Configure the currency used for transactions and pricing.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="currency">Currency *</Label>
                            <select
                                id="currency"
                                name="currency"
                                v-model="formData.currency"
                                @change="handleCurrencyChange"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Select currency</option>
                                <option
                                    v-for="currency in currencies"
                                    :key="currency.code"
                                    :value="currency.code"
                                >
                                    {{ currency.name }} ({{ currency.symbol }})
                                </option>
                            </select>
                            <InputError :message="errors.currency" />
                        </div>

                        <div class="space-y-2">
                            <Label for="currency_symbol">Currency Symbol *</Label>
                            <Input
                                id="currency_symbol"
                                name="currency_symbol"
                                v-model="formData.currency_symbol"
                                placeholder="Enter currency symbol"
                                required
                            />
                            <InputError :message="errors.currency_symbol" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Receipt Configuration -->
            <Card>
                <CardHeader>
                    <CardTitle>Receipt Configuration</CardTitle>
                    <CardDescription>
                        Customize how receipts are generated and displayed.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="space-y-2">
                        <Label for="receipt_number_format">Receipt Number Format *</Label>
                        <select
                            id="receipt_number_format"
                            name="receipt_number_format"
                            v-model="formData.receipt_number_format"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option value="">Select receipt format</option>
                            <option
                                v-for="format in receiptFormats"
                                :key="format.value"
                                :value="format.value"
                            >
                                {{ format.label }}
                            </option>
                        </select>
                        <div v-if="formData.receipt_number_format" class="text-sm text-muted-foreground">
                            <strong>Example:</strong> {{ receiptFormats.find(f => f.value === formData.receipt_number_format)?.example }}
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Use placeholders: {YYYY} for year, {MM} for month, {DD} for day, {0000} for sequence number
                        </p>
                        <InputError :message="errors.receipt_number_format" />
                    </div>

                    <div class="space-y-2">
                        <Label for="receipt_settings.header_text">Receipt Header Text</Label>
                        <textarea
                            id="receipt_settings.header_text"
                            name="receipt_settings[header_text]"
                            v-model="formData.receipt_settings.header_text"
                            placeholder="Enter header text for receipts"
                            rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <InputError :message="errors['receipt_settings.header_text']" />
                    </div>

                    <div class="space-y-2">
                        <Label for="receipt_footer">Receipt Footer Text</Label>
                        <textarea
                            id="receipt_footer"
                            name="receipt_footer"
                            v-model="formData.receipt_footer"
                            placeholder="Enter footer text for receipts"
                            rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <InputError :message="errors.receipt_footer" />
                    </div>

                    <div class="space-y-4">
                        <Label>Receipt Display Options</Label>
                        <div class="space-y-3">
                            <div class="flex flex-row items-center justify-between rounded-lg border p-4">
                                <div class="space-y-0.5">
                                    <Label class="text-base">Show Logo on Receipt</Label>
                                    <p class="text-sm text-muted-foreground">
                                        Display the store logo on printed receipts
                                    </p>
                                </div>
                                <Checkbox
                                    name="receipt_settings[show_logo]"
                                    v-model:checked="formData.receipt_settings.show_logo"
                                />
                            </div>

                            <div class="flex flex-row items-center justify-between rounded-lg border p-4">
                                <div class="space-y-0.5">
                                    <Label class="text-base">Show Business Information</Label>
                                    <p class="text-sm text-muted-foreground">
                                        Display store name, address, and contact info on receipts
                                    </p>
                                </div>
                                <Checkbox
                                    name="receipt_settings[show_business_info]"
                                    v-model:checked="formData.receipt_settings.show_business_info"
                                />
                            </div>

                            <div class="flex flex-row items-center justify-between rounded-lg border p-4">
                                <div class="space-y-0.5">
                                    <Label class="text-base">Show Customer Information</Label>
                                    <p class="text-sm text-muted-foreground">
                                        Display customer details on receipts when applicable
                                    </p>
                                </div>
                                <Checkbox
                                    name="receipt_settings[show_customer_info]"
                                    v-model:checked="formData.receipt_settings.show_customer_info"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- System Preferences -->
            <Card>
                <CardHeader>
                    <CardTitle>System Preferences</CardTitle>
                    <CardDescription>
                        Configure date, time, number formats, and other system preferences.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="system_settings.date_format">Date Format</Label>
                            <select
                                id="system_settings.date_format"
                                name="system_settings[date_format]"
                                v-model="formData.system_settings.date_format"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option
                                    v-for="format in dateFormats"
                                    :key="format.value"
                                    :value="format.value"
                                >
                                    {{ format.label }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="system_settings.time_format">Time Format</Label>
                            <select
                                id="system_settings.time_format"
                                name="system_settings[time_format]"
                                v-model="formData.system_settings.time_format"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option
                                    v-for="format in timeFormats"
                                    :key="format.value"
                                    :value="format.value"
                                >
                                    {{ format.label }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="system_settings.number_format">Number Format</Label>
                            <select
                                id="system_settings.number_format"
                                name="system_settings[number_format]"
                                v-model="formData.system_settings.number_format"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option
                                    v-for="format in numberFormats"
                                    :key="format.value"
                                    :value="format.value"
                                >
                                    {{ format.label }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="system_settings.theme">Theme</Label>
                            <select
                                id="system_settings.theme"
                                name="system_settings[theme]"
                                v-model="formData.system_settings.theme"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="light">Light</option>
                                <option value="dark">Dark</option>
                                <option value="system">System</option>
                            </select>
                        </div>
                    </div>
                </CardContent>
            </Card>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <Button type="submit" :disabled="processing">
                            {{ processing ? 'Saving...' : 'Save Settings' }}
                        </Button>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
