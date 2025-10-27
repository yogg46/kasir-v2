<?php

namespace App\Observers;

use App\Models\purchaseOrdersModels;

class PurchaseOrderObserver
{
    /**
     * Handle the purchaseOrdersModels "created" event.
     */
    public function created(purchaseOrdersModels $purchaseOrdersModels): void
    {
        //
    }

    /**
     * Handle the purchaseOrdersModels "updated" event.
     */
    public function updated(purchaseOrdersModels $purchaseOrdersModels): void
    {
        //
    }

    public function creating(purchaseOrdersModels $po): void
    {
        // Auto set order_date jika kosong
        if (!$po->order_date) {
            $po->order_date = now()->format('Y-m-d');
        }
    }

    public function updating(purchaseOrdersModels $po): void
    {
        // Validasi: tidak bisa ubah status skip
        $allowedTransitions = [
            'draf' => ['approved', 'cancelled'],
            'approved' => ['received', 'cancelled'],
            'received' => [],
            'cancelled' => []
        ];

        if ($po->isDirty('status')) {
            $oldStatus = $po->getOriginal('status');
            $newStatus = $po->status;

            if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
                throw new \Exception("Tidak bisa mengubah status dari {$oldStatus} ke {$newStatus}");
            }
        }

        // Tidak bisa edit PO yang sudah received
        if ($po->getOriginal('status') === 'received' && $po->isDirty() && !$po->isDirty('notes')) {
            throw new \Exception('Tidak bisa mengubah PO yang sudah diterima');
        }
    }

    /**
     * Handle the purchaseOrdersModels "deleted" event.
     */
    public function deleted(purchaseOrdersModels $purchaseOrdersModels): void
    {
        //
    }

    /**
     * Handle the purchaseOrdersModels "restored" event.
     */
    public function restored(purchaseOrdersModels $purchaseOrdersModels): void
    {
        //
    }

    /**
     * Handle the purchaseOrdersModels "force deleted" event.
     */
    public function forceDeleted(purchaseOrdersModels $purchaseOrdersModels): void
    {
        //
    }
}
