<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        $request->authenticate();

        // Get the authenticated user
        $user = Auth::user();

        // Check if user status is inactive
        if ($user->status === 'inactive') {
            // Logout the user immediately
            Auth::guard('web')->logout();

            // Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to deactivated account page
            return redirect()->route('account.deactivated');
        }

        // Update last login information
        $user->last_login_at = now();
        $user->last_login_device_id = $request->header('User-Agent') ?? 'Unknown';
        $user->current_session_id = $request->session()->getId();
        $user->save();

        // If user is active, proceed normally
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Clear current session ID before logout
        $user = Auth::user();
        if ($user) {
            $user->current_session_id = null;
            $user->save();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
