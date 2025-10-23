<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class hargaModel extends Model
{
    use HasUuids, HasFactory;
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
}
