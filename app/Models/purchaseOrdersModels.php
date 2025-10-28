<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class purchaseOrdersModels extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
    protected $casts = [
        'order_date' => 'datetime',
        'total' => 'decimal:2',
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
            ->withTrashed()
                ->orderByDesc('order_number')
                ->lockForUpdate()
                ->first();

            // Ambil nomor terakhir dari kode (setelah tanda "-")
            if ($latest && preg_match('/-(\d+)$/', $latest->order_number, $matches)) {
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
    public function toGudang()
    {
        return $this->belongsTo(gudangModel::class, 'warehouse_id');
    }
    public function toItems()
    {
        return $this->hasMany(purchaseOrderItemsModels::class, 'purchase_order_id');
    }

     // Total formatted
    protected function totalFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->total, 0, ',', '.')
        );
    }

    // Status badge
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'draf' => 'secondary',
                'approved' => 'info',
                'received' => 'success',
                'cancelled' => 'danger',
                default => 'secondary'
            }
        );
    }

    // Status label
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'draf' => 'Draft',
                'approved' => 'Disetujui',
                'received' => 'Diterima',
                'cancelled' => 'Dibatalkan',
                default => 'Unknown'
            }
        );
    }
}
