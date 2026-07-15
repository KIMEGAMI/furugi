<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private const DEMO_EMAIL = 'user@shinji.work';

    private const DEMO_PASSWORD = '12345678';

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request);
    }

    public function demo(Request $request): RedirectResponse
    {
        if (! Auth::attempt(['email' => self::DEMO_EMAIL, 'password' => self::DEMO_PASSWORD])) {
            throw ValidationException::withMessages([
                'email' => 'デモユーザーにログインできませんでした。管理者にお問い合わせください。',
            ]);
        }

        $this->activateDemoPremium();

        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectAfterAuthentication(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('status', 'verification-link-sent');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function activateDemoPremium(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $user->forceFill([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
            'premium_started_at' => $user->premium_started_at ?? now(),
            'premium_ends_at' => now()->addYears(10),
        ])->save();
    }
}
