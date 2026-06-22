<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'password' => bcrypt(str()->random(32)),
            ]);
        } else {
            $updated = false;

            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
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
}
