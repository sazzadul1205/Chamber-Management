<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Get the user before authentication
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($field, $request->login)->first();

        // Store role_id in session BEFORE authentication
        if ($user) {
            session(['role_id' => $user->role_id]);
        }

        // Authenticate the user
        $request->authenticate();

        $request->session()->regenerate();

        // Also store role_id in session after authentication (redundant but safe)
        if ($user) {
            session(['role_id' => $user->role_id]);
            // Or store the entire role name
            session(['user_role' => $user->role->name ?? 'User']);
        }

        return redirect()->intended(route('backend.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear role_id from session
        session()->forget(['role_id', 'user_role']);

        return redirect('/');
    }
}
