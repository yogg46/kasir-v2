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
        Schema::create('sales_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->constrained('branches_models')->onDelete('cascade'); // FK ke branches_models
            $table->foreignUuid('cashier_id')->constrained('users')->onDelete('cascade'); // FK ke users_models
            $table->date('sale_date');
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal',15,2)->default(0);
            $table->decimal('total_amount',15,2)->default(0);
            $table->decimal('discount_total',15,2)->default(0);
            $table->enum('payment_method',['cash','qris','bank_transfer'])->default('cash');
            $table->enum('status',['paid','void','refund'])->default('paid');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_models');
    }
};
