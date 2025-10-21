<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class purchaseOrdersModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'purchase_orders_models';
    protected $fillable = [
        'suplier_id',
        'warehouse_id',
        'order_number',
        'order_date',
        'total',
        'status',
        'notes'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = self::generateIncrementCode('PO');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd'); // Format tanggal: 20251017

            // Cari kode terakhir untuk hari ini, dan kunci baris agar tidak bentrok
            $latest = static::where('order_number', 'like', "{$prefix}-{$today}-%")
                ->orderByDesc('order_number')
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

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where('order_number', 'like', "%{$keyword}%")
            ->orWhere('notes', 'like', "%{$keyword}%");
    }

    public function toSupplier()
    {
        return $this->belongsTo(supliersModels::class, 'suplier_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(warehosesModels::class, 'warehouse_id');
    }
    public function toItems()
    {
        return $this->hasMany(purchaseOrderItemsModels::class, 'purchase_order_id');
    }
}
