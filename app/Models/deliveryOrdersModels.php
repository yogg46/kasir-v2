<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class deliveryOrdersModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'delivery_orders_models';
    protected $fillable = ['warehose_from', 'warehose_to', 'delivery_number', 'delivery_date', 'status', 'notes'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->delivery_number)) {
                $model->delivery_number = self::generateIncrementCode('DO');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd'); // Format tanggal: 20251017

            // Cari kode terakhir untuk hari ini, dan kunci baris agar tidak bentrok
            $latest = static::where('delivery_number', 'like', "{$prefix}-{$today}-%")
                ->orderByDesc('delivery_number')
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

    public function toWarehouseFrom()
    {
        return $this->belongsTo(warehosesModels::class, 'warehose_from');
    }
    public function toWarehouseTo()
    {
        return $this->belongsTo(warehosesModels::class, 'warehose_to');
    }
    public function toItems()
    {
        return $this->hasMany(deliveryOrderItemsModels::class, 'delivery_order_id');
    }
}
