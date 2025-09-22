<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Unit;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Halaman utama laporan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get available units for admin
        $units = $user->role === 'admin' 
            ? Unit::where('is_active', true)->orderBy('unit_name')->get() 
            : collect([$user->unit]);
        
        // Get categories
        $categories = Category::where('is_active', true)
            ->orderBy('transaction_type')
            ->orderBy('category_name')
            ->get();

        // Default values
        $periodType = $request->get('period_type', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $transactionType = $request->get('transaction_type');
        $categoryId = $request->get('category_id');
        $unitId = $request->get('unit_id');

        // Calculate date range if not custom
        $dateRange = $this->calculateDateRange($periodType, $startDate, $endDate);

        // Generate report data
        $reportData = $this->generateReportData($dateRange, [
            'transaction_type' => $transactionType,
            'category_id' => $categoryId,
            'unit_id' => $user->role === 'admin' ? $unitId : $user->unit_id,
            'user_role' => $user->role,
            'user_unit_id' => $user->unit_id
        ]);

        return view('reports.index', compact(
            'units', 
            'categories', 
            'reportData',
            'periodType',
            'startDate',
            'endDate', 
            'transactionType',
            'categoryId',
            'unitId',
            'dateRange'
        ));
    }

    /**
     * Export ke PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'period_type' => 'required|in:weekly,monthly,yearly,custom,today,last_7_days,last_30_days',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|in:income,expense',
            'category_id' => 'nullable|exists:categories,category_id',
            'unit_id' => 'nullable|exists:units,unit_id'
        ]);

        // Calculate date range
        $dateRange = $this->calculateDateRange(
            $request->period_type, 
            $request->start_date, 
            $request->end_date
        );
        
        // Build filters dengan role-based restrictions
        $filters = [
            'transaction_type' => $request->transaction_type,
            'category_id' => $request->category_id,
            'unit_id' => $user->role === 'admin' ? $request->unit_id : $user->unit_id,
            'user_role' => $user->role,
            'user_unit_id' => $user->unit_id
        ];
        
        // Generate report data
        $reportData = $this->generateReportData($dateRange, $filters);
        
        // Generate HTML for PDF/Print
        try {
            $html = view('reports.pdf', [
                'transactions' => $reportData['transactions'],
                'summary' => $reportData['summary'],
                'dateRange' => $dateRange,
                'filters' => $filters,
                'user' => $user
            ])->render();
            
            // Generate filename
            $filename = 'laporan_kas_' . 
                        str_replace('-', '', $dateRange['start']) . '_' . 
                        str_replace('-', '', $dateRange['end']) . '_' .
                        date('YmdHis') . '.html';
            
            return response($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating report PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Print report (tampilkan HTML untuk print)
     */
    public function printReport(Request $request)
    {
        $user = Auth::user();
        
        // Sama seperti exportPdf tapi return view langsung
        $request->validate([
            'period_type' => 'required|in:weekly,monthly,yearly,custom,today,last_7_days,last_30_days',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|in:income,expense',
            'category_id' => 'nullable|exists:categories,category_id',
            'unit_id' => 'nullable|exists:units,unit_id'
        ]);

        $dateRange = $this->calculateDateRange(
            $request->period_type, 
            $request->start_date, 
            $request->end_date
        );
        
        $filters = [
            'transaction_type' => $request->transaction_type,
            'category_id' => $request->category_id,
            'unit_id' => $user->role === 'admin' ? $request->unit_id : $user->unit_id,
            'user_role' => $user->role,
            'user_unit_id' => $user->unit_id
        ];
        
        $reportData = $this->generateReportData($dateRange, $filters);
        
        return view('reports.print', [
            'transactions' => $reportData['transactions'],
            'summary' => $reportData['summary'],
            'dateRange' => $dateRange,
            'filters' => $filters,
            'user' => $user
        ]);
    }

    /**
     * Get categories by transaction type (AJAX)
     */
    public function getCategoriesByType(Request $request)
    {
        $type = $request->get('type');
        
        $categories = Category::where('is_active', true)
            ->when($type, function($query, $type) {
                return $query->where('transaction_type', $type);
            })
            ->orderBy('category_name')
            ->get(['category_id', 'category_name', 'transaction_type']);
            
        return response()->json($categories);
    }

    /**
     * Calculate date range berdasarkan period type
     */
    private function calculateDateRange($periodType, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();
        
        switch ($periodType) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay()->format('Y-m-d'),
                    'end' => $now->copy()->endOfDay()->format('Y-m-d'),
                    'label' => 'Hari Ini (' . $now->format('d F Y') . ')'
                ];
                
            case 'last_7_days':
                return [
                    'start' => $now->copy()->subDays(6)->startOfDay()->format('Y-m-d'),
                    'end' => $now->copy()->endOfDay()->format('Y-m-d'),
                    'label' => '7 Hari Terakhir (' . $now->copy()->subDays(6)->format('d M') . ' - ' . $now->format('d M Y') . ')'
                ];
                
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(29)->startOfDay()->format('Y-m-d'),
                    'end' => $now->copy()->endOfDay()->format('Y-m-d'),
                    'label' => '30 Hari Terakhir (' . $now->copy()->subDays(29)->format('d M') . ' - ' . $now->format('d M Y') . ')'
                ];
                
            case 'weekly':
                $startOfWeek = $now->copy()->startOfWeek();
                $endOfWeek = $now->copy()->endOfWeek();
                return [
                    'start' => $startOfWeek->format('Y-m-d'),
                    'end' => $endOfWeek->format('Y-m-d'),
                    'label' => 'Minggu Ini (' . $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y') . ')'
                ];
                
            case 'monthly':
                $startOfMonth = $now->copy()->startOfMonth();
                $endOfMonth = $now->copy()->endOfMonth();
                return [
                    'start' => $startOfMonth->format('Y-m-d'),
                    'end' => $endOfMonth->format('Y-m-d'),
                    'label' => 'Bulan Ini (' . $startOfMonth->format('F Y') . ')'
                ];
                
            case 'yearly':
                $startOfYear = $now->copy()->startOfYear();
                $endOfYear = $now->copy()->endOfYear();
                return [
                    'start' => $startOfYear->format('Y-m-d'),
                    'end' => $endOfYear->format('Y-m-d'),
                    'label' => 'Tahun Ini (' . $startOfYear->format('Y') . ')'
                ];
                
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate) : $now->copy()->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate) : $now;
                return [
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d'),
                    'label' => 'Periode Kustom (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')'
                ];
                
            default:
                return [
                    'start' => $now->copy()->startOfMonth()->format('Y-m-d'),
                    'end' => $now->format('Y-m-d'),
                    'label' => 'Bulan Ini'
                ];
        }
    }

    /**
     * Generate report data dengan optimized query
     */
    private function generateReportData($dateRange, $filters)
    {
        $user = Auth::user();
        
        // Base query untuk transaksi
        $query = Transaction::with(['category:category_id,category_name,transaction_type', 'unit:unit_id,unit_name'])
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']]);
        
        // Apply role-based filtering
        if ($user->role !== 'admin') {
            $query->where('unit_id', $user->unit_id);
        }
        
        // Apply filters
        if (!empty($filters['transaction_type'])) {
            $query->whereHas('category', function($q) use ($filters) {
                $q->where('transaction_type', $filters['transaction_type']);
            });
        }
        
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        if (!empty($filters['unit_id']) && $user->role === 'admin') {
            $query->where('unit_id', $filters['unit_id']);
        }
        
        // Get transactions
        $transactions = $query->orderBy('transaction_date', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->get();
        
        // Calculate summary
        $summary = $this->calculateSummary($transactions);
        
        return [
            'transactions' => $transactions,
            'summary' => $summary,
            'filters_applied' => $filters,
            'date_range' => $dateRange
        ];
    }

    /**
     * Calculate summary data
     */
    private function calculateSummary($transactions)
    {
        $incomeTotal = $transactions->where('category.transaction_type', 'income')->sum('amount');
        $expenseTotal = $transactions->where('category.transaction_type', 'expense')->sum('amount');
        $balance = $incomeTotal - $expenseTotal;
        $totalTransactions = $transactions->count();
        
        // Category breakdown
        $categoryBreakdown = $transactions->groupBy('category.category_name')
            ->map(function ($group) {
                return [
                    'category_name' => $group->first()->category->category_name,
                    'transaction_type' => $group->first()->category->transaction_type,
                    'total_amount' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })->sortByDesc('total_amount')->values();
        
        return [
            'income_total' => $incomeTotal,
            'expense_total' => $expenseTotal,
            'balance' => $balance,
            'total_transactions' => $totalTransactions,
            'category_breakdown' => $categoryBreakdown,
            'avg_transaction' => $totalTransactions > 0 ? ($incomeTotal + $expenseTotal) / $totalTransactions : 0
        ];
    }
}