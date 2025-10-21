<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class batchesModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'batches_models';
    protected $fillable = ['product_id', 'warehouse_id', 'code', 'expiry_date', 'quantity'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = self::generateIncrementCode('BCH');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd'); // Format tanggal: 20251017

            // Cari kode terakhir untuk hari ini, dan kunci baris agar tidak bentrok
            $latest = static::where('code', 'like', "{$prefix}-{$today}-%")
                ->orderByDesc('code')
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

    public function toProduct()
    {
        return $this->belongsTo(productsModels::class, 'product_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(warehosesModels::class, 'warehouse_id');
    }
    //
}
