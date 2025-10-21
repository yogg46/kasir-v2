<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class discountsModels extends Model
{
    use HasFactory, HasUuids;

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
    public function toPrice()
    {
        return $this->belongsTo(pricesModels::class, 'price_id');
    }
    public function toBranch()
    {
        return $this->belongsTo(branchesModel::class, 'branch_id');
    }
    public function toProduct()
    {
        return $this->belongsTo(productsModels::class, 'product_id');
    }
}
