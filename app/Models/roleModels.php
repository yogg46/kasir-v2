<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class roleModels extends Model
{
    use HasFactory;

    protected $table = 'role_models';
    protected $fillable = ['role'];

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where('role', 'like', "%{$keyword}%");
    }

    public function toUsers()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
