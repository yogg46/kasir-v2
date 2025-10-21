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
        Schema::create('discounts_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('price_id')->nullable()->constrained('prices_models')->onDelete('set null'); // FK ke prices_models
            $table->foreignUuid('branch_id')->nullable()->constrained('branches_models')->onDelete('set null'); // FK ke branches_models
            $table->foreignUuid('product_id')->nullable()->constrained('products_models')->onDelete('set null'); // FK ke products_models
            $table->enum('type',['item','transaction'])->default('item');
            $table->decimal('discount_percent',5,2)->nullable(); // diskon dalam persen
            $table->decimal('discount_amount',15,2)->nullable(); // diskon dalam nominal
            $table->date('valid_from')->nullable(); // tanggal mulai berlaku
            $table->date('valid_until')->nullable(); // tanggal berakhir berlaku
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts_models');
    }
};
