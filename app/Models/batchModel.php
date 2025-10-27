<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class batchModel extends Model
{
   use HasFactory, HasUuids, SoftDeletes;

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

    public function toProduk()
    {
        return $this->belongsTo(produkModel::class, 'product_id');
    }
    public function toGudang()
    {
        return $this->belongsTo(gudangModel::class, 'warehouse_id');
    }


    // Cek apakah sudah expired
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->expiry_date
                ? \Carbon\Carbon::parse($this->expiry_date)->isPast()
                : false
        );
    }

    // Hari sampai expired
    protected function daysUntilExpiry(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->expiry_date
                ? now()->diffInDays(\Carbon\Carbon::parse($this->expiry_date), false)
                : null
        );
    }

    // Status expiry (good/warning/danger)
    protected function expiryStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->expiry_date) return 'no-expiry';
                $days = $this->days_until_expiry;
                if ($days < 0) return 'expired';
                if ($days <= 30) return 'danger';
                if ($days <= 90) return 'warning';
                return 'good';
            }
        );
    }
}
