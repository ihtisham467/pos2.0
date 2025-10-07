<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('business_hours')->nullable(); // Store business hours as JSON
            $table->string('currency', 3)->default('USD');
            $table->string('currency_symbol', 5)->default('$');
            $table->json('receipt_settings')->nullable(); // Store receipt configuration as JSON
            $table->string('receipt_footer')->nullable();
            $table->string('receipt_number_format')->default('POS-{YYYY}-{MM}-{DD}-{0000}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
