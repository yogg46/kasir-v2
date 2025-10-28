<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class shiftKasirModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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

    protected $casts = [
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
    ];

    public function toKasir()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }

    protected function shiftDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->shift_end) return null;
                return \Carbon\Carbon::parse($this->shift_start)
                    ->diffInMinutes(\Carbon\Carbon::parse($this->shift_end));
            }
        );
    }

    // Selisih uang (expected vs actual)
    protected function cashDifference(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->final_cash - ($this->initial_cash + $this->cash_in - $this->cash_out)
        );
    }

    // Status selisih
    protected function cashStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diff = $this->cash_difference;
                if ($diff == 0) return 'balanced';
                return $diff > 0 ? 'over' : 'short';
            }
        );
    }
}
