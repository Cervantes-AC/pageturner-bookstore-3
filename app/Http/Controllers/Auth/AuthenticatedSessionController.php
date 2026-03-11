<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginLog;
use App\Models\TwoFactorSecret;
use App\Notifications\TwoFactorCodeNotification;
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

        $user = Auth::user();
        
        // Log successful login
        LoginLog::logAttempt($request->email, true, $user);

        $request->session()->regenerate();

        // Check if 2FA is enabled
        if ($user->two_factor_enabled) {
            // Generate and send 2FA code
            $twoFactorSecret = TwoFactorSecret::generateForUser($user);
            $user->notify(new TwoFactorCodeNotification($twoFactorSecret->code));
            
            // Set session flag for 2FA requirement
            session(['2fa_required' => true]);
            
            return redirect()->route('two-factor.show');
        }

        // Update login info
        $user->updateLoginInfo();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
