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
        Schema::create('saleitems_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sale_id')->constrained('sales_models')->onDelete('cascade'); // FK ke sales_models
            $table->foreignUuid('product_id')->constrained('products_models')->onDelete('cascade'); // FK ke products_models
            $table->string('unit_name')->nullable();
            $table->integer('quantity')->default(0);

            $table->decimal('price',15,2)->default(0);
            $table->decimal('discount',15,2)->default(0);
            $table->decimal('subtotal',15,2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saleitems_models');
    }
};
