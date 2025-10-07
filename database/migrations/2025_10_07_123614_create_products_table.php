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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique(); // Stock Keeping Unit
            $table->string('barcode')->nullable()->unique(); // Barcode for scanning
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('selling_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock_level')->default(0);
            $table->string('serial_number')->nullable(); // Optional serial number tracking
            $table->string('image_path')->nullable();
            $table->boolean('track_serial_numbers')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['sku', 'barcode']);
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
