<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class salesModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sales_models';
    protected $fillable = [
        'branch_id',
        'cashier_id',
        'sale_date',
        'invoice_number',
        'subtotal',
        'total_amount',
        'discount_total',
        'payment_method',
        'status',
        'notes'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->invoice_number)) {
                $model->invoice_number = self::generateIncrementCode('INV');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd'); // Format tanggal: 20251017

            // Cari kode terakhir untuk hari ini, dan kunci baris agar tidak bentrok
            $latest = static::where('invoice_number', 'like', "{$prefix}-{$today}-%")
                ->orderByDesc('invoice_number')
                ->lockForUpdate()
                ->first();

            // Ambil nomor terakhir dari kode (setelah tanda "-")
            if ($latest && preg_match('/-(\d+)$/', $latest->code, $matches)) {
                $number = intval($matches[1]) + 1;
            } else {
                $number = 1; // Reset ke 1 jika belum ada untuk hari ini
            }

            // Format hasil akhir → WH-20251017-000001
            return sprintf('%s-%s-%06d', $prefix, $today, $number);
        });
    }

    public function toBranch()
    {
        return $this->belongsTo(branchesModel::class, 'branch_id');
    }
    public function toCashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function toItems()
    {
        return $this->hasMany(saleitemsModels::class, 'sale_id');
    }
}
