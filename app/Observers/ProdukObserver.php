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

    /**
     * Handle the produkModel "deleted" event.
     */
    public function deleted(produkModel $produkModel): void
    {
        //
    }

    public function deleting(produkModel $produk): void
    {
        // Validasi: tidak bisa hapus produk yang punya transaksi
        if ($produk->toStocks()->where('quantity', '>', 0)->exists()) {
            throw new \Exception('Tidak bisa menghapus produk yang masih memiliki stok');
        }
    }

    /**
     * Handle the produkModel "restored" event.
     */
    public function restored(produkModel $produkModel): void
    {
        //
    }

    /**
     * Handle the produkModel "force deleted" event.
     */
    public function forceDeleted(produkModel $produkModel): void
    {
        //
    }
}
