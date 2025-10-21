<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class returnItemsModels extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'return_items_models';
    protected $fillable = ['return_id', 'product_id', 'batch_id', 'quantity', 'price', 'subtotal', 'reason'];

    public function toReturn()
    {
        return $this->belongsTo(returnsModels::class, 'return_id');
    }
    public function toProduct()
    {
        return $this->belongsTo(productsModels::class, 'product_id');
    }
    public function toBatch()
    {
        return $this->belongsTo(batchesModels::class, 'batch_id');
    }
}
