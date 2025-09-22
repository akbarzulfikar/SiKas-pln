<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'email',
        'password',
        'role',
        'unit_id',
        'nip',
        'position',
        'is_active',
        'last_login'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'created_by', 'user_id');
    }

    // ✅ PERBAIKAN: Hapus approvedTransactions karena tidak ada approval system lagi
    // public function approvedTransactions(): HasMany
    // {
    //     return $this->hasMany(Transaction::class, 'approved_by', 'user_id');
    // }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'created_by', 'user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // Accessors
    public function getRoleBadgeColorAttribute()
    {
        return $this->role === 'admin' ? 'danger' : 'primary';
    }

    public function getRoleDisplayNameAttribute()
    {
        return $this->role === 'admin' ? 'Administrator' : 'User';
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    // ✅ PERBAIKAN KRUSIAL: Lengkapi method generateUserId yang terpotong
    public static function generateUserId($unitId): string
    {
        $prefix = 'USR_' . strtoupper($unitId) . '_';
        
        $lastUser = self::where('user_id', 'like', $prefix . '%')
            ->orderBy('user_id', 'desc')
            ->first();

        if ($lastUser) {
            // Ambil 3 digit terakhir dari user ID
            $lastNumber = (int) substr($lastUser->user_id, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // ✅ Tambahan method helper untuk statistik
    public function getTotalTransactionsAttribute()
    {
        return $this->transactions()->count();
    }

    public function getThisMonthTransactionsAttribute()
    {
        return $this->transactions()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();
    }

    // ✅ Method untuk update last login
    public function updateLastLogin()
    {
        $this->update(['last_login' => now()]);
    }
}