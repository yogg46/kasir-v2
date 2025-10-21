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

    public function toProduct()
    {
        return $this->belongsTo(productsModels::class, 'product_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(warehosesModels::class, 'warehouse_id');
    }
    public function toBranch()
    {
        return $this->belongsTo(branchesModel::class, 'branch_id');
    }
}
