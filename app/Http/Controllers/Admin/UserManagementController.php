<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a list of staff users.
     */
    public function index(Request $request)
    {
        $query = User::with('roleModel')
            ->whereIn('role', ['admin', 'owner', 'doctor', 'frontdesk', 'super-admin']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            // Filter by role slug — join to roles table
            $query->whereHas('roleModel', fn($q) => $q->where('slug', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $roles = Role::active()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created staff user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role_id'  => 'required|exists:roles,id',
            'password' => 'required|string|min:8',
        ]);

        $role = Role::findOrFail($request->role_id);
        $roleEnum = $this->slugToEnum($role->slug);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $roleEnum,
            'role_id'   => $role->id,
            'is_active' => true,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Return user data as JSON (for modal).
     */
    public function show(User $user)
    {
        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'role_id'    => $user->role_id,
            'is_active'  => $user->is_active,
            'last_login_at' => $user->last_login_at?->format('d M Y, H:i'),
            'created_at' => $user->created_at->format('d M Y'),
        ]);
    }

    /**
     * Update an existing staff user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'password'=> 'nullable|string|min:8',
        ]);

        $role = Role::findOrFail($request->role_id);
        $roleEnum = $this->slugToEnum($role->slug);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role'    => $roleEnum,
            'role_id' => $role->id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Map role slug → users.role enum value.
     */
    private function slugToEnum(string $slug): string
    {
        return match($slug) {
            'owner'       => 'owner',
            'admin'       => 'admin',
            'doctor'      => 'doctor',
            'frontdesk'   => 'frontdesk',
            'super-admin' => 'admin',
            default       => 'frontdesk',
        };
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$statusText}.");
    }

    /**
     * Delete a staff user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
