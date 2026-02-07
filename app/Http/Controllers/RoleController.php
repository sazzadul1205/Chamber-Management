<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // =======================
    // LIST ROLES
    // =======================
    public function index()
    {
        $roles = Role::latest()->get();
        return view('backend.roles.index', compact('roles'));
    }

    // =======================
    // CREATE ROLE
    // =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
        ]);

        Role::create($validated);

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role created successfully.');
    }

    // =======================
    // UPDATE ROLE
    // =======================
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
        ]);

        $role->update($validated);

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    // =======================
    // SOFT DELETE ROLE
    // =======================
    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return redirect()
                ->route('backend.roles.index')
                ->with('error', 'Cannot delete role. Users are assigned to this role.');
        }

        $role->delete();

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    // =======================
    // RESTORE SOFT DELETED ROLE
    // =======================
    public function restore(Role $role)
    {
        $role->restore();

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role restored successfully.');
    }

    // =======================
    // FORCE DELETE ROLE
    // =======================
    public function forceDelete(Role $role)
    {
        $role->forceDelete();

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role permanently deleted.');
    }
}
