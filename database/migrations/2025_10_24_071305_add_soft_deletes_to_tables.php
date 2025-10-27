<?php

// ============================================
// PART 1: MIGRATION - TAMBAH DELETED_AT COLUMN
// ============================================

// File: database/migrations/xxxx_add_soft_deletes_to_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel-tabel yang PERLU soft delete

        // 1. Master Data (bisa di-restore)
        Schema::table('products_models', function (Blueprint $table) {
            $table->softDeletes(); // Menambah kolom deleted_at
        });

        Schema::table('categories_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('supliers_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('branches_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('warehoses_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 2. Pricing & Discounts (history penting)
        Schema::table('prices_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('discounts_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 3. Transaksi (audit trail)
        Schema::table('sales_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('purchase_orders_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('goods_receipts_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('returns_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('delivery_orders_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 4. Batch & Stock (tracking penting)
        Schema::table('batches_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('stock_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 5. Shift Kasir (audit trail)
        Schema::table('cashier_shifts_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 6. Notifications (opsional, bisa dihapus permanen juga)
        Schema::table('notifications_models', function (Blueprint $table) {
            $table->softDeletes();
        });

        /*
        TIDAK PERLU SOFT DELETE (hapus permanen):
        - saleitems_models (item detail, ikut parent)
        - purchase_order_items_models (item detail)
        - goods_receipt_items_models (item detail)
        - delivery_order_items_models (item detail)
        - return_items_models (item detail)
        - role_models (master role, jarang berubah)
        */
    }

    public function down(): void
    {
        Schema::table('products_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('supliers_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('branches_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('warehoses_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('prices_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('discounts_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sales_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_orders_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('goods_receipts_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('returns_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('delivery_orders_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('batches_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('stock_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cashier_shifts_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('notifications_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
