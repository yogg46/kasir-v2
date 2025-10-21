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
        Schema::create('goods_receipts_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders_models')->onDelete('cascade'); // FK ke purchase_orders_models
            $table->foreignUuid('warehouse_id')->constrained('warehoses_models')->onDelete('cascade'); // FK ke warehoses_models
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');
            $table->string('received_by')->nullable();
            $table->enum('status',['pending','completed','cancelled'])->default('pending');
            $table->decimal('total',15,2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts_models');
    }
};
