<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class notificationsModels extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'notifications_models';
    protected $fillable = [
        'user_id',
        'branch_id',
        'title',
        'message',
        'type',
        'meta_data',
        'is_read'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'is_read' => 'boolean'
    ];

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where('title', 'like', "%{$keyword}%")
            ->orWhere('message', 'like', "%{$keyword}%")
            ->orWhere('type', 'like', "%{$keyword}%");
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }
}
