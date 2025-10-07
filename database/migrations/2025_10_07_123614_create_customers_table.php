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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique(); // Customer ID/Code
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('outstanding_balance', 10, 2)->default(0);
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->enum('credit_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('last_payment_date')->nullable();
            $table->decimal('total_purchases', 10, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['customer_code', 'phone', 'email']);
            $table->index('credit_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
