<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class deliveryOrderItemsModels extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'delivery_order_items_models';
    protected $fillable = ['delivery_order_id', 'product_id', 'quantity'];

    public function toDeliveryOrder()
    {
        return $this->belongsTo(deliveryOrdersModels::class, 'delivery_order_id');
    }
    public function toProduk()
    {
        return $this->belongsTo(produkModel::class, 'product_id');
    }
}
