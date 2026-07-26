<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (TransportExceptionInterface) {
            return back()->withErrors([
                'email' => '認証メールの送信に失敗しました。管理者はメール設定を確認してください。',
            ]);
        }

        return back()->with('status', 'verification-link-sent');
    }
}
