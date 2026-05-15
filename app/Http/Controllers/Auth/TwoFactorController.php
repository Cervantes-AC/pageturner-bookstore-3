<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;
use App\Notifications\TwoFactorDisabled;
use App\Notifications\TwoFactorEnabled;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function showChallenge(): View
    {
        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();
        $storedCode = session('two_factor_code');
        $expiresAt = session('two_factor_code_expires_at');

        if (!$storedCode || !$expiresAt || now()->gt($expiresAt)) {
            return back()->with('error', 'Code expired. Please request a new one.');
        }

        if ($request->code !== $storedCode) {
            return back()->with('error', 'Invalid code. Please try again.');
        }

        session()->forget(['two_factor_code', 'two_factor_code_expires_at']);
        session(['two_factor_authenticated' => true]);

        return redirect()->intended(route('dashboard'));
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $this->sendTwoFactorCode($request->user());
        return back()->with('success', 'A new code has been sent to your email.');
    }

    public function showSetup(): View
    {
        $user = request()->user();
        $recoveryCodes = $user->twoFactorRecoveryCodes();

        return view('auth.two-factor-setup', compact('user', 'recoveryCodes'));
    }

    public function enable(Request $request): RedirectResponse
    {
        $user = $request->user();
        $secret = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = $user->generateRecoveryCode();
        }

        $user->two_factor_enabled = true;
        $user->two_factor_secret = bcrypt($secret);
        $user->setTwoFactorRecoveryCodes($codes);
        $user->save();

        $user->notify(new TwoFactorEnabled());
        $user->notify(new TwoFactorCode($secret));

        return redirect()->route('two-factor.setup')
            ->with('success', 'Two-factor authentication enabled. Check your email for your setup code.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        session()->forget('two_factor_authenticated');

        $user->notify(new TwoFactorDisabled());

        return redirect()->route('two-factor.setup')
            ->with('success', 'Two-factor authentication disabled.');
    }

    public function verifyRecoveryCode(Request $request): RedirectResponse
    {
        $request->validate(['recovery_code' => 'required|string']);

        $user = $request->user();
        $codes = $user->twoFactorRecoveryCodes();

        $index = array_search($request->recovery_code, $codes);

        if ($index === false) {
            return back()->with('error', 'Invalid recovery code.');
        }

        unset($codes[$index]);
        $user->setTwoFactorRecoveryCodes(array_values($codes));
        $user->save();

        session(['two_factor_authenticated' => true]);

        return redirect()->intended(route('dashboard'))
            ->with('info', 'Recovery code used. ' . count($codes) . ' remaining.');
    }

    public static function sendTwoFactorCode($user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        session(['two_factor_code' => $code]);
        session(['two_factor_code_expires_at' => now()->addMinutes(10)]);
        $user->notify(new TwoFactorCode($code));
    }
}
