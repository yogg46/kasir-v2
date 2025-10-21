<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class branchesModel extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'branches_models';
    protected $fillable = ['code', 'name', 'address', 'phone', 'is_head_office'];

    protected $casts = [
        'is_head_office' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = self::generateIncrementCode('TK');
            }
        });
    }

    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            // Ambil kode terakhir, lock baris agar tidak diakses bersamaan
            $latest = static::where('code', 'like', "{$prefix}%")
                ->orderByDesc('code')
                ->lockForUpdate()
                ->first();

            // Tentukan prefix numerik saat ini (contoh: WH2 → 2, WH → 1)
            $currentPrefixNumber = 1;

            if ($latest && preg_match('/^' . preg_quote($prefix, '/') . '(\d*)-(\d+)$/', $latest->code, $matches)) {
                $currentPrefixNumber = !empty($matches[1]) ? intval($matches[1]) : 1;
                $number = intval($matches[2]);
            } else {
                $number = 0;
            }

            // Naikkan nomor urut
            $number++;

            // Jika sudah melebihi batas, naikkan prefix (WH → WH2, dst)
            if ($number > 9999999) {
                $currentPrefixNumber++;
                $number = 1;
            }

            // Format prefix final: WH, WH2, WH3, dst
            $finalPrefix = $currentPrefixNumber > 1 ? "{$prefix}{$currentPrefixNumber}" : $prefix;

            return sprintf('%s-%07d', $finalPrefix, $number);
        });
    }

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('code', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->orWhere('address', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%");
        });
    }

    // 🔗 Relasi
    public function toWarehouses()
    {
        return $this->hasMany(warehosesModels::class, 'branch_id');
    }
    public function toUsers()
    {
        return $this->hasMany(User::class, 'branch_id');
    }
    public function toPrices()
    {
        return $this->hasMany(pricesModels::class, 'branch_id');
    }
}
