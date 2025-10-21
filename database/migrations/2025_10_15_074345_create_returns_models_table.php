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
        Schema::create('returns_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('source_branch_id')->nullable()->constrained('branches_models')->onDelete('cascade'); // FK ke branches_models
            $table->foreignUuid('target_branch_id')->nullable()->constrained('branches_models')->onDelete('cascade'); // FK ke branches_models
            $table->foreignUuid('suplier_id')->nullable()->constrained('supliers_models')->onDelete('set null'); // opsional, FK ke supliers_models
           
            $table->string('return_number')->unique();
            $table->enum('type', ['customer_to_branch', 'branch_to_head', 'head_to_supplier']);
            $table->text('notes')->nullable();
            $table->date('return_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns_models');
    }
};
