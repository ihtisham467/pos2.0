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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('payment_type', ['cash', 'credit_payment']); // Cash or customer credit payment
            $table->decimal('amount', 10, 2);
            $table->string('payment_reference')->nullable(); // Receipt number or reference
            $table->text('notes')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();
            
            $table->index(['sales_transaction_id', 'customer_id', 'user_id']);
            $table->index(['payment_type', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
