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
        Schema::create('cashier_shifts_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cashier_id')->constrained('users')->onDelete('cascade'); // FK ke users_models
            $table->foreignUuid('branch_id')->constrained('branches_models')->onDelete('cascade'); // FK ke branches_models
            $table->dateTime('shift_start');
            $table->dateTime('shift_end')->nullable();
            $table->decimal('initial_cash',15,2)->default(0);
            $table->decimal('cash_in',15,2)->default(0);
            $table->decimal('cash_out',15,2)->default(0);
            $table->decimal('final_cash',15,2)->default(0);
            $table->enum('status',['open','closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts_models');
    }
};
