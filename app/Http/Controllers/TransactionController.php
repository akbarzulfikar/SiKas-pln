<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters - pastikan semua variable didefinisikan
        $transactionType = $request->get('transaction_type', '');
        $categoryId = $request->get('category_id', '');
        $unitId = $request->get('unit_id', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $search = $request->get('search', '');

        // Base query
        $query = Transaction::with(['category', 'creator', 'unit']);

        // Filter by user role
        if ($user->role === 'user') {
            $query->where('unit_id', $user->unit_id);
        }

        // Apply filters
        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($unitId && $user->role === 'admin') {
            $query->where('unit_id', $unitId);
        }

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('category_name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Get transactions with pagination
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString(); // Keep query parameters in pagination links

        // Get filter options
        $units = collect();
        if ($user->role === 'admin') {
            $units = Unit::where('is_active', true)->orderBy('unit_name')->get();
        }

        // Get categories based on transaction type
        $categories = collect();
        if ($transactionType) {
            $categories = Category::where('is_active', true)
                ->where('transaction_type', $transactionType)
                ->orderBy('category_name')
                ->get();
        } else {
            $categories = Category::where('is_active', true)->orderBy('category_name')->get();
        }

        // Calculate summary statistics for current filter
        $summaryData = $this->calculateSummary($query);

        // PENTING: Pass SEMUA variables ke view
        return view('transactions.index', compact(
            'transactions',
            'units',
            'categories',
            'summaryData',
            'transactionType',
            'categoryId', 
            'unitId',
            'startDate',
            'endDate',
            'search'  // <- Ini yang penting!
        ));
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($query)
    {
        // Clone query untuk menghindari pagination di summary
        $summaryQuery = clone $query;
        $transactions = $summaryQuery->get();

        $totalIncome = $transactions->where('transaction_type', 'income')->sum('amount');
        $totalExpense = $transactions->where('transaction_type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;
        $totalTransactions = $transactions->count();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_balance' => $totalBalance,
            'total_transactions' => $totalTransactions,
        ];
    }

    /**
     * Get categories by transaction type (AJAX)
     */
    public function getCategoriesByType(Request $request)
    {
        $transactionType = $request->get('type');

        if (!in_array($transactionType, ['income', 'expense'])) {
            return response()->json(['error' => 'Invalid transaction type'], 400);
        }

        $categories = Category::where('is_active', true)
            ->where('transaction_type', $transactionType)
            ->orderBy('category_name')
            ->get(['category_id', 'category_name']);

        return response()->json($categories);
    }

    public function create()
    {
        $incomeCategories = Category::where('is_active', true)
            ->where('transaction_type', 'income')
            ->orderBy('category_name')->get();
            
        $expenseCategories = Category::where('is_active', true)
            ->where('transaction_type', 'expense')
            ->orderBy('category_name')->get();

        return view('transactions.create', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validation
        $request->validate([
            'transaction_date' => 'required|date|before_or_equal:today',
            'transaction_type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,category_id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ], [
            'transaction_date.required' => 'Tanggal transaksi harus diisi',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh lebih dari hari ini',
            'transaction_type.required' => 'Jenis transaksi harus dipilih',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal Rp 1',
            'evidence_file.mimes' => 'File harus berformat: jpg, jpeg, png, pdf, doc, docx',
            'evidence_file.max' => 'Ukuran file maksimal 5MB'
        ]);

        // Generate transaction ID and number
        $transactionId = Transaction::generateTransactionId($user->unit_id, $request->transaction_date);
        $transactionNumber = Transaction::generateTransactionNumber($user->unit_id, $request->transaction_date);

        $data = [
            'transaction_id' => $transactionId,
            'transaction_number' => $transactionNumber,
            'transaction_date' => $request->transaction_date,
            'transaction_type' => $request->transaction_type,
            'category_id' => $request->category_id,
            'unit_id' => $user->unit_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'created_by' => $user->user_id
        ];

        // Handle file upload
        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;

            // Store in storage/app/public/evidence
            $file->storeAs('evidence', $filename, 'public');
            $data['evidence_file'] = $filename;
        }

        $transaction = Transaction::create($data);

        return redirect()->route('transactions.show', $transaction->transaction_id)
            ->with('success', 'Transaksi berhasil ditambahkan dengan nomor: ' . $transactionNumber);
    }

    public function show($id)
    {
        $user = Auth::user();
        $transaction = Transaction::with(['category', 'creator', 'unit'])->findOrFail($id);

        // Check authorization for regular users
        if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat transaksi ini');
        }

        return view('transactions.show', compact('transaction'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $transaction = Transaction::findOrFail($id);

        // Check authorization
        if (
            $user->role === 'user' &&
            ($transaction->unit_id !== $user->unit_id || $transaction->created_by !== $user->user_id)
        ) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus transaksi ini');
        }

        // Delete evidence file if exists
        if ($transaction->evidence_file && Storage::disk('public')->exists('evidence/' . $transaction->evidence_file)) {
            Storage::disk('public')->delete('evidence/' . $transaction->evidence_file);
        }

        $transactionNumber = $transaction->transaction_number;
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi ' . $transactionNumber . ' berhasil dihapus');
    }

    public function showEvidence($id)
    {
        $user = Auth::user();
        $transaction = Transaction::with(['category', 'creator', 'unit'])->findOrFail($id);

        // Check authorization for regular users
        if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat transaksi ini');
        }

        if (!$transaction->evidence_file || !$transaction->evidenceFileExists()) {
            return redirect()->back()->with('error', 'Bukti transaksi tidak ditemukan');
        }

        return view('transactions.show-evidence', compact('transaction'));
    }
}