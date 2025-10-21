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
        Schema::create('purchase_order_items_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders_models')->onDelete('cascade'); // FK ke purchase_orders_models
            $table->foreignUuid('product_id')->constrained('products_models')->onDelete('cascade'); // FK ke products_models
            $table->integer('quantity')->default(0);
            $table->decimal('price',15,2)->default(0);
            $table->decimal('subtotal',15,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items_models');
    }
};
