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
        Schema::create('batches_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products_models')->onDelete('cascade'); // FK ke products_models
            $table->foreignUuid('warehouse_id')->nullable()->constrained('warehoses_models')->onDelete('cascade'); // FK ke warehoses_models
            $table->string('code')->unique();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches_models');
    }
};
