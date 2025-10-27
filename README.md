# 📦 Sistem Manajemen Gudang & Penjualan

Dokumen ini berisi daftar modul dan rincian fitur berdasarkan jenis pengguna dalam sistem.

---

## 👑 Administrator / Super Admin

### 🔹 Master Data

-   Tambah / ubah produk, kategori, satuan, harga
-   Tambah kolom keterangan/catatan (seasonal/UMKM)
-   Manajemen supplier, cabang, dan gudang
-   Diskon per item / per transaksi
-   Pengaturan harga multi-level (eceran, grosir, cabang)

### 🔹 User Management

-   Buat akun toko baru
-   Buat akun user baru (kasir, gudang, manajer)
-   Atur role & hak akses

### 🔹 Reporting (Global)

-   Dashboard penjualan seluruh cabang
-   Laporan stok global per gudang
-   Tambahan lokasi rak (opsional)

### 🔹 Purchasing

-   Buat & kelola Purchase Order (PO)
-   Penerimaan barang dari supplier (Goods Receipt)
-   Retur ke supplier

---

## 🏭 Manajer Gudang Induk

### 🔹 Inventory (Gudang Induk)

-   Manajemen stok masuk / keluar
-   Distribusi ke cabang (Delivery Order)
-   Permintaan dari cabang (Stock Requisition)
    -   Berdasarkan buffer min / max item
    -   Opsi piutang supplier
-   Stok opname & adjustment
-   Tracking batch / expiry / serial number

### 🔹 Reporting

-   Laporan pembelian, penerimaan, dan distribusi
-   Laporan stok pusat per kategori / supplier

---

## 🏬 Gudang Cabang / Toko

### 🔹 Inventory (Cabang)

-   Penerimaan barang dari gudang induk
-   Transfer antar cabang (jika diizinkan)
-   Retur ke gudang induk
-   Stok opname cabang
-   Penyesuaian stok

### 🔹 Laporan Cabang

-   Laporan penerimaan barang
-   Laporan retur & stok akhir cabang
-   Laporan selisih opname

---

## 💰 Kasir

### 🔹 Kasir (POS)

-   Transaksi penjualan (barcode / manual)
-   Pembayaran multi-metode (Cash, QRIS)
-   Cetak struk (thermal printer)
-   Refund / void transaksi
-   Buka & tutup kasir (shift management)

### 🔹 Laporan

-   Laporan penjualan per shift
-   Laporan kas harian

---

## 👨‍💼 Kepala Toko (Opsional)

### 🔹 Sales & Inventory Overview

-   Monitoring stok toko secara real-time
-   Laporan retur barang ke pusat
-   Review penjualan harian per kasir
-   Laporan stok, penjualan, dan margin

---

## 📊 Direksi

### 🔹 Dashboard Analitik

-   Omzet per cabang, kategori, produk
-   Profit margin & tren penjualan
-   Top produk / cabang / kasir
-   Laporan distribusi & stok
-   Laporan gabungan penjualan & pembelian

---

## 🧩 Catatan Tambahan

-   Sistem ini mendukung multi-cabang dan multi-user role.
-   Hak akses pengguna diatur melalui modul **User Management** oleh Super Admin.
-   Semua laporan dapat diekspor ke format Excel / PDF (opsional).

---

```bash
app/
└── Http/
    └── Livewire/
        ├── MasterData/
        │   ├── Product/
        │   │   ├── ProductList.php
        │   │   ├── ProductForm.php
        │   │   └── product-list.blade.php
        │   ├── Supplier/
        │   │   ├── SupplierList.php
        │   │   └── SupplierForm.php
        │   └── PriceSetting/
        │       ├── MultiLevelPricing.php
        │       └── DiscountSetting.php
        │
        ├── UserManagement/
        │   ├── UserList.php
        │   ├── RoleManager.php
        │   └── PermissionManager.php
        │
        ├── Inventory/
        │   ├── StockIn.php
        │   ├── StockOut.php
        │   ├── StockTransfer.php
        │   ├── StockOpname.php
        │   ├── ReturnToSupplier.php
        │   └── BatchTracking.php
        │
        ├── Purchasing/
        │   ├── PurchaseOrderList.php
        │   ├── PurchaseOrderForm.php
        │   ├── GoodsReceipt.php
        │   └── SupplierReturn.php
        │
        ├── Sales/
        │   ├── PosTransaction.php
        │   ├── RefundTransaction.php
        │   ├── ShiftManagement.php
        │   └── CashReport.php
        │
        ├── Reporting/
        │   ├── SalesReport.php
        │   ├── StockReport.php
        │   ├── ProfitAnalysis.php
        │   └── CombinedReport.php
        │
        └── Dashboard/
            ├── GlobalDashboard.php
            ├── WarehouseDashboard.php
            └── BranchDashboard.php
```

---

```php

// ============================================
// PART 5: CONTOH PENGGUNAAN
// ============================================

/*
===========================================
CONTOH PENGGUNAAN ACCESSOR:
===========================================

// 1. Produk
$produk = produkModel::find($id);
echo $produk->is_active_text; // "Aktif" atau "Nonaktif"
echo $produk->type_label; // "UMKM", "Regular", "Musiman"
echo $produk->total_stock; // Total stok semua gudang

// 2. Harga
$harga = hargaModel::find($id);
echo $harga->price_formatted; // "Rp 150.000"
echo $harga->profit_margin; // 25.5 (dalam persen)
echo $harga->is_valid ? 'Valid' : 'Expired'; // Cek masih berlaku atau tidak

// 3. Batch
$batch = batchModel::find($id);
echo $batch->is_expired ? 'Expired' : 'OK';
echo $batch->days_until_expiry; // 45 (hari)
echo $batch->expiry_status; // "good", "warning", "danger", "expired"

// 4. Sales
$sale = salesModels::find($id);
echo $sale->total_amount_formatted; // "Rp 1.500.000"
echo $sale->status_badge; // "success", "danger", "warning"
echo $sale->payment_method_label; // "Tunai", "QRIS", "Transfer Bank"

// 5. Shift Kasir
$shift = shiftKasirModel::find($id);
echo $shift->shift_duration; // 480 (menit)
echo $shift->cash_difference; // 5000 (selisih)
echo $shift->cash_status; // "balanced", "over", "short"

===========================================
CONTOH VALIDASI OBSERVER:
===========================================

// 1. Tidak bisa menonaktifkan produk yang masih ada stok
try {
    $produk = produkModel::find($id);
    $produk->is_active = false;
    $produk->save(); // Akan throw exception jika masih ada stok
} catch (\Exception $e) {
    echo $e->getMessage();
}

// 2. Hanya 1 default price per produk per cabang
$harga1 = hargaModel::create([
    'product_id' => $produkId,
    'branch_id' => $branchId,
    'is_default' => true,
    // ...
]);
// Otomatis set harga lain menjadi is_default = false

// 3. Validasi shift kasir
try {
    $shift = shiftKasirModel::create([
        'cashier_id' => $kasierId,
        'branch_id' => $branchId,
        'initial_cash' => 100000
    ]);
    // Akan error jika kasir masih punya shift open
} catch (\Exception $e) {
    echo $e->getMessage();
}

// 4. Auto notifikasi saat stok menipis
$stock = stockModels::find($id);
$stock->quantity = 8; // < 10
$stock->save(); // Otomatis create notifikasi

// 5. Validasi transaksi harus dalam shift
try {
    $sale = salesModels::create([
        'cashier_id' => $kasierId,
        'branch_id' => $branchId,
        // ...
    ]);
    // Akan error jika kasir belum buka shift
} catch (\Exception $e) {
    echo $e->getMessage();
}

===========================================
QUERY DENGAN INDEX (LEBIH CEPAT):
===========================================

// 1. Cari produk aktif by kategori (menggunakan composite index)
$products = produkModel::where('category_id', $categoryId)
    ->where('is_active', true)
    ->get();

// 2. Cari harga default per cabang (menggunakan composite index)
$defaultPrice = hargaModel::where('product_id', $productId)
    ->where('branch_id', $branchId)
    ->where('is_default', true)
    ->first();

// 3. Cek batch yang akan expired (menggunakan index expiry_date)
$expiringBatches = batchModel::where('expiry_date', '<=', now()->addDays(30))
    ->where('warehouse_id', $warehouseId)
    ->get();

// 4. Laporan penjualan per cabang per tanggal (composite index)
$sales = salesModels::where('branch_id', $branchId)
    ->whereBetween('sale_date', [$startDate, $endDate])
    ->get();

// 5. Notifikasi belum dibaca per user (composite index)
$unreadNotifs = notificationsModels::where('user_id', $userId)
    ->where('is_read', false)
    ->get();

===========================================
TIPS PERFORMANCE:
===========================================

1. Gunakan eager loading untuk relasi:
   $products = produkModel::with(['toKategori', 'toStocks'])->get();

2. Gunakan chunk untuk data besar:
   produkModel::where('is_active', true)->chunk(100, function ($products) {
       foreach ($products as $product) {
           // Process...
       }
   });

3. Cache query yang sering digunakan:
   $categories = Cache::remember('categories', 3600, function () {
       return kategoriModel::all();
   });

4. Gunakan index yang sudah dibuat:
   // ✅ GOOD - menggunakan index
   ->where('branch_id', $id)->where('sale_date', $date)

   // ❌ BAD - tidak menggunakan index
   ->whereRaw("DATE_FORMAT(sale_date, '%Y-%m') = '2024-10'")

5. Select hanya kolom yang diperlukan:
   produkModel::select('id', 'name', 'code')->get();

*/

```
