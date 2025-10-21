<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categoriesModels extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'categories_models';
    protected $fillable = ['name', 'description'];

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%");
    }

    public function toProducts()
    {
        return $this->hasMany(productsModels::class, 'category_id');
    }
}
