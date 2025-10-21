<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class saleitemsModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'saleitems_models';
    protected $fillable = ['sale_id', 'product_id', 'unit_name', 'quantity', 'price', 'discount', 'subtotal'];

    public function toSale()
    {
        return $this->belongsTo(salesModels::class, 'sale_id');
    }
    public function toProduct()
    {
        return $this->belongsTo(productsModels::class, 'product_id');
    }
}
