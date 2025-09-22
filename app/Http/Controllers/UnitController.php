<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of units
     */
    public function index(Request $request)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $type = $request->get('type', ''); // Filter by type (UP3/ULP)
        $status = $request->get('status', ''); // Filter by status
        $search = $request->get('search', ''); // Search

        // Base query
        $query = Unit::withCount(['users', 'transactions']);

        // Apply filters
        if ($type) {
            $query->where('unit_type', $type);
        }

        if ($status !== '') {
            $query->where('is_active', $status === '1');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('unit_name', 'like', '%' . $search . '%')
                  ->orWhere('unit_id', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        // Get units with pagination
        $units = $query->orderBy('unit_type', 'asc')
            ->orderBy('unit_name', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total' => Unit::count(),
            'active' => Unit::where('is_active', true)->count(),
            'inactive' => Unit::where('is_active', false)->count(),
            'up3' => Unit::where('unit_type', 'UP3')->count(),
            'ulp' => Unit::where('unit_type', 'ULP')->count()
        ];

        return view('units.index', compact('units', 'stats', 'type', 'status', 'search'));
    }

    /**
     * Show the form for creating a new unit
     */
    public function create()
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        return view('units.create');
    }

    /**
     * Store a newly created unit
     */
    public function store(Request $request)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $request->validate([
            'unit_id' => 'required|string|max:10|unique:units,unit_id|alpha_dash',
            'unit_name' => 'required|string|max:100',
            'unit_type' => 'required|in:UP3,ULP',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'is_active' => 'nullable|boolean'
        ], [
            'unit_id.required' => 'ID Unit harus diisi',
            'unit_id.unique' => 'ID Unit sudah digunakan',
            'unit_id.alpha_dash' => 'ID Unit hanya boleh huruf, angka, dan underscore',
            'unit_name.required' => 'Nama Unit harus diisi',
            'unit_type.required' => 'Tipe Unit harus dipilih',
            'email.email' => 'Format email tidak valid'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Unit::create($data);

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil ditambahkan');
    }

    /**
     * Display the specified unit
     */
    public function show(Unit $unit)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Load relationships
        $unit->loadCount(['users', 'transactions']);

        // Get users in this unit
        $users = $unit->users()->orderBy('name')->get();

        // Get recent transactions
        $recentTransactions = $unit->transactions()
            ->with(['category', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $totalIncome = $unit->transactions()
            ->where('transaction_type', 'income')
            ->sum('amount');
        
        $totalExpense = $unit->transactions()
            ->where('transaction_type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return view('units.show', compact('unit', 'users', 'recentTransactions', 'totalIncome', 'totalExpense', 'balance'));
    }

    /**
     * Show the form for editing unit
     */
    public function edit(Unit $unit)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified unit
     */
    public function update(Request $request, Unit $unit)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $request->validate([
            'unit_name' => 'required|string|max:100',
            'unit_type' => 'required|in:UP3,ULP',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'is_active' => 'nullable|boolean'
        ], [
            'unit_name.required' => 'Nama Unit harus diisi',
            'unit_type.required' => 'Tipe Unit harus dipilih',
            'email.email' => 'Format email tidak valid'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $unit->update($data);

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil diperbarui');
    }

    /**
     * Remove the specified unit
     */
    public function destroy(Unit $unit)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Check if unit has users
        if ($unit->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Unit tidak dapat dihapus karena masih memiliki user. Hapus atau pindahkan user terlebih dahulu.');
        }

        // Check if unit has transactions
        if ($unit->transactions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Unit tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $unitName = $unit->unit_name;
        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit "' . $unitName . '" berhasil dihapus');
    }

    /**
     * Toggle unit status (active/inactive)
     */
    public function toggleStatus(Unit $unit)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $unit->update([
            'is_active' => !$unit->is_active
        ]);

        $status = $unit->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', 'Unit "' . $unit->unit_name . '" berhasil ' . $status);
    }
}