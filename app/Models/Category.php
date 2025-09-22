<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category_id',
        'category_name',
        'transaction_type',
        'description',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id', 'category_id');
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('transaction_type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('transaction_type', 'expense');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getBadgeColorAttribute()
    {
        return $this->transaction_type === 'income' ? 'success' : 'danger';
    }

    public function getIconAttribute()
    {
        return $this->transaction_type === 'income' ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
    }

    public function getTypeDisplayAttribute()
    {
        return $this->transaction_type === 'income' ? 'Kas Masuk' : 'Kas Keluar';
    }

    // Static methods
    public static function generateCategoryId($transactionType): string
    {
        $prefix = $transactionType === 'income' ? 'KM' : 'KK';
        
        $lastCategory = self::where('category_id', 'like', $prefix . '%')
            ->orderBy('category_id', 'desc')
            ->first();

        if ($lastCategory) {
            $lastNumber = (int) substr($lastCategory->category_id, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Helper methods
    public function canBeDeleted(): bool
    {
        return $this->transactions()->count() === 0;
    }
}