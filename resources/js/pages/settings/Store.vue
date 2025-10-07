<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import StoreController from '@/actions/App/Http/Controllers/Settings/StoreController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Upload, X } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Currency {
    code: string;
    name: string;
    symbol: string;
}

interface ReceiptNumberFormat {
    value: string;
    label: string;
}

interface BusinessHours {
    [key: string]: {
        open: string;
        close: string;
        closed: boolean;
    };
}

interface Props {
    store: {
        id?: number;
        name: string;
        business_name: string;
        address: string;
        phone: string;
        email: string;
        business_registration_number?: string;
        logo_path?: string;
        business_hours: BusinessHours;
        currency: string;
        currency_symbol: string;
        receipt_settings?: any;
        receipt_footer?: string;
        receipt_number_format: string;
        is_active: boolean;
    };
    currencies: Currency[];
    receiptNumberFormats: ReceiptNumberFormat[];
    businessHours: BusinessHours;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Store settings',
        href: '/settings/store',
    },
];

const logoFile = ref<File | null>(null);
const logoPreview = ref<string | null>(props.store.logo_path || null);

const selectedCurrency = ref(props.store.currency || 'USD');
const selectedCurrencySymbol = computed(() => {
    const currency = props.currencies.find(c => c.code === selectedCurrency.value);
    return currency?.symbol || '$';
});

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        logoFile.value = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const removeLogo = () => {
    logoFile.value = null;
    logoPreview.value = null;
};

const businessHours = ref<BusinessHours>(props.store.business_hours || props.businessHours);

// Make store data reactive
const form = useForm({
    name: props.store.name || '',
    business_name: props.store.business_name || '',
    address: props.store.address || '',
    phone: props.store.phone || '',
    email: props.store.email || '',
    business_registration_number: props.store.business_registration_number || '',
    receipt_footer: props.store.receipt_footer || '',
    receipt_number_format: props.store.receipt_number_format || 'POS-{YYYY}-{MM}-{DD}-{0000}',
    is_active: props.store.is_active ?? true,
    business_hours: businessHours.value,
    currency: selectedCurrency.value,
    currency_symbol: selectedCurrencySymbol.value,
});

const submitForm = () => {
    // Update form data with current values
    form.business_hours = businessHours.value;
    form.currency = selectedCurrency.value;
    form.currency_symbol = selectedCurrencySymbol.value;
    
    // Add logo file if selected
    if (logoFile.value) {
        form.logo = logoFile.value;
    }
    
    form.patch(route('store.update'), {
        preserveScroll: true,
    });
};

const updateBusinessHours = (day: string, field: string, value: any) => {
    businessHours.value[day] = {
        ...businessHours.value[day],
        [field]: value
    };
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Store settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Store Settings</h2>
                    <p class="text-muted-foreground">
                        Configure your store information, currency, and receipt settings.
                    </p>
                </div>

                <form
                    @submit.prevent="submitForm"
                    class="space-y-6"
                >
                    
                    <Tabs default-value="general" class="space-y-6">
                        <TabsList class="grid w-full grid-cols-4">
                            <TabsTrigger value="general">General</TabsTrigger>
                            <TabsTrigger value="currency">Currency</TabsTrigger>
                            <TabsTrigger value="receipt">Receipt</TabsTrigger>
                            <TabsTrigger value="hours">Business Hours</TabsTrigger>
                        </TabsList>

                        <!-- General Information -->
                        <TabsContent value="general" class="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Store Information</CardTitle>
                                    <CardDescription>
                                        Basic information about your store and business.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <Label for="name">Store Name</Label>
                                            <Input
                                                id="name"
                                                v-model="form.name"
                                                required
                                                placeholder="My Store"
                                            />
                                            <InputError :message="form.errors.name" />
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="business_name">Business Name</Label>
                                            <Input
                                                id="business_name"
                                                v-model="form.business_name"
                                                required
                                                placeholder="My Business LLC"
                                            />
                                            <InputError :message="form.errors.business_name" />
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="address">Address</Label>
                                        <Textarea
                                            id="address"
                                            v-model="form.address"
                                            required
                                            placeholder="123 Main St, City, State 12345"
                                            rows="3"
                                        />
                                        <InputError :message="form.errors.address" />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <Label for="phone">Phone Number</Label>
                                            <Input
                                                id="phone"
                                                v-model="form.phone"
                                                required
                                                placeholder="+1 (555) 123-4567"
                                            />
                                            <InputError :message="form.errors.phone" />
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="email">Email Address</Label>
                                            <Input
                                                id="email"
                                                type="email"
                                                v-model="form.email"
                                                required
                                                placeholder="store@example.com"
                                            />
                                            <InputError :message="form.errors.email" />
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="business_registration_number">Business Registration Number (Optional)</Label>
                                        <Input
                                            id="business_registration_number"
                                            v-model="form.business_registration_number"
                                            placeholder="123456789"
                                        />
                                        <InputError :message="form.errors.business_registration_number" />
                                    </div>

                                    <!-- Logo Upload -->
                                    <div class="space-y-2">
                                        <Label>Store Logo</Label>
                                        <div class="flex items-center space-x-4">
                                            <div v-if="logoPreview" class="relative">
                                                <img
                                                    :src="logoPreview"
                                                    alt="Store logo"
                                                    class="h-20 w-20 rounded-lg object-cover border"
                                                />
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    size="sm"
                                                    class="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0"
                                                    @click="removeLogo"
                                                >
                                                    <X class="h-3 w-3" />
                                                </Button>
                                            </div>
                                            <div class="flex-1">
                                                <Label
                                                    for="logo"
                                                    class="flex h-20 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400"
                                                >
                                                    <Upload class="h-6 w-6 text-gray-400" />
                                                    <span class="text-sm text-gray-500">Upload logo</span>
                                                </Label>
                                                <Input
                                                    id="logo"
                                                    name="logo"
                                                    type="file"
                                                    accept="image/*"
                                                    class="hidden"
                                                    @change="handleLogoChange"
                                                />
                                            </div>
                                        </div>
                                        <InputError :message="form.errors.logo" />
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>

                        <!-- Currency Settings -->
                        <TabsContent value="currency" class="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Currency Settings</CardTitle>
                                    <CardDescription>
                                        Configure the currency used for transactions and pricing.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="currency">Currency</Label>
                                        <Select v-model="selectedCurrency" name="currency">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select currency" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="currency in currencies"
                                                    :key="currency.code"
                                                    :value="currency.code"
                                                >
                                                    {{ currency.symbol }} {{ currency.name }} ({{ currency.code }})
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError :message="form.errors.currency" />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="currency_symbol">Currency Symbol</Label>
                                        <Input
                                            id="currency_symbol"
                                            name="currency_symbol"
                                            :value="selectedCurrencySymbol"
                                            readonly
                                            class="bg-gray-50"
                                        />
                                        <p class="text-sm text-muted-foreground">
                                            This will be automatically set based on the selected currency.
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>

                        <!-- Receipt Settings -->
                        <TabsContent value="receipt" class="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Receipt Settings</CardTitle>
                                    <CardDescription>
                                        Configure how receipts are formatted and numbered.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="receipt_number_format">Receipt Number Format</Label>
                                        <Select v-model="form.receipt_number_format">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select format" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="format in receiptNumberFormats"
                                                    :key="format.value"
                                                    :value="format.value"
                                                >
                                                    {{ format.label }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError :message="form.errors.receipt_number_format" />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="receipt_footer">Receipt Footer</Label>
                                        <Textarea
                                            id="receipt_footer"
                                            v-model="form.receipt_footer"
                                            placeholder="Thank you for your business!"
                                            rows="3"
                                        />
                                        <InputError :message="form.errors.receipt_footer" />
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>

                        <!-- Business Hours -->
                        <TabsContent value="hours" class="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Business Hours</CardTitle>
                                    <CardDescription>
                                        Set your store's operating hours for each day of the week.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div
                                        v-for="(day, dayKey) in businessHours"
                                        :key="dayKey"
                                        class="flex items-center space-x-4 rounded-lg border p-4"
                                    >
                                        <div class="w-20">
                                            <Label class="capitalize">{{ dayKey }}</Label>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <Checkbox
                                                :id="`${dayKey}-closed`"
                                                :checked="day.closed"
                                                @update:checked="(checked) => updateBusinessHours(dayKey, 'closed', checked)"
                                            />
                                            <Label :for="`${dayKey}-closed`" class="text-sm">Closed</Label>
                                        </div>

                                        <div v-if="!day.closed" class="flex items-center space-x-2">
                                            <div class="space-y-1">
                                                <Label :for="`${dayKey}-open`" class="text-xs">Open</Label>
                                                <Input
                                                    :id="`${dayKey}-open`"
                                                    type="time"
                                                    :value="day.open"
                                                    @change="(e) => updateBusinessHours(dayKey, 'open', e.target.value)"
                                                    class="w-24"
                                                />
                                            </div>
                                            <span class="text-muted-foreground">to</span>
                                            <div class="space-y-1">
                                                <Label :for="`${dayKey}-close`" class="text-xs">Close</Label>
                                                <Input
                                                    :id="`${dayKey}-close`"
                                                    type="time"
                                                    :value="day.close"
                                                    @change="(e) => updateBusinessHours(dayKey, 'close', e.target.value)"
                                                    class="w-24"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>
                    </Tabs>

                    <div class="flex items-center gap-4">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            data-test="update-store-button"
                        >
                            Save Settings
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="form.recentlySuccessful"
                                class="text-sm text-green-600"
                            >
                                Settings saved successfully.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
