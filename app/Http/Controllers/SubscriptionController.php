<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class SubscriptionController extends Controller
{
    private const STRIPE_API_BASE = 'https://api.stripe.com/v1';

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('subscriptions.index', [
            'user' => $user,
            'isPremium' => $user->isPremium(),
            'freeItemLimit' => User::FREE_AUCTION_ITEM_LIMIT,
            'price' => config('services.stripe.premium_amount', 480),
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $user = $request->user();
        $secret = config('services.stripe.secret');

        if (! is_string($secret) || $secret === '') {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', 'Stripeの設定が未完了です。STRIPE_SECRETを設定してください。');
        }

        try {
            $customerId = $this->ensureStripeCustomer($user, $secret);
        } catch (Throwable) {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '決済用の顧客情報を作成できませんでした。時間をおいて再度お試しください。');
        }

        $lineItem = $this->subscriptionLineItem();

        $response = Http::asForm()
            ->withToken($secret)
            ->post(self::STRIPE_API_BASE.'/checkout/sessions', [
                'mode' => 'subscription',
                'customer' => $customerId,
                'line_items' => [$lineItem],
                'success_url' => route('subscriptions.success', [], true).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscriptions.index', [], true),
                'client_reference_id' => (string) $user->id,
                'metadata[user_id]' => (string) $user->id,
            ]);

        if ($response->failed()) {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '決済画面を作成できませんでした。時間をおいて再度お試しください。');
        }

        $checkoutUrl = $response->json('url');

        if (! is_string($checkoutUrl) || $checkoutUrl === '') {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '決済画面のURLを取得できませんでした。');
        }

        return redirect()->away($checkoutUrl);
    }

    public function success(): View
    {
        return view('subscriptions.success');
    }

    public function portal(Request $request): RedirectResponse
    {
        $user = $request->user();
        $secret = config('services.stripe.secret');

        if (! is_string($secret) || $secret === '' || ! is_string($user->stripe_customer_id) || $user->stripe_customer_id === '') {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '請求管理画面を開けませんでした。');
        }

        $response = Http::asForm()
            ->withToken($secret)
            ->post(self::STRIPE_API_BASE.'/billing_portal/sessions', [
                'customer' => $user->stripe_customer_id,
                'return_url' => route('subscriptions.index', [], true),
            ]);

        if ($response->failed()) {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '請求管理画面を作成できませんでした。');
        }

        $portalUrl = $response->json('url');

        if (! is_string($portalUrl) || $portalUrl === '') {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '請求管理画面のURLを取得できませんでした。');
        }

        return redirect()->away($portalUrl);
    }

    private function ensureStripeCustomer(User $user, string $secret): string
    {
        if (is_string($user->stripe_customer_id) && $user->stripe_customer_id !== '') {
            return $user->stripe_customer_id;
        }

        $response = Http::asForm()
            ->withToken($secret)
            ->post(self::STRIPE_API_BASE.'/customers', [
                'email' => $user->email,
                'name' => $user->name,
                'metadata[user_id]' => (string) $user->id,
            ]);

        $response->throw();

        $customerId = (string) $response->json('id');
        $user->forceFill(['stripe_customer_id' => $customerId])->save();

        return $customerId;
    }

    /**
     * @return array<string, mixed>
     */
    private function subscriptionLineItem(): array
    {
        $priceId = config('services.stripe.premium_price_id');

        if (is_string($priceId) && $priceId !== '') {
            return [
                'price' => $priceId,
                'quantity' => 1,
            ];
        }

        return [
            'quantity' => 1,
            'price_data' => [
                'currency' => config('services.stripe.premium_currency', 'jpy'),
                'unit_amount' => (int) config('services.stripe.premium_amount', 480),
                'recurring' => ['interval' => 'month'],
                'product_data' => [
                    'name' => 'FURUGI Premium',
                    'description' => 'Inventory, sales analysis, CSV import/export, and premium insights.',
                ],
            ],
        ];
    }
}
