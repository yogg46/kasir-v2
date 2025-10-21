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

```
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
