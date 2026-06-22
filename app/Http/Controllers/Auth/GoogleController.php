<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $googleId = (string) $googleUser->getId();
        $email = $googleUser->getEmail();

        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            if (! $email || ! $this->hasVerifiedGoogleEmail($googleUser)) {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'Google アカウントのメールアドレスを確認できませんでした。',
                    ]);
            }

            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Google User',
                'email' => $email,
                'google_id' => $googleId,
                'email_verified_at' => now(),
                'password' => bcrypt(str()->random(32)),
            ]);
        } else {
            $updated = false;

            if (! $user->google_id) {
                $user->google_id = $googleId;
                $updated = true;
            }

            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
                $updated = true;
            }

            if ($updated) {
                $user->save();
            }
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    private function hasVerifiedGoogleEmail(SocialiteUser $googleUser): bool
    {
        $rawUser = $googleUser->getRaw();
        $verified = $rawUser['email_verified'] ?? $rawUser['verified_email'] ?? false;

        return filter_var($verified, FILTER_VALIDATE_BOOLEAN);
    }
}
