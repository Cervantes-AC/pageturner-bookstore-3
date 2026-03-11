<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorSecret;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $code = $request->code;

        // Check if it's a recovery code
        if ($user->useRecoveryCode($code)) {
            $user->updateLoginInfo();
            session()->forget('2fa_required');
            return redirect()->intended(route('dashboard'));
        }

        // Check 2FA code
        $twoFactorSecret = TwoFactorSecret::where('user_id', $user->id)
            ->where('code', $code)
            ->where('used', false)
            ->first();

        if (!$twoFactorSecret || $twoFactorSecret->isExpired()) {
            return back()->withErrors([
                'code' => 'The provided code is invalid or has expired.'
            ]);
        }

        // Mark code as used
        $twoFactorSecret->markAsUsed();
        $user->updateLoginInfo();
        
        session()->forget('2fa_required');
        
        return redirect()->intended(route('dashboard'));
    }

    public function resend()
    {
        $user = auth()->user();
        
        // Generate new code
        $twoFactorSecret = TwoFactorSecret::generateForUser($user);
        
        // Send notification
        $user->notify(new TwoFactorCodeNotification($twoFactorSecret->code));
        
        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    public function enable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        
        if ($user->two_factor_enabled) {
            return back()->withErrors(['password' => '2FA is already enabled.']);
        }

        // Generate recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();
        
        $user->update(['two_factor_enabled' => true]);

        return view('profile.two-factor-recovery-codes', compact('recoveryCodes'));
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
        ]);

        // Delete any unused codes
        TwoFactorSecret::where('user_id', $user->id)->delete();

        return back()->with('status', '2FA has been disabled successfully.');
    }

    public function showRecoveryCodes()
    {
        $user = auth()->user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('profile.edit');
        }

        return view('profile.two-factor-recovery-codes', [
            'recoveryCodes' => $user->two_factor_recovery_codes ?? []
        ]);
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $recoveryCodes = $user->generateRecoveryCodes();

        return view('profile.two-factor-recovery-codes', compact('recoveryCodes'))
            ->with('status', 'Recovery codes have been regenerated.');
    }
}