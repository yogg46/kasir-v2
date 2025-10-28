<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class returModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'returns_models';
    protected $fillable = [
        'source_branch_id',
        'target_branch_id',
        'suplier_id',
        'return_number',
        'type',
        'notes',
        'return_date'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->return_number)) {
                $model->return_number = self::generateIncrementCode('RTN');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd'); // Format tanggal: 20251017

            // Cari kode terakhir untuk hari ini, dan kunci baris agar tidak bentrok
            $latest = static::where('return_number', 'like', "{$prefix}-{$today}-%")
            ->withTrashed()
                ->orderByDesc('return_number')
                ->lockForUpdate()
                ->first();

            // Ambil nomor terakhir dari kode (setelah tanda "-")
            if ($latest && preg_match('/-(\d+)$/', $latest->return_number, $matches)) {
                $number = intval($matches[1]) + 1;
            } else {
                $number = 1; // Reset ke 1 jika belum ada untuk hari ini
            }

            // Format hasil akhir → WH-20251017-000001
            return sprintf('%s-%s-%06d', $prefix, $today, $number);
        });
    }

    public function toSourceBranch()
    {
        return $this->belongsTo(cabangModel::class, 'source_branch_id');
    }

    public function toTargetBranch()
    {
        return $this->belongsTo(cabangModel::class, 'target_branch_id');
    }

    public function toSupplier()
    {
        return $this->belongsTo(supliersModels::class, 'suplier_id');
    }
    public function toItems()
    {
        return $this->hasMany(itemReturModel::class, 'return_id');
    }
}
