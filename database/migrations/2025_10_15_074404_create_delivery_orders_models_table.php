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
        Schema::create('delivery_orders_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('warehose_from')->constrained('warehoses_models')->onDelete('cascade'); // FK ke warehoses_models
            $table->foreignUuid('warehose_to')->constrained('warehoses_models')->onDelete('cascade'); // FK ke warehoses_models
            $table->string('delivery_number')->unique();
            $table->date('delivery_date');
            $table->enum('status',['draf','in_transit','delivered','cancelled'])->default('draf');
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders_models');
    }
};
