<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        // HAPUS manual admin check karena sudah di-handle middleware
        
        $type = $request->get('type', ''); // Filter by transaction_type
        $status = $request->get('status', ''); // Filter by active status
        $search = $request->get('search', ''); // Search by name

        // Base query
        $query = Category::with(['creator']);

        // Apply filters
        if ($type) {
            $query->where('transaction_type', $type);
        }

        if ($status !== '') {
            $query->where('is_active', $status === '1');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Get categories with pagination
        $categories = $query->orderBy('transaction_type')
            ->orderBy('category_name')
            ->paginate(15)
            ->withQueryString();

        // Get statistics
        $stats = $this->getCategoryStatistics($type);

        return view('categories.index', compact('categories', 'stats', 'type', 'status', 'search'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:categories,category_name',
            'transaction_type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
        ], [
            'category_name.required' => 'Nama kategori harus diisi',
            'category_name.unique' => 'Nama kategori sudah digunakan',
            'transaction_type.required' => 'Jenis transaksi harus dipilih',
            'transaction_type.in' => 'Jenis transaksi tidak valid'
        ]);

        // Generate category ID
        $categoryId = Category::generateCategoryId($request->transaction_type);

        // Handle checkbox value properly
        $isActive = $request->has('is_active') ? true : false;

        Category::create([
            'category_id' => $categoryId,
            'category_name' => $request->category_name,
            'transaction_type' => $request->transaction_type,
            'description' => $request->description,
            'is_active' => $isActive,
            'created_by' => Auth::user()->user_id
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan dengan ID: ' . $categoryId);
    }

    /**
     * Display the specified category
     */
    public function show($id)
    {
        $category = Category::with(['creator'])
            ->withCount('transactions')
            ->findOrFail($id);

        // Get usage statistics
        $usageStats = $this->getCategoryUsageStats($category);

        // Get recent transactions
        $recentTransactions = $category->transactions()
            ->with(['unit', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        return view('categories.show', compact('category', 'usageStats', 'recentTransactions'));
    }

    /**
     * Show the form for editing category
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'category_name' => 'required|string|max:100|unique:categories,category_name,' . $id . ',category_id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean'
        ], [
            'category_name.required' => 'Nama kategori harus diisi',
            'category_name.unique' => 'Nama kategori sudah digunakan'
        ]);

        // Handle checkbox value properly
        $isActive = $request->has('is_active') ? true : false;

        $category->update([
            'category_name' => $request->category_name,
            'description' => $request->description,
            'is_active' => $isActive
        ]);

        return redirect()->route('categories.show', $category->category_id)
            ->with('success', 'Kategori berhasil diupdate');
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has transactions
        if ($category->transactions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan dalam ' .
                    $category->transactions()->count() . ' transaksi');
        }

        $categoryName = $category->category_name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori "' . $categoryName . '" berhasil dihapus');
    }

    /**
     * Toggle category status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);

        $category->update([
            'is_active' => !$category->is_active
        ]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', 'Kategori "' . $category->category_name . '" berhasil ' . $status);
    }

    /**
     * Get category statistics
     */
    private function getCategoryStatistics($type = '')
    {
        $query = Category::query();

        if ($type) {
            $query->where('transaction_type', $type);
        }

        $totalCategories = (clone $query)->count();
        $activeCategories = (clone $query)->where('is_active', true)->count();
        $inactiveCategories = $totalCategories - $activeCategories;

        $incomeCategories = Category::where('transaction_type', 'income')->count();
        $expenseCategories = Category::where('transaction_type', 'expense')->count();

        return [
            'total' => $totalCategories,
            'active' => $activeCategories,
            'inactive' => $inactiveCategories,
            'income' => $incomeCategories,
            'expense' => $expenseCategories
        ];
    }

    /**
     * Get category usage statistics
     */
    private function getCategoryUsageStats($category)
    {
        $totalTransactions = $category->transactions()->count();
        $totalAmount = $category->transactions()->sum('amount');

        // Monthly usage for last 12 months
        $monthlyUsage = $category->transactions()
            ->select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->where('transaction_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Usage by unit
        $unitUsage = $category->transactions()
            ->with('unit')
            ->select('unit_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('unit_id')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'monthly_usage' => $monthlyUsage,
            'unit_usage' => $unitUsage,
            'avg_amount' => $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0
        ];
    }

    /**
     * Export categories to CSV
     */
    public function export(Request $request)
    {
        $categories = Category::with('creator')->orderBy('transaction_type')->orderBy('category_name')->get();

        $filename = 'categories_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['ID', 'Nama Kategori', 'Jenis', 'Deskripsi', 'Status', 'Dibuat Oleh', 'Tanggal Dibuat']);
            
            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->category_id,
                    $category->category_name,
                    $category->transaction_type === 'income' ? 'Kas Masuk' : 'Kas Keluar',
                    $category->description ?: '-',
                    $category->is_active ? 'Aktif' : 'Tidak Aktif',
                    $category->creator->name ?? '-',
                    $category->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}