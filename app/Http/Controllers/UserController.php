<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $role = $request->get('role', ''); // Filter by role
        $unit = $request->get('unit_id', ''); // Filter by unit
        $status = $request->get('status', ''); // Filter by status
        $search = $request->get('search', ''); // Search by name/username

        // Base query
        $query = User::with(['unit']);

        // Apply filters
        if ($role) {
            $query->where('role', $role);
        }

        if ($unit) {
            $query->where('unit_id', $unit);
        }

        if ($status !== '') {
            $query->where('is_active', $status === '1');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        // Get users with pagination
        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get statistics
        $stats = $this->getUserStatistics();

        // Get units for filter
        $units = Unit::where('is_active', true)->orderBy('unit_name')->get();

        return view('users.index', compact('users', 'stats', 'units', 'role', 'unit', 'status', 'search'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $units = Unit::where('is_active', true)->orderBy('unit_name')->get();
        return view('users.create', compact('units'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => 'required|in:admin,user',
            'unit_id' => 'required|exists:units,unit_id',
            'nip' => 'nullable|string|max:20|unique:users,nip',
            'position' => 'nullable|string|max:100',
            'is_active' => 'nullable|in:0,1,true,false,on,off'
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'unit_id.required' => 'Unit harus dipilih',
            'unit_id.exists' => 'Unit yang dipilih tidak valid',
            'nip.unique' => 'NIP sudah digunakan'
        ]);

        // Generate user ID
        $userId = User::generateUserId($request->unit_id);

        // Handle checkbox value
        $isActive = $request->has('is_active') ? true : false;

        User::create([
            'user_id' => $userId,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'unit_id' => $request->unit_id,
            'nip' => $request->nip,
            'position' => $request->position,
            'is_active' => $isActive
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan dengan ID: ' . $userId);
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::with(['unit', 'transactions', 'categories'])
            ->withCount(['transactions', 'categories'])
            ->findOrFail($id);

        // Get user statistics
        $userStats = $this->getUserActivityStats($user);

        // Get recent transactions
        $recentTransactions = $user->transactions()
            ->with(['category', 'unit'])
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        return view('users.show', compact('user', 'userStats', 'recentTransactions'));
    }

    /**
     * Show the form for editing user
     */
    public function edit($id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::findOrFail($id);
        $units = Unit::where('is_active', true)->orderBy('unit_name')->get();
        
        return view('users.edit', compact('user', 'units'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',user_id',
            'email' => 'required|email|max:100|unique:users,email,' . $id . ',user_id',
            'role' => 'required|in:admin,user',
            'unit_id' => 'required|exists:units,unit_id',
            'nip' => 'nullable|string|max:20|unique:users,nip,' . $id . ',user_id',
            'position' => 'nullable|string|max:100',
            'is_active' => 'nullable|in:0,1,true,false,on,off'
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'role.required' => 'Role harus dipilih',
            'unit_id.required' => 'Unit harus dipilih',
            'unit_id.exists' => 'Unit yang dipilih tidak valid',
            'nip.unique' => 'NIP sudah digunakan'
        ]);

        // Handle checkbox value
        $isActive = $request->has('is_active') ? true : false;

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'unit_id' => $request->unit_id,
            'nip' => $request->nip,
            'position' => $request->position,
            'is_active' => $isActive
        ]);

        return redirect()->route('users.show', $user->user_id)
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::findOrFail($id);

        // Prevent deleting current logged in user
        if ($user->user_id === Auth::user()->user_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        // Check if user has transactions
        if ($user->transactions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'User tidak dapat dihapus karena memiliki ' . 
                    $user->transactions()->count() . ' transaksi');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User "' . $userName . '" berhasil dihapus');
    }

    /**
     * Show form to change user password
     */
    public function showChangePassword($id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::findOrFail($id);
        return view('users.change-password', compact('user'));
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('users.show', $user->user_id)
            ->with('success', 'Password user berhasil diubah');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus($id)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $user = User::findOrFail($id);

        // Prevent disabling current logged in user
        if ($user->user_id === Auth::user()->user_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', 'User "' . $user->name . '" berhasil ' . $status);
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        $adminUsers = User::where('role', 'admin')->count();
        $regularUsers = User::where('role', 'user')->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
            'admin' => $adminUsers,
            'regular' => $regularUsers
        ];
    }

    /**
     * Get user activity statistics
     */
    private function getUserActivityStats($user)
    {
        $totalTransactions = $user->transactions()->count();
        $totalAmount = $user->transactions()->sum('amount');
        $totalIncome = $user->transactions()->where('transaction_type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('transaction_type', 'expense')->sum('amount');

        // Recent activity (last 30 days)
        $recentActivity = $user->transactions()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'recent_activity' => $recentActivity,
            'avg_amount' => $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0
        ];
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        // Check admin access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $role = $request->get('role', '');
        
        $query = User::with('unit');
        
        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $filename = 'users_' . ($role ?: 'all') . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'ID User',
                'Nama Lengkap',
                'Username',
                'Email',
                'Role',
                'Unit',
                'NIP',
                'Jabatan',
                'Status',
                'Last Login',
                'Tanggal Dibuat'
            ]);

            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->user_id,
                    $user->name,
                    $user->username,
                    $user->email,
                    $user->role === 'admin' ? 'Administrator' : 'User',
                    $user->unit ? $user->unit->unit_name : '-',
                    $user->nip ?: '-',
                    $user->position ?: '-',
                    $user->is_active ? 'Aktif' : 'Tidak Aktif',
                    $user->last_login ? $user->last_login->format('d/m/Y H:i') : '-',
                    $user->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}