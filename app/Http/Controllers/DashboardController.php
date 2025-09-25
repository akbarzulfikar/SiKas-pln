<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'all'); // all, today, week, month, year

        // Base query dengan filter unit untuk user biasa
        $baseQuery = Transaction::query();
        if ($user->role === 'user') {
            $baseQuery->where('unit_id', $user->unit_id);
        }

        // Apply period filter
        $baseQuery = $this->applyPeriodFilter($baseQuery, $period);

        // Week statistics (always week regardless of period filter)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Total balance calculation - REMOVED approved() scope
        $allTimeQuery = Transaction::query();
        if ($user->role === 'user') {
            $allTimeQuery->where('unit_id', $user->unit_id);
        }

        $totalIncome = (clone $allTimeQuery)->where('transaction_type', 'income')->sum('amount');
        $totalExpense = (clone $allTimeQuery)->where('transaction_type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // This week transactions
        $weekQuery = (clone $allTimeQuery)->whereBetween('transaction_date', [$startOfWeek, $endOfWeek]);
        $incomeThisWeek = (clone $weekQuery)->where('transaction_type', 'income')->sum('amount');
        $expenseThisWeek = (clone $weekQuery)->where('transaction_type', 'expense')->sum('amount');

        // Total transactions count (based on period filter)
        $totalTransactions = (clone $baseQuery)->count();

        // Today's transactions
        $todaysTransactions = Transaction::query()
            ->with(['category', 'creator', 'unit'])
            ->when($user->role === 'user', function ($query) use ($user) {
                return $query->where('unit_id', $user->unit_id);
            })
            ->whereDate('transaction_date', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Expense categories chart data (last 30 days) - DEFAULT: ALL UNITS untuk admin
        $expenseCategoriesQuery = Transaction::query()
            ->with('category')
            ->where('transaction_type', 'expense')
            ->where('transaction_date', '>=', Carbon::now()->subDays(30));

        if ($user->role === 'user') {
            $expenseCategoriesQuery->where('unit_id', $user->unit_id);
        }

        $expenseCategories = $expenseCategoriesQuery
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->having('total', '>', 0)
            ->orderBy('total', 'desc')
            ->limit(8) // Top 8 categories
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->category_name,
                    'total' => $item->total
                ];
            });

        // Get list of units for admin filter
        $units = collect();
        if ($user->role === 'admin') {
            $units = Unit::where('is_active', true)->orderBy('unit_name')->get();
        }

        // Additional data for admin
        $unitStats = collect();
        if ($user->role === 'admin') {
            $unitStats = Unit::with(['transactions'])->get()->map(function ($unit) {
                $totalIncome = $unit->transactions->where('transaction_type', 'income')->sum('amount');
                $totalExpense = $unit->transactions->where('transaction_type', 'expense')->sum('amount');

                return [
                    'unit_id' => $unit->unit_id,
                    'unit_name' => $unit->unit_name,
                    'unit_type' => $unit->unit_type,
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'balance' => $totalIncome - $totalExpense,
                    'total_transactions' => $unit->transactions->count()
                ];
            });
        }

        return view('dashboard.index', compact(
            'balance',
            'incomeThisWeek',
            'expenseThisWeek',
            'totalTransactions',
            'todaysTransactions',
            'expenseCategories',
            'unitStats',
            'period',
            'units'
        ));
    }

    /**
     * Get chart data for AJAX requests
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $type = $request->type ?? 'income';

        // Validate type
        if (!in_array($type, ['income', 'expense'])) {
            return response()->json(['error' => 'Invalid transaction type'], 400);
        }

        $query = Transaction::query()
            ->where('transaction_type', $type)
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        // Filter by unit for regular users
        if ($user->role === 'user') {
            $query->where('unit_id', $user->unit_id);
        }

        $data = $query->select(
            DB::raw('DATE(transaction_date) as date'),
            DB::raw('SUM(amount) as total')
        )
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => (float) $item->total
                ];
            });

        return response()->json($data);
    }


    // Tambahkan method ini di DashboardController.php

    /**
     * Get category breakdown for charts - FIXED VERSION
     */
    public function getCategoryData(Request $request)
    {
        try {
            $user = Auth::user();
            $type = $request->type ?? 'expense';
            $days = $request->days ?? 30;
            $unitId = $request->unit_id;

            // Validate type
            if (!in_array($type, ['income', 'expense'])) {
                return response()->json(['error' => 'Invalid transaction type'], 400);
            }

            $query = Transaction::query()
                ->with('category')
                ->where('transaction_type', $type)
                ->where('transaction_date', '>=', Carbon::now()->subDays($days));

            // Filter by unit
            if ($user->role === 'user') {
                $query->where('unit_id', $user->unit_id);
            } elseif ($user->role === 'admin' && $unitId) {
                $query->where('unit_id', $unitId);
            }

            $data = $query
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->having('total', '>', 0)
                ->orderBy('total', 'desc')
                ->limit(8)
                ->get()
                ->map(function ($item, $index) {
                    $colors = [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF'
                    ];

                    return [
                        'category' => $item->category->category_name,
                        'total' => (float) $item->total,
                        'color' => $colors[$index % count($colors)]
                    ];
                });

            return response()->json($data);
        } catch (Exception $e) {
            Log::error('Dashboard category data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load data'], 500);
        }
    }

    /**
     * Get real-time statistics
     */
    public function getRealtimeStats(Request $request)
    {
        $user = Auth::user();

        $baseQuery = Transaction::query();
        if ($user->role === 'user') {
            $baseQuery->where('unit_id', $user->unit_id);
        }

        // Today's stats
        $today = Carbon::today();
        $todayIncome = (clone $baseQuery)->where('transaction_type', 'income')
            ->whereDate('transaction_date', $today)->sum('amount');
        $todayExpense = (clone $baseQuery)->where('transaction_type', 'expense')
            ->whereDate('transaction_date', $today)->sum('amount');

        // This week stats
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $weekIncome = (clone $baseQuery)->where('transaction_type', 'income')
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])->sum('amount');
        $weekExpense = (clone $baseQuery)->where('transaction_type', 'expense')
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])->sum('amount');

        // Total balance
        $totalIncome = (clone $baseQuery)->where('transaction_type', 'income')->sum('amount');
        $totalExpense = (clone $baseQuery)->where('transaction_type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Total transactions
        $totalTransactions = (clone $baseQuery)->count();

        return response()->json([
            'today' => [
                'income' => (float) $todayIncome,
                'expense' => (float) $todayExpense,
                'balance' => (float) ($todayIncome - $todayExpense)
            ],
            'week' => [
                'income' => (float) $weekIncome,
                'expense' => (float) $weekExpense,
                'balance' => (float) ($weekIncome - $weekExpense)
            ],
            'total' => [
                'income' => (float) $totalIncome,
                'expense' => (float) $totalExpense,
                'balance' => (float) $balance,
                'transactions' => $totalTransactions
            ],
            'last_updated' => now()->toISOString()
        ]);
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, $period)
    {
        switch ($period) {
            case 'today':
                return $query->whereDate('transaction_date', Carbon::today());
            case 'week':
                return $query->whereBetween('transaction_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
            case 'month':
                return $query->whereMonth('transaction_date', Carbon::now()->month)
                    ->whereYear('transaction_date', Carbon::now()->year);
            case 'year':
                return $query->whereYear('transaction_date', Carbon::now()->year);
            default:
                return $query; // 'all' - no additional filter
        }
    }

    /**
     * Generate color for category charts
     */
    private function generateCategoryColor($categoryId)
    {
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#FF6384',
            '#C9CBCF',
            '#4BC0C0',
            '#FF6384'
        ];

        $index = crc32($categoryId) % count($colors);
        return $colors[abs($index)];
    }
}

