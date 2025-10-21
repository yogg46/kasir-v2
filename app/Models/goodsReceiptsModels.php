<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class goodsReceiptsModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'goods_receipts_models';
    protected $fillable = [
        'purchase_order_id',
        'warehouse_id',
        'receipt_number',
        'receipt_date',
        'received_by',
        'status',
        'total',
        'notes'
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        // 'total' => 'decimal:2',
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->receipt_number)) {
                $model->receipt_number = self::generateIncrementCode('RCV');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd'); // Format tanggal: 20251017

            // Cari kode terakhir untuk hari ini, dan kunci baris agar tidak bentrok
            $latest = static::where('receipt_number', 'like', "{$prefix}-{$today}-%")
                ->orderByDesc('receipt_number')
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

    public function toPurchaseOrder()
    {
        return $this->belongsTo(purchaseOrdersModels::class, 'purchase_order_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(warehosesModels::class, 'warehouse_id');
    }
    public function toItems()
    {
        return $this->hasMany(goodsReceiptItemsModels::class, 'goods_receipt_id');
    }
}
