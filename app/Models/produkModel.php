<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class produkModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'products_models';
    protected $fillable = [
        'category_id',
        // 'suplier_id',
        'code',
        'name',
        'description',
        'barcode',
        'type',
        'notes',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = self::generateIncrementCode('PRD');
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

        // Default: tidak include yang dihapus
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('code', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->orWhere('barcode', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    // Scope baru untuk include trashed
    public function scopeSearchWithTrashed($query, $keyword)
    {
        return $this->scopeSearch($query, $keyword)->withTrashed();
    }

    // Scope untuk hanya yang trashed
    public function scopeOnlyDeleted($query)
    {
        return $query->onlyTrashed();
    }

    // 🔗 Relasiw
    public function toKategori()
    {
        return $this->belongsTo(kategoriModel::class, 'category_id');
    }
    // public function toSupplier()
    // {
    //     return $this->belongsTo(supliersModels::class, 'suplier_id');
    // }
    public function toHarga()
    {
        return $this->hasMany(hargaModel::class, 'product_id');
    }
    public function toStocks()
    {
        return $this->hasMany(stockModels::class, 'product_id');
    }
    public function toBatches()
    {
        return $this->hasMany(batchModel::class, 'product_id');
    }


    // Accessor untuk status aktif/nonaktif
    protected function isActiveText(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_active ? 'Aktif' : 'Nonaktif'
        );
    }

    // Accessor untuk type badge color
    protected function typeBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->type) {
                'umkm' => 'primary',
                'seasonal' => 'warning',
                'regular' => 'success',
                default => 'secondary'
            }
        );
    }

    // Accessor untuk type label
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->type) {
                'umkm' => 'UMKM',
                'seasonal' => 'Musiman',
                'regular' => 'Regular',
                default => 'Unknown'
            }
        );
    }

    // Total stok semua gudang
    protected function totalStock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->toStocks()->sum('quantity')
        );
    }

    protected function deletedStatus(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->trashed() ? 'Dihapus' : 'Aktif'
        );
    }
}
