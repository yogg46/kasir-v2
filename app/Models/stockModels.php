<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class stockModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'stock_models';
    protected $fillable = ['product_id', 'warehouse_id', 'branch_id', 'quantity'];

    public function toProduk()
    {
        return $this->belongsTo(produkModel::class, 'product_id');
    }
    public function toGudang()
    {
        return $this->belongsTo(gudangModel::class, 'warehouse_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }
}
