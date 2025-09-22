<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $primaryKey = 'unit_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'unit_id',
        'unit_name', 
        'unit_type',
        'address',
        'phone',
        'email',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id', 'unit_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'unit_id', 'unit_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUP3($query)
    {
        return $query->where('unit_type', 'UP3');
    }

    public function scopeULP($query)
    {
        return $query->where('unit_type', 'ULP');
    }

    // Accessors - SUDAH BAGUS!
    public function getBadgeColorAttribute()
    {
        return $this->unit_type === 'UP3' ? 'success' : 'info';
    }

    public function getTotalIncomeAttribute()
    {
        return $this->transactions()
            ->where('transaction_type', 'income')
            ->sum('amount');
    }

    public function getTotalExpenseAttribute()
    {
        return $this->transactions()
            ->where('transaction_type', 'expense')
            ->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_income - $this->total_expense;
    }

    public function getActiveUsersCountAttribute()
    {
        return $this->users()->where('is_active', true)->count(); // Tambah filter is_active
    }

    public function getTransactionsThisMonthAttribute()
    {
        return $this->transactions()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();
    }

    // ✨ TAMBAHAN: Method helper yang berguna untuk view
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    public function getFormattedIncomeAttribute()
    {
        return 'Rp ' . number_format($this->total_income, 0, ',', '.');
    }

    public function getFormattedExpenseAttribute()
    {
        return 'Rp ' . number_format($this->total_expense, 0, ',', '.');
    }

    // ✨ TAMBAHAN: Helper untuk validasi
    public function canBeDeleted(): bool
    {
        return $this->users()->count() === 0 && $this->transactions()->count() === 0;
    }

    // ✨ TAMBAHAN: Helper untuk get display info
    public function getDisplayInfoAttribute()
    {
        return [
            'name' => $this->unit_name,
            'type' => $this->unit_type,
            'status' => $this->is_active ? 'Aktif' : 'Tidak Aktif',
            'users_count' => $this->users()->count(),
            'transactions_count' => $this->transactions()->count(),
            'balance' => $this->formatted_balance
        ];
    }
}