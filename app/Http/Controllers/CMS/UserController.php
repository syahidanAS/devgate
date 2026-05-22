<?php

namespace App\Http\Controllers\CMS;

// User administration for Superadmins
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display all users with search and role filters.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('cms.users.index', compact('users'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('cms.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|alpha_dash|max:255|unique:users,username',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'is_active' => 'required|boolean',
            'roles'     => 'required|array',
            'roles.*'   => 'exists:roles,name',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'username'          => $validated['username'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'is_active'         => $validated['is_active'],
            'email_verified_at' => now(), // Auto verify admin-created accounts
        ]);

        $user->assignRole($validated['roles']);

        return redirect()->route('cms.users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Show edit form for editing roles/status.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('cms.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user roles and active status.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
            'roles'     => 'required|array',
            'roles.*'   => 'exists:roles,name',
        ]);

        $user->update([
            'is_active' => $validated['is_active'],
        ]);

        // Sync Spatie roles
        $user->syncRoles($validated['roles']);

        return redirect()->route('cms.users.index')->with('success', "Akun user {$user->name} berhasil diperbarui.");
    }

    /**
     * Delete/Suspend user from the database.
     */
    public function destroy(User $user)
    {
        // Safety check: Prevent deleting logged-in superadmin
        if ($user->id === auth()->id()) {
            return redirect()->route('cms.users.index')->with('error', "Anda tidak dapat menghapus akun Anda sendiri.");
        }

        // Safety check: Prevent deleting the only superadmin
        if ($user->hasRole('superadmin')) {
            $superadminCount = User::role('superadmin')->count();
            if ($superadminCount <= 1) {
                return redirect()->route('cms.users.index')->with('error', "Sistem membutuhkan minimal satu Super Administrator.");
            }
        }

        $user->delete();

        return redirect()->route('cms.users.index')->with('success', "Pengguna {$user->name} berhasil dihapus.");
    }
}
