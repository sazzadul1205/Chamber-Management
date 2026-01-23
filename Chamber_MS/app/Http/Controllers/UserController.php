<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = Role::all();

        return view('backend.user.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('backend.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'role_id' => $request->role_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status,
            ]);

            DB::commit();

            Log::info("User created: {$user->id} by " . auth()->id());

            return redirect()->route('backend.user.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('backend.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('backend.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'full_name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|confirmed']);
        }

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();

            $user->update([
                'role_id' => $request->role_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => $request->status,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            DB::commit();

            Log::info("User updated: {$user->id} by " . auth()->id(), ['old' => $oldData, 'new' => $user->toArray()]);

            return redirect()->route('backend.user.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot delete Super Admin user.');
        }

        if ($user->id == auth()->id()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot delete your own account.');
        }

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();

            Log::info("User deleted: {$user->id} by " . auth()->id());

            return redirect()->route('backend.user.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('backend.user.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        $user->restore();

        Log::info("User restored: {$user->id} by " . auth()->id());

        return redirect()->route('backend.user.index')
            ->with('success', 'User restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot permanently delete Super Admin user.');
        }

        $user->forceDelete();

        Log::info("User permanently deleted: {$user->id} by " . auth()->id());

        return redirect()->route('backend.user.index')
            ->with('success', 'User permanently deleted.');
    }
}
