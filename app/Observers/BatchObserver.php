<?php

namespace App\Observers;

use App\Models\batchModel;
use App\Models\stockModels;
use App\Models\notificationsModels;

class BatchObserver
{
    /**
     * Handle the batchModel "created" event.
     */
    public function created(batchModel $batch): void
    {
        // Update stock saat batch baru dibuat
        $this->updateStock($batch);

        // Notif jika batch akan expired dalam 90 hari
        if ($batch->expiry_date && $batch->days_until_expiry <= 90) {
            $this->createExpiryNotification($batch);
        }
    }

    /**
     * Handle the batchModel "updated" event.
     */
    public function updated(batchModel $batchModel): void
    {
        //
    }
    public function updating(batchModel $batch): void
    {
        // Update stock jika quantity berubah
        if ($batch->isDirty('quantity')) {
            $this->updateStock($batch);
        }
    }

    private function updateStock(batchModel $batch, bool $isDeleting = false): void
    {
        $stock = stockModels::firstOrCreate(
            [
                'product_id' => $batch->product_id,
                'warehouse_id' => $batch->warehouse_id,
            ],
            ['quantity' => 0]
        );

        if ($isDeleting) {
            $stock->decrement('quantity', $batch->getOriginal('quantity'));
        } else {
            $oldQty = $batch->getOriginal('quantity') ?? 0;
            $diff = $batch->quantity - $oldQty;
            if ($diff != 0) {
                $stock->increment('quantity', $diff);
            }
        }
    }

    private function createExpiryNotification(batchModel $batch): void
    {
        $warehouse = $batch->toGudang;

        notificationsModels::create([
            'branch_id' => $warehouse->branch_id ?? null,
            'title' => 'Batch Mendekati Expired',
            'message' => "Batch {$batch->code} akan expired dalam {$batch->days_until_expiry} hari",
            'type' => $batch->days_until_expiry <= 30 ? 'danger' : 'warning',
            'meta_data' => [
                'batch_id' => $batch->id,
                'product_id' => $batch->product_id,
                'expiry_date' => $batch->expiry_date,
                'days_remaining' => $batch->days_until_expiry
            ]
        ]);
    }

    /**
     * Handle the batchModel "deleted" event.
     */
    public function deleted(batchModel $batchModel): void
    {
        //
    }

    /**
     * Handle the batchModel "restored" event.
     */
    public function restored(batchModel $batchModel): void
    {
        //
    }

    /**
     * Handle the batchModel "force deleted" event.
     */
    public function forceDeleted(batchModel $batchModel): void
    {
        //
    }
}
