<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role_id',
        'branch_id',
        'appearance'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;
        return $query->where(function ($q) use ($keyword) {
            $q->orWhere('name', 'like', "%{$keyword}%")

                ->orWhere('username', 'like', "%{$keyword}%");
        });
    }

    public function toRole()
    {
        return $this->belongsTo(roleModels::class, 'role_id');
    }
    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }

    public function getNamaCabangAttribute()
    {
        $role = strtolower(optional($this->toRole)->name ?? '');
        $cabang = optional($this->toCabang)->name;

        // Jika tidak ada cabang sama sekali
        if (!$this->toCabang) {
            return 'Tidak ada cabang';
        }

        // Jika bukan role gudang → tampilkan name cabang
        if ($role !== 'gudang') {
            return $cabang ?? 'Tidak ada cabang';
        }

        // Jika role gudang → cek jumlah gudang di cabangnya
        $gudangs = $this->toCabang->toGudang ?? collect();

        if ($gudangs->count() === 1) {
            return $gudangs->first()->name ?? 'Tidak ada gudang';
        }

        // Jika lebih dari satu gudang → tampilkan name cabang
        return $cabang ?? 'Tidak ada cabang';
    }
}
