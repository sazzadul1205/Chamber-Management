<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, role, and status filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Start query with role relationship
        $query = User::with('role');

        // Search by name, phone, email
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

        // Paginate results
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = Role::all();

        return view('backend.user.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        return view('backend.user.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Create new user
            $user = User::create([
                'role_id' => $request->role_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status,
            ]);

            // Commit transaction
            DB::commit();

            // Log the action
            Log::info("User created: {$user->id} by user ID: " . auth()->id());

            return redirect()->route('backend.user.index')
                ->with('success', 'User created successfully.');
                
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return back()->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        $user->load('role');
        return view('backend.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('backend.user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Validate request data
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'full_name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Validate password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|confirmed']);
        }

        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Store old data for logging
            $oldData = $user->toArray();

            // Update user data
            $updateData = [
                'role_id' => $request->role_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => $request->status,
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Commit transaction
            DB::commit();

            // Log the action with changes
            Log::info("User updated: {$user->id} by user ID: " . auth()->id(), [
                'old_data' => $oldData,
                'new_data' => $user->toArray()
            ]);

            return redirect()->route('backend.user.index')
                ->with('success', 'User updated successfully.');
                
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return back()->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle user status between active and inactive
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Request $request, User $user)
    {
        // Prevent toggling Super Admin status
        if ($user->isSuperAdmin()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot change Super Admin status.');
        }

        // Prevent users from toggling their own status
        if ($user->id === auth()->id()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot change your own status.');
        }

        // Check if user has permission (Admin or Super Admin)
        if (auth()->user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        // Validate current status
        $request->validate([
            'current_status' => 'required|in:active,inactive,suspended',
        ]);

        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Store old status for logging
            $oldStatus = $user->status;

            // Determine new status
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            // Update user status
            $user->update(['status' => $newStatus]);

            // Commit transaction
            DB::commit();

            // Log the action
            Log::info("User status toggled: {$user->id} from '{$oldStatus}' to '{$newStatus}' by user ID: " . auth()->id());

            return redirect()->route('backend.user.index')
                ->with('success', "User {$user->full_name} status changed to " . ucfirst($newStatus));
                
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return redirect()->route('backend.user.index')
                ->with('error', 'Failed to change user status: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified user
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Prevent deleting Super Admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot delete Super Admin user.');
        }

        // Prevent users from deleting their own account
        if ($user->id === auth()->id()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot delete your own account.');
        }

        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Soft delete user
            $user->delete();
            
            // Commit transaction
            DB::commit();

            // Log the action
            Log::info("User deleted: {$user->id} by user ID: " . auth()->id());

            return redirect()->route('backend.user.index')
                ->with('success', 'User deleted successfully.');
                
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return redirect()->route('backend.user.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted user
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        // Find user including soft deleted
        $user = User::withTrashed()->findOrFail($id);

        // Check authorization (only Admins/Super Admins)
        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        // Restore user
        $user->restore();

        // Log the action
        Log::info("User restored: {$user->id} by user ID: " . auth()->id());

        return redirect()->route('backend.user.index')
            ->with('success', 'User restored successfully.');
    }

    /**
     * Permanently delete a user
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        // Find user including soft deleted
        $user = User::withTrashed()->findOrFail($id);

        // Check authorization (only Admins/Super Admins)
        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent permanent deletion of Super Admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('backend.user.index')
                ->with('error', 'Cannot permanently delete Super Admin user.');
        }

        // Permanently delete user
        $user->forceDelete();

        // Log the action
        Log::info("User permanently deleted: {$user->id} by user ID: " . auth()->id());

        return redirect()->route('backend.user.index')
            ->with('success', 'User permanently deleted.');
    }

    /**
     * Reset or change user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request, $id)
    {
        // Find user
        $user = User::findOrFail($id);

        // Set validation rules
        $rules = [
            'new_password' => 'required|min:6|confirmed',
        ];

        // Add current password validation if user is changing their own password
        if (Auth::id() == $user->id) {
            $rules['current_password'] = 'required';
        }

        // Validate request
        $request->validate($rules);

        // Verify current password if user is changing their own password
        if (Auth::id() == $user->id) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Log the action and return appropriate success message
        Log::info("Password reset for user: {$user->id} by user ID: " . auth()->id());
        
        if (Auth::id() == $user->id) {
            return back()->with('success', 'Password changed successfully');
        } else {
            return back()->with('success', 'Password reset successfully');
        }
    }
}