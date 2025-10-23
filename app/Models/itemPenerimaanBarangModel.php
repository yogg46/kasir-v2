<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class itemPenerimaanBarangModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'goods_receipt_items_models';
    protected $fillable = ['goods_receipt_id', 'product_id', 'batch_id', 'quantity', 'price', 'subtotal'];

    public function toGoodsReceipt()
    {
        return $this->belongsTo(penerimaanBarangModel::class, 'goods_receipt_id');
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
