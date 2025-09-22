<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionService
{
    /**
     * Create a new transaction
     */
    public function createTransaction(array $data, ?UploadedFile $file = null): Transaction
    {
        // Handle file upload if provided
        if ($file) {
            $data['evidence_file'] = $this->uploadEvidenceFile($file);
        }

        return Transaction::create($data);
    }

    /**
     * Upload evidence file
     */
    private function uploadEvidenceFile(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;

        // Store file in evidence directory
        $file->storeAs('evidence', $filename, 'public');

        return $filename;
    }

    /**
     * Get transaction statistics
     */
    public function getTransactionStatistics(?string $unitId = null, array $filters = []): array
    {
        $query = Transaction::query();

        // Apply unit filter
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        // Apply date range filter
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        // Calculate statistics
        $totalIncome = (clone $query)->income()->sum('amount');
        $totalExpense = (clone $query)->expense()->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;
        $totalTransactions = $query->count();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_balance' => $totalBalance,
            'total_transactions' => $totalTransactions,
            'formatted_income' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
            'formatted_expense' => 'Rp ' . number_format($totalExpense, 0, ',', '.'),
            'formatted_balance' => 'Rp ' . number_format($totalBalance, 0, ',', '.'),
        ];
    }

    /**
     * Get monthly transaction trends
     */
    public function getMonthlyTrends(?string $unitId = null, int $months = 6): array
    {
        $query = Transaction::query();

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $transactions = $query->dateRange($startDate, $endDate)
            ->selectRaw('
                YEAR(transaction_date) as year,
                MONTH(transaction_date) as month,
                transaction_type,
                SUM(amount) as total_amount
            ')
            ->groupBy('year', 'month', 'transaction_type')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Format data for charts
        $trends = [];
        for ($i = 0; $i < $months; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key = $date->format('Y-m');
            
            $trends[$key] = [
                'period' => $date->format('M Y'),
                'income' => 0,
                'expense' => 0,
                'balance' => 0
            ];
        }

        foreach ($transactions as $transaction) {
            $key = sprintf('%d-%02d', $transaction->year, $transaction->month);
            if (isset($trends[$key])) {
                $trends[$key][$transaction->transaction_type] = $transaction->total_amount;
            }
        }

        // Calculate balance for each month
        foreach ($trends as &$trend) {
            $trend['balance'] = $trend['income'] - $trend['expense'];
        }

        return array_values($trends);
    }

    /**
     * Get category breakdown
     */
    public function getCategoryBreakdown(?string $unitId = null, array $filters = []): array
    {
        $query = Transaction::with('category')
            ->selectRaw('
                category_id,
                transaction_type,
                COUNT(*) as transaction_count,
                SUM(amount) as total_amount
            ')
            ->groupBy('category_id', 'transaction_type');

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        $results = $query->get();

        $breakdown = [
            'income' => [],
            'expense' => []
        ];

        foreach ($results as $result) {
            $type = $result->transaction_type;
            $breakdown[$type][] = [
                'category_id' => $result->category_id,
                'category_name' => $result->category->category_name ?? 'Unknown',
                'transaction_count' => $result->transaction_count,
                'total_amount' => $result->total_amount,
                'formatted_amount' => 'Rp ' . number_format($result->total_amount, 0, ',', '.'),
            ];
        }

        // Sort by amount descending
        usort($breakdown['income'], fn($a, $b) => $b['total_amount'] <=> $a['total_amount']);
        usort($breakdown['expense'], fn($a, $b) => $b['total_amount'] <=> $a['total_amount']);

        return $breakdown;
    }

    /**
     * Delete transaction and its evidence file
     */
    public function deleteTransaction(string $transactionId): bool
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Delete evidence file if exists
        if ($transaction->evidence_file && Storage::disk('public')->exists('evidence/' . $transaction->evidence_file)) {
            Storage::disk('public')->delete('evidence/' . $transaction->evidence_file);
        }

        return $transaction->delete();
    }

    /**
     * Get transactions with filters and pagination
     */
    public function getFilteredTransactions(array $filters = [], int $perPage = 15)
    {
        $query = Transaction::withDetails();

        // Apply unit filter
        if (!empty($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }

        // Apply transaction type filter
        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply date range filter
        if (!empty($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('category_name', 'like', '%' . $search . '%');
                  });
            });
        }

        return $query->orderBy('transaction_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage)
                    ->withQueryString();
    }

    /**
     * Validate transaction data before creation
     */
    public function validateTransactionData(array $data): array
    {
        $errors = [];

        // Check if category exists and is active
        $category = Category::where('category_id', $data['category_id'])
                           ->where('is_active', true)
                           ->first();

        if (!$category) {
            $errors[] = 'Kategori tidak ditemukan atau tidak aktif';
        } elseif ($category->transaction_type !== $data['transaction_type']) {
            $errors[] = 'Jenis transaksi tidak sesuai dengan kategori';
        }

        // Check if unit exists and is active
        $unit = Unit::where('unit_id', $data['unit_id'])
                   ->where('is_active', true)
                   ->first();

        if (!$unit) {
            $errors[] = 'Unit tidak ditemukan atau tidak aktif';
        }

        // Validate transaction date
        if (Carbon::parse($data['transaction_date'])->isFuture()) {
            $errors[] = 'Tanggal transaksi tidak boleh lebih dari hari ini';
        }

        return $errors;
    }

    /**
     * Get recent transactions for dashboard
     */
    public function getRecentTransactions(?string $unitId = null, int $limit = 5): array
    {
        $query = Transaction::withDetails();

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        return $query->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
    }
}