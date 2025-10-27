<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class diskonModel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'discounts_models';
    protected $fillable = [
        'price_id',
        'branch_id',
        'product_id',
        'type',
        'discount_percent',
        'discount_amount',
        'valid_from',
        'valid_until',
        'notes'
    ];

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('type', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%");
        });
    }

    // 🔗 Relasi
    public function toHarga()
    {
        return $this->belongsTo(hargaModel::class, 'price_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }
    public function toProduk()
    {
        return $this->belongsTo(produkModel::class, 'product_id');
    }
}
