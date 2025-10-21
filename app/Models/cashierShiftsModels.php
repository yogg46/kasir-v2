<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class cashierShiftsModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cashier_shifts_models';
    protected $fillable = [
        'cashier_id',
        'branch_id',
        'shift_start',
        'shift_end',
        'initial_cash',
        'cash_in',
        'cash_out',
        'final_cash',
        'status'
    ];

    public function toCashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function toBranch()
    {
        return $this->belongsTo(branchesModel::class, 'branch_id');
    }
}
