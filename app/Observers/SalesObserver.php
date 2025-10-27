<?php

namespace App\Observers;

use App\Models\salesModels;
use App\Models\shiftKasirModel;

class SalesObserver
{
    /**
     * Handle the salesModels "created" event.
     */
    public function created(salesModels $sale): void
    {
        // Update cash_in di shift kasir (hanya untuk cash payment)
        if ($sale->payment_method === 'cash') {
            $activeShift = shiftKasirModel::where('cashier_id', $sale->cashier_id)
                ->where('branch_id', $sale->branch_id)
                ->where('status', 'open')
                ->first();

            if ($activeShift) {
                $activeShift->increment('cash_in', $sale->total_amount);
            }
        }
    }

    public function creating(salesModels $sale): void
    {
        // Validasi: harus ada shift aktif
        $activeShift = shiftKasirModel::where('cashier_id', $sale->cashier_id)
            ->where('branch_id', $sale->branch_id)
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            throw new \Exception('Kasir belum membuka shift. Silakan buka shift terlebih dahulu.');
        }

        // Auto set sale_date ke hari ini jika kosong
        if (!$sale->sale_date) {
            $sale->sale_date = now()->format('Y-m-d');
        }
    }

    /**
     * Handle the salesModels "updated" event.
     */
    public function updated(salesModels $salesModels): void
    {
        //
    }
    public function updating(salesModels $sale): void
    {
        // Validasi: tidak bisa ubah transaksi yang sudah void/refund
        if ($sale->getOriginal('status') !== 'paid' && $sale->isDirty()) {
            throw new \Exception('Tidak bisa mengubah transaksi dengan status ' . $sale->getOriginal('status'));
        }
    }

    /**
     * Handle the salesModels "deleted" event.
     */
    public function deleting(salesModels $sale): void
    {
        if ($sale->isForceDeleting()) {
            // Force delete - hapus items juga
            $sale->toItems()->forceDelete();
        } else {
            // Soft delete
            if ($sale->status === 'paid' && $sale->created_at->diffInDays(now()) > 7) {
                throw new \Exception('Tidak bisa menghapus transaksi yang sudah lebih dari 7 hari');
            }

            // Soft delete items juga (jika items punya soft delete)
            // $sale->toItems()->delete();
        }
    }

    public function deleted(salesModels $sale): void
    {
        if (!$sale->isForceDeleting()) {
            // Revert stock jika perlu
            // ... logic revert stock

            // Log
            // \Log::info("Sale soft deleted: {$sale->invoice_number}");
        }
    }

    /**
     * Handle the salesModels "restored" event.
     */
    public function restored(salesModels $salesModels): void
    {
        //
    }

    /**
     * Handle the salesModels "force deleted" event.
     */
    public function forceDeleted(salesModels $salesModels): void
    {
        //
    }
}
