<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class purchaseOrderItemsModels extends Model
{
    use HasFactory, HasUuids;

   

    protected $table = 'purchase_order_items_models';
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity', 'price', 'subtotal'];

    public function toPurchaseOrder()
    {
        return $this->belongsTo(purchaseOrdersModels::class, 'purchase_order_id');
    }
    public function toProduct()
    {
        return $this->belongsTo(productsModels::class, 'product_id');
    }
}
