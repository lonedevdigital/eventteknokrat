<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleManagementController extends Controller
{
    private function checkAccess(): void
    {
        if (!auth()->user()->isSuperUser()) {
            abort(403, 'Hanya Superuser yang dapat mengakses halaman ini.');
        }
    }

    public function index()
    {
        $this->checkAccess();
        $roles = Role::orderByDesc('is_system')->orderBy('label')->get();
        $allPermissions = Role::allPermissions();
        return view('admin.role_management.index', compact('roles', 'allPermissions'));
    }

    public function create()
    {
        $this->checkAccess();
        $permissionGroups = Role::PERMISSION_GROUPS;
        return view('admin.role_management.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'name'          => ['required', 'regex:/^[a-z][a-z0-9_]*$/', 'max:50', 'unique:roles,name'],
            'label'         => ['required', 'string', 'max:100'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(Role::allPermissions()))],
        ], [
            'name.required' => 'Slug role wajib diisi.',
            'name.regex'    => 'Slug harus dimulai huruf kecil, hanya boleh huruf kecil, angka, dan underscore.',
            'name.unique'   => 'Slug role sudah digunakan.',
            'label.required' => 'Nama role wajib diisi.',
        ]);

        Role::create([
            'name'        => $request->name,
            'label'       => $request->label,
            'permissions' => $request->input('permissions', []),
            'is_system'   => false,
        ]);

        return redirect()->route('role-management.index')
            ->with('success', 'Role "' . $request->label . '" berhasil ditambahkan.');
    }

    public function edit(Role $roleManagement)
    {
        $this->checkAccess();
        $permissionGroups = Role::PERMISSION_GROUPS;
        return view('admin.role_management.edit', compact('roleManagement', 'permissionGroups'));
    }

    public function update(Request $request, Role $roleManagement)
    {
        $this->checkAccess();

        $request->validate([
            'label'         => ['required', 'string', 'max:100'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(Role::allPermissions()))],
        ], [
            'label.required' => 'Nama role wajib diisi.',
        ]);

        $roleManagement->update([
            'label'       => $request->label,
            'permissions' => $request->input('permissions', []),
        ]);

        return redirect()->route('role-management.index')
            ->with('success', 'Role "' . $roleManagement->label . '" berhasil diperbarui.');
    }

    public function destroy(Role $roleManagement)
    {
        $this->checkAccess();

        if ($roleManagement->is_system) {
            return back()->with('error', 'Role sistem tidak dapat dihapus.');
        }

        $userCount = User::where('role', $roleManagement->name)->count();
        if ($userCount > 0) {
            return back()->with('error', "Role ini masih digunakan oleh {$userCount} user. Pindahkan role user tersebut terlebih dahulu.");
        }

        $label = $roleManagement->label;
        $roleManagement->delete();

        return redirect()->route('role-management.index')
            ->with('success', "Role \"{$label}\" berhasil dihapus.");
    }
}
