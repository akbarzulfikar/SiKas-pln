<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_id',
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'category_id',
        'unit_id',
        'description',
        'amount',
        'evidence_file',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
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

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('transaction_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    // Accessors
    public function getEvidenceFileUrlAttribute()
    {
        if ($this->evidence_file) {
            return asset('storage/evidence/' . $this->evidence_file);
        }
        return null;
    }

    public function getTypeBadgeColorAttribute()
    {
        return $this->transaction_type === 'income' ? 'success' : 'danger';
    }

    public function getTypeDisplayAttribute()
    {
        return $this->transaction_type === 'income' ? 'Kas Masuk' : 'Kas Keluar';
    }

    // Helper methods untuk file evidence
    public function evidenceFileExists(): bool
    {
        if (!$this->evidence_file) return false;
        return file_exists(storage_path('app/public/evidence/' . $this->evidence_file));
    }

    /**
     * Check if evidence file is an image
     * Method yang digunakan oleh view
     */
    public function isImageFile(): bool
    {
        if (!$this->evidence_file) return false;
        
        $extension = pathinfo($this->evidence_file, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Alias untuk isImageFile() - untuk konsistensi
     */
    public function isImage(): bool
    {
        return $this->isImageFile();
    }

    /**
     * Check if evidence file is a PDF
     */
    public function isPdfFile(): bool
    {
        if (!$this->evidence_file) return false;
        
        $extension = pathinfo($this->evidence_file, PATHINFO_EXTENSION);
        return strtolower($extension) === 'pdf';
    }

    /**
     * Check if evidence file is a document (Word)
     */
    public function isDocumentFile(): bool
    {
        if (!$this->evidence_file) return false;
        
        $extension = pathinfo($this->evidence_file, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['doc', 'docx']);
    }

    /**
     * Get formatted file size
     */
    public function getFileSize(): string
    {
        if ($this->evidence_file && $this->evidenceFileExists()) {
            $bytes = filesize(storage_path('app/public/evidence/' . $this->evidence_file));
            return $this->formatBytes($bytes);
        }
        return '';
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get file extension in uppercase
     */
    public function getFileExtension(): string
    {
        if (!$this->evidence_file) return '';
        return strtoupper(pathinfo($this->evidence_file, PATHINFO_EXTENSION));
    }

    /**
     * Get file icon based on extension
     */
    public function getFileIcon(): string
    {
        if ($this->isImageFile()) {
            return 'fas fa-image text-success';
        } elseif ($this->isPdfFile()) {
            return 'fas fa-file-pdf text-danger';
        } elseif ($this->isDocumentFile()) {
            return 'fas fa-file-word text-primary';
        } else {
            return 'fas fa-file text-secondary';
        }
    }

    // Static methods untuk generate ID dan Number
    public static function generateTransactionId($unitId, $date): string
    {
        $dateStr = Carbon::parse($date)->format('Ymd');
        $prefix = 'TRX_' . strtoupper($unitId) . '_' . $dateStr . '_';
        
        $lastTransaction = self::where('transaction_id', 'like', $prefix . '%')
            ->orderBy('transaction_id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_id, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function generateTransactionNumber($unitId, $date): string
    {
        $dateStr = Carbon::parse($date)->format('Ymd');
        $count = self::whereDate('transaction_date', $date)
            ->where('unit_id', $unitId)
            ->count() + 1;

        return strtoupper($unitId) . '/' . $dateStr . '/' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}