<?php

namespace App\Observers;

use App\Models\hargaModel;
use App\Models\notificationsModels;

class HargaObserver
{
    /**
     * Handle the hargaModel "created" event.
     */
    public function created(hargaModel $hargaModel): void
    {
        //
    }
    public function creating(hargaModel $harga): void
    {
        // Validasi: hanya 1 default price per produk per cabang
        if ($harga->is_default) {
            hargaModel::where('product_id', $harga->product_id)
                ->where('branch_id', $harga->branch_id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Validasi: unit_qty harus > 0
        if ($harga->unit_qty <= 0) {
            throw new \Exception('Jumlah unit harus lebih dari 0');
        }

        // Validasi: price harus >= purchase_price
        if ($harga->purchase_price && $harga->price < $harga->purchase_price) {
            throw new \Exception('Harga jual tidak boleh lebih kecil dari harga beli');
        }
    }

    /**
     * Handle the hargaModel "updated" event.
     */
    public function updated(hargaModel $hargaModel): void
    {
        //
    }

    public function updating(hargaModel $harga): void
    {
        // Notifikasi jika harga berubah signifikan (> 10%)
        if ($harga->isDirty('price')) {
            $oldPrice = $harga->getOriginal('price');
            $newPrice = $harga->price;
            $percentChange = abs(($newPrice - $oldPrice) / $oldPrice * 100);

            if ($percentChange > 10) {
                notificationsModels::create([
                    'branch_id' => $harga->branch_id,
                    'title' => 'Perubahan Harga Signifikan',
                    'message' => "Harga produk berubah {$percentChange}%",
                    'type' => 'warning',
                    'meta_data' => [
                        'product_id' => $harga->product_id,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice,
                        'change_percent' => $percentChange
                    ]
                ]);
            }
        }

        // Validasi sama seperti creating
        if ($harga->isDirty('is_default') && $harga->is_default) {
            hargaModel::where('product_id', $harga->product_id)
                ->where('branch_id', $harga->branch_id)
                ->where('id', '!=', $harga->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }

    /**
     * Handle the hargaModel "deleted" event.
     */
    public function deleted(hargaModel $hargaModel): void
    {
        //
    }

    /**
     * Handle the hargaModel "restored" event.
     */
    public function restored(hargaModel $hargaModel): void
    {
        //
    }

    /**
     * Handle the hargaModel "force deleted" event.
     */
    public function forceDeleted(hargaModel $hargaModel): void
    {
        //
    }
}
