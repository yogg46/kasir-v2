<?php

// ============================================
// PART 1: MIGRATION - TAMBAH INDEX
// ============================================
// File: database/migrations/xxxx_add_indexes_for_performance.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products - sering dicari by code, barcode, category
        Schema::table('products_models', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('is_active');
            $table->index(['category_id', 'is_active']); // composite untuk filter aktif per kategori
        });

        // Stock - sering query by product & warehouse
        Schema::table('stock_models', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index(['product_id', 'warehouse_id']); // composite untuk cek stok produk di gudang
        });

        // Batches - query by expiry, product, warehouse
        Schema::table('batches_models', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('expiry_date'); // untuk cek expired
            $table->index(['expiry_date', 'warehouse_id']); // cek expired per gudang
        });

        // Prices - query by product, branch, validity
        Schema::table('prices_models', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('branch_id');
            $table->index('is_default');
            $table->index(['product_id', 'branch_id', 'is_default']); // get default price per branch
            $table->index(['valid_from', 'valid_until']); // cek harga aktif
        });

        // Discounts - query by validity, type
        Schema::table('discounts_models', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('branch_id');
            $table->index('type');
            $table->index(['valid_from', 'valid_until']); // cek diskon aktif
            $table->index(['product_id', 'branch_id', 'type']); // get diskon spesifik
        });

        // Sales - query by date, branch, cashier, status
        Schema::table('sales_models', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('cashier_id');
            $table->index('sale_date');
            $table->index('status');
            $table->index(['branch_id', 'sale_date']); // laporan per cabang per tanggal
            $table->index(['cashier_id', 'sale_date']); // laporan per kasir
        });

        // Purchase Orders - query by supplier, warehouse, date, status
        Schema::table('purchase_orders_models', function (Blueprint $table) {
            $table->index('suplier_id');
            $table->index('warehouse_id');
            $table->index('order_date');
            $table->index('status');
            $table->index(['status', 'order_date']); // filter PO by status & date
        });

        // Goods Receipts - query by PO, warehouse, date, status
        Schema::table('goods_receipts_models', function (Blueprint $table) {
            $table->index('purchase_order_id');
            $table->index('warehouse_id');
            $table->index('receipt_date');
            $table->index('status');
        });

        // Returns - query by branches, supplier, date, type
        Schema::table('returns_models', function (Blueprint $table) {
            $table->index('source_branch_id');
            $table->index('target_branch_id');
            $table->index('suplier_id');
            $table->index('return_date');
            $table->index('type');
        });

        // Delivery Orders - query by warehouses, date, status
        Schema::table('delivery_orders_models', function (Blueprint $table) {
            $table->index('warehose_from');
            $table->index('warehose_to');
            $table->index('delivery_date');
            $table->index('status');
        });

        // Cashier Shifts - query by cashier, branch, date
        Schema::table('cashier_shifts_models', function (Blueprint $table) {
            $table->index('cashier_id');
            $table->index('branch_id');
            $table->index('shift_start');
            $table->index('status');
            $table->index(['cashier_id', 'status']); // cek shift aktif kasir
        });

        // Notifications - query by user, branch, read status
        Schema::table('notifications_models', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']); // notif belum dibaca per user
        });

        // Users - query by role, branch
        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id');
            $table->index('branch_id');
        });

        // Warehouses - query by branch
        Schema::table('warehoses_models', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('is_main');
        });
    }

    public function down(): void
    {
        Schema::table('products_models', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['category_id', 'is_active']);
        });

        Schema::table('stock_models', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropIndex(['product_id', 'warehouse_id']);
        });

        Schema::table('batches_models', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropIndex(['expiry_date']);
            $table->dropIndex(['expiry_date', 'warehouse_id']);
        });

        Schema::table('prices_models', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['is_default']);
            $table->dropIndex(['product_id', 'branch_id', 'is_default']);
            $table->dropIndex(['valid_from', 'valid_until']);
        });

        Schema::table('discounts_models', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['valid_from', 'valid_until']);
            $table->dropIndex(['product_id', 'branch_id', 'type']);
        });

        Schema::table('sales_models', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['cashier_id']);
            $table->dropIndex(['sale_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['branch_id', 'sale_date']);
            $table->dropIndex(['cashier_id', 'sale_date']);
        });

        Schema::table('purchase_orders_models', function (Blueprint $table) {
            $table->dropIndex(['suplier_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropIndex(['order_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'order_date']);
        });

        Schema::table('goods_receipts_models', function (Blueprint $table) {
            $table->dropIndex(['purchase_order_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropIndex(['receipt_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('returns_models', function (Blueprint $table) {
            $table->dropIndex(['source_branch_id']);
            $table->dropIndex(['target_branch_id']);
            $table->dropIndex(['suplier_id']);
            $table->dropIndex(['return_date']);
            $table->dropIndex(['type']);
        });

        Schema::table('delivery_orders_models', function (Blueprint $table) {
            $table->dropIndex(['warehose_from']);
            $table->dropIndex(['warehose_to']);
            $table->dropIndex(['delivery_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('cashier_shifts_models', function (Blueprint $table) {
            $table->dropIndex(['cashier_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['shift_start']);
            $table->dropIndex(['status']);
            $table->dropIndex(['cashier_id', 'status']);
        });

        Schema::table('notifications_models', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['is_read']);
            $table->dropIndex(['user_id', 'is_read']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
            $table->dropIndex(['branch_id']);
        });

        Schema::table('warehoses_models', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['is_main']);
        });
    }
};
