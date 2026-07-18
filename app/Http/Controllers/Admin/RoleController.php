<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display role management page.
     */
    public function index(Request $request)
    {
        $roles = Role::withCount('users')->orderBy('created_at')->get();
        $modules = Role::availableModules();

        // Load selected role (from query string or first)
        $selectedRole = $request->filled('selected')
            ? $roles->firstWhere('id', $request->integer('selected'))
            : $roles->first();

        return view('admin.roles.index', compact('roles', 'modules', 'selectedRole'));
    }

    /**
     * Return role data as JSON.
     */
    public function show(Role $role)
    {
        return response()->json([
            'id'          => $role->id,
            'name'        => $role->name,
            'slug'        => $role->slug,
            'description' => $role->description,
            'permissions' => $role->permissions ?? [],
            'is_active'   => $role->is_active,
            'users_count' => $role->users()->count(),
        ]);
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        $modules = Role::availableModules();
        $permissions = [];
        foreach (array_keys($modules) as $module) {
            $permissions[$module] = [
                'view'   => false,
                'add'    => false,
                'edit'   => false,
                'delete' => false,
            ];
        }

        Role::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'permissions' => $permissions,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Role berhasil dibuat.');
    }

    /**
     * Update role name / description.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $role->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.roles.index', ['selected' => $role->id])
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Save the permission matrix for a role.
     */
    public function savePermissions(Request $request, Role $role)
    {
        $modules = Role::availableModules();
        $permissions = [];

        foreach (array_keys($modules) as $module) {
            $permissions[$module] = [
                'view'   => (bool) $request->input("permissions.{$module}.view"),
                'add'    => (bool) $request->input("permissions.{$module}.add"),
                'edit'   => (bool) $request->input("permissions.{$module}.edit"),
                'delete' => (bool) $request->input("permissions.{$module}.delete"),
            ];
        }

        $role->update(['permissions' => $permissions]);

        return back()->with('success', 'Hak akses berhasil disimpan.');
    }

    /**
     * Toggle role active status.
     */
    public function toggleStatus(Role $role)
    {
        $role->update(['is_active' => ! $role->is_active]);
        $text = $role->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Role berhasil {$text}.");
    }

    /**
     * Delete a role (only if no users assigned).
     */
    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'Role tidak dapat dihapus karena masih digunakan oleh ' . $role->users()->count() . ' user.']);
        }

        $role->delete();
        return back()->with('success', 'Role berhasil dihapus.');
    }
}
