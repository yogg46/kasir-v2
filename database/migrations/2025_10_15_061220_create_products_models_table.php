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
        Schema::create('products_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories_models')->onDelete('cascade'); // FK ke categories_models
            $table->foreignUuid('suplier_id')->nullable()->constrained('supliers_models')->onDelete('set null'); // opsional jika produk tidak memiliki suplier
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->enum('type',['umkm','regular','seasonal'])->default('regular');
            $table->string('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_models');
    }
};
