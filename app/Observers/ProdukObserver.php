<?php

namespace App\Observers;

use App\Models\produkModel;
use App\Models\stockModels;

class ProdukObserver
{
    /**
     * Handle the produkModel "created" event.
     */
    public function created(produkModel $produkModel): void
    {
        //
    }

    /**
     * Handle the produkModel "updated" event.
     */
    public function updated(produkModel $produkModel): void
    {
        //
    }

    public function updating(produkModel $produk): void
    {
        // Validasi: tidak bisa nonaktifkan produk yang masih punya stok
        if ($produk->isDirty('is_active') && !$produk->is_active) {
            $hasStock = stockModels::where('product_id', $produk->id)
                ->where('quantity', '>', 0)
                ->exists();

            if ($hasStock) {
                throw new \Exception('Tidak bisa menonaktifkan produk yang masih memiliki stok');
            }
        }
    }

    public function deleting(produkModel $produk): void
    {
        // Cek apakah ini soft delete atau force delete
        if ($produk->isForceDeleting()) {
            // Force delete - validasi ketat
            if ($produk->toStocks()->where('quantity', '>', 0)->exists()) {
                throw new \Exception('Tidak bisa menghapus permanen produk yang masih memiliki stok');
            }
        } else {
            // Soft delete - validasi lebih longgar
            if ($produk->toStocks()->where('quantity', '>', 0)->exists()) {
                throw new \Exception('Tidak bisa menghapus produk yang masih memiliki stok. Kosongkan stok terlebih dahulu.');
            }
        }
    }

    public function deleted(produkModel $produk): void
    {
        if (!$produk->isForceDeleting()) {
            // Soft delete - soft delete relasi juga (cascade soft delete)
            $produk->toHarga()->delete();
            $produk->toStocks()->delete();
            $produk->toBatches()->delete();

            // Log
            // \Log::info("Produk soft deleted: {$produk->name}");
        }
    }

    public function restoring(produkModel $produk): void
    {
        // Validasi sebelum restore
        if (!$produk->toKategori || $produk->toKategori->trashed()) {
            throw new \Exception('Tidak bisa restore produk karena kategori sudah dihapus');
        }
    }

    public function restored(produkModel $produk): void
    {
        // Restore relasi yang ikut di-soft delete
        $produk->toHarga()->withTrashed()->restore();
        $produk->toStocks()->withTrashed()->restore();
        $produk->toBatches()->withTrashed()->restore();

        // Log
        // \Log::info("Produk restored: {$produk->name}");
    }

    /**
     * Handle the produkModel "force deleted" event.
     */
    public function forceDeleted(produkModel $produkModel): void
    {
        //
    }
}
