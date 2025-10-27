<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class hargaModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $table = 'prices_models';
    protected $fillable = [
        'product_id',
        'branch_id',
        'unit_name',
        'unit_qty',
        'price',
        'old_price',
        'purchase_price',
        'is_default',
        'valid_from',
        'valid_until',
        'notes'
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('unit_name', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%");
        });
    }

    public function toPoduk()
    {
        return $this->belongsTo(produkModel::class, 'product_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }


    // Format harga rupiah
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->price, 0, ',', '.')
        );
    }

    protected function purchasePriceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->purchase_price
                ? 'Rp ' . number_format($this->purchase_price, 0, ',', '.')
                : '-'
        );
    }

    // Margin keuntungan
    protected function profitMargin(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->purchase_price
                ? round((($this->price - $this->purchase_price) / $this->purchase_price) * 100, 2)
                : 0
        );
    }

    // Status validity
    protected function isValid(): Attribute
    {
        return Attribute::make(
            get: function () {
                $now = now();
                $from = $this->valid_from ? \Carbon\Carbon::parse($this->valid_from) : null;
                $until = $this->valid_until ? \Carbon\Carbon::parse($this->valid_until) : null;

                if (!$from && !$until) return true;
                if ($from && $now < $from) return false;
                if ($until && $now > $until) return false;
                return true;
            }
        );
    }
}
