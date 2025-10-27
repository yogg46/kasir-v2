<?php

namespace App\Observers;

use App\Models\stockModels;
use App\Models\notificationsModels;

class StockObserver
{
    /**
     * Handle the stockModels "created" event.
     */
    public function created(stockModels $stockModels): void
    {
        //
    }

    public function updating(stockModels $stock): void
    {
        // Validasi: quantity tidak boleh negatif
        if ($stock->quantity < 0) {
            throw new \Exception('Stok tidak boleh negatif');
        }

        // Notifikasi jika stok menipis (< 10)
        if ($stock->isDirty('quantity') && $stock->quantity < 10 && $stock->quantity > 0) {
            $warehouse = $stock->toGudang;

            notificationsModels::create([
                'branch_id' => $warehouse->branch_id ?? null,
                'title' => 'Stok Menipis',
                'message' => "Stok produk tersisa {$stock->quantity} unit",
                'type' => 'warning',
                'meta_data' => [
                    'product_id' => $stock->product_id,
                    'warehouse_id' => $stock->warehouse_id,
                    'quantity' => $stock->quantity
                ]
            ]);
        }

        // Notifikasi jika stok habis
        if ($stock->isDirty('quantity') && $stock->quantity == 0) {
            $warehouse = $stock->toGudang;

            notificationsModels::create([
                'branch_id' => $warehouse->branch_id ?? null,
                'title' => 'Stok Habis',
                'message' => 'Stok produk telah habis',
                'type' => 'danger',
                'meta_data' => [
                    'product_id' => $stock->product_id,
                    'warehouse_id' => $stock->warehouse_id
                ]
            ]);
        }
    }

    /**
     * Handle the stockModels "updated" event.
     */
    public function updated(stockModels $stockModels): void
    {
        //
    }

    /**
     * Handle the stockModels "deleted" event.
     */
    public function deleted(stockModels $stockModels): void
    {
        //
    }

    /**
     * Handle the stockModels "restored" event.
     */
    public function restored(stockModels $stockModels): void
    {
        //
    }

    /**
     * Handle the stockModels "force deleted" event.
     */
    public function forceDeleted(stockModels $stockModels): void
    {
        //
    }
}
