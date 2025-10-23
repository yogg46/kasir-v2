<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class shiftKasirModel extends Model
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

    public function toKasir()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }
}
