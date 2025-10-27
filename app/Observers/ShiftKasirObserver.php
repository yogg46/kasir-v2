<?php

namespace App\Observers;

use App\Models\shiftKasirModel;

class ShiftKasirObserver
{
    /**
     * Handle the shiftKasirModel "created" event.
     */
    public function created(shiftKasirModel $shiftKasirModel): void
    {
        //
    }

    public function creating(shiftKasirModel $shift): void
    {
        // Validasi: tidak boleh ada shift open untuk kasir yang sama
        $hasOpenShift = shiftKasirModel::where('cashier_id', $shift->cashier_id)
            ->where('status', 'open')
            ->exists();

        if ($hasOpenShift) {
            throw new \Exception('Kasir masih memiliki shift yang aktif. Tutup shift sebelumnya terlebih dahulu.');
        }

        // Auto set shift_start
        if (!$shift->shift_start) {
            $shift->shift_start = now();
        }
    }

    public function updating(shiftKasirModel $shift): void
    {
        // Saat menutup shift, auto set shift_end dan final_cash
        if ($shift->isDirty('status') && $shift->status === 'closed') {
            if (!$shift->shift_end) {
                $shift->shift_end = now();
            }

            // Calculate expected final cash
            $expectedFinal = $shift->initial_cash + $shift->cash_in - $shift->cash_out;

            // Jika final_cash belum diset, gunakan expected
            if (!$shift->final_cash) {
                $shift->final_cash = $expectedFinal;
            }
        }
    }

    /**
     * Handle the shiftKasirModel "updated" event.
     */
    public function updated(shiftKasirModel $shiftKasirModel): void
    {
        //
    }

    /**
     * Handle the shiftKasirModel "deleted" event.
     */
    public function deleted(shiftKasirModel $shiftKasirModel): void
    {
        //
    }

    /**
     * Handle the shiftKasirModel "restored" event.
     */
    public function restored(shiftKasirModel $shiftKasirModel): void
    {
        //
    }

    /**
     * Handle the shiftKasirModel "force deleted" event.
     */
    public function forceDeleted(shiftKasirModel $shiftKasirModel): void
    {
        //
    }
}
