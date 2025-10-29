<?php

namespace App\Http\Controllers;

use App\Models\salesModels;
use Illuminate\Http\Request;
use App\Models\shiftKasirModel;

class PrintController extends Controller
{
    public function printShift($shiftId)
    {
        $shift = shiftKasirModel::with(['toKasir', 'toCabang'])->findOrFail($shiftId);

        // Get sales during this shift
        $sales = salesModels::where('cashier_id', $shift->cashier_id)
            ->where('branch_id', $shift->branch_id)
            ->whereBetween('created_at', [
                $shift->shift_start,
                $shift->shift_end ?? now()
            ])
            ->where('status', 'paid')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('print.shift_print', compact('shift', 'sales'));
    }
}
