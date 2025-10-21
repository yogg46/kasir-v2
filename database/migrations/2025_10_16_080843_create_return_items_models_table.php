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
        Schema::create('return_items_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('return_id')->constrained('returns_models')->onDelete('cascade'); // FK ke returns_models
            $table->foreignUuid('product_id')->constrained('products_models')->onDelete('cascade'); // FK ke products_models
             $table->foreignUuid('batch_id')->nullable()->constrained('batches_models')->onDelete('set null'); // opsional, FK ke batches_models
            $table->integer('quantity')->default(0);
            $table->decimal('price',15,2)->default(0);
            $table->decimal('subtotal',15,2)->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items_models');
    }
};
