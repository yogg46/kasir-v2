<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class itemReturModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'return_items_models';
    protected $fillable = ['return_id', 'product_id', 'batch_id', 'quantity', 'price', 'subtotal', 'reason'];

    public function toRetur()
    {
        return $this->belongsTo(returModel::class, 'return_id');
    }
    public function toProduk()
    {
        return $this->belongsTo(produkModel::class, 'product_id');
    }
    public function toBatch()
    {
        return $this->belongsTo(batchModel::class, 'batch_id');
    }
}
