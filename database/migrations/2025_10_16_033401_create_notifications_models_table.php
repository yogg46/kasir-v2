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
        Schema::create('notifications_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null'); // opsional, FK ke users
            $table->foreignUuid('branch_id')->nullable()->constrained('branches_models')->onDelete('set null'); // opsional, FK ke branches_models
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->nullable(); // e.g., info, warning, error
            $table->json('meta_data')->nullable(); // old_price, new_price, product_id, etc
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_models');
    }
};
