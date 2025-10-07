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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('movement_type', ['sale', 'purchase', 'adjustment', 'return', 'damage', 'loss']);
            $table->integer('quantity_change'); // Positive for additions, negative for reductions
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('reference_type')->nullable(); // 'sales_transaction', 'purchase', 'adjustment', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of the related record
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->string('serial_number')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'movement_type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
