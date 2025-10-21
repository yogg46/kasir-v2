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
        Schema::create('prices_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products_models')->onDelete('cascade'); // FK ke products_models
            $table->foreignUuid('branch_id')->nullable()->constrained('branches_models')->onDelete('set null'); // FK ke branches_models
            $table->string('unit_name'); // nama satuan, misal 'pcs', 'box', dll
            $table->integer('unit_qty')->default(1); // jumlah unit dalam satuan ini
            $table->decimal('price', 15, 2); // harga dalam satuan
            $table->decimal('old_price', 15, 2)->nullable(); // harga lama opsional
            $table->decimal('purchase_price', 15, 2)->nullable(); // harga beli opsional
            $table->boolean('is_default')->default(false); // apakah ini harga default untuk produk di cabang ini
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
        Schema::dropIfExists('prices_models');
    }
};
