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
        Schema::create('purchase_orders_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('suplier_id')->constrained('supliers_models')->onDelete('cascade'); // FK ke supliers_models
            $table->foreignUuid('warehouse_id')->constrained('warehoses_models')->onDelete('cascade'); // FK ke warehoses_models
            $table->string('order_number')->unique();
            $table->date('order_date');
             $table->decimal('total', 15, 2)->default(0);
            $table->enum('status',['draf','approved','received','cancelled'])->default('draf');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders_models');
    }
};
