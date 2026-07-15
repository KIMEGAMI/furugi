<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    private const STRIPE_API_BASE = 'https://api.stripe.com/v1';

    private const WEBHOOK_TOLERANCE_SECONDS = 300;

    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (! is_string($webhookSecret) || $webhookSecret === '') {
            Log::warning('Stripe webhook secret is not configured.');

            return response('Webhook secret missing', 500);
        }

        if (! $this->hasValidSignature($payload, $signature, $webhookSecret)) {
            return response('Invalid signature', 400);
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return response('Invalid payload', 400);
        }

        match (data_get($event, 'type')) {
            'checkout.session.completed' => $this->handleCheckoutCompleted(data_get($event, 'data.object', [])),
            'customer.subscription.updated' => $this->syncSubscription(data_get($event, 'data.object', [])),
            'customer.subscription.deleted' => $this->syncSubscription(data_get($event, 'data.object', [])),
            default => null,
        };

        return response('OK');
    }

    private function handleCheckoutCompleted(mixed $session): void
    {
        if (! is_array($session)) {
            return;
        }

        $subscriptionId = data_get($session, 'subscription');
        $customerId = data_get($session, 'customer');
        $userId = data_get($session, 'metadata.user_id') ?: data_get($session, 'client_reference_id');

        if (! is_string($subscriptionId) || $subscriptionId === '') {
            return;
        }

        $subscription = $this->fetchSubscription($subscriptionId);

        if ($subscription === []) {
            return;
        }

        if (is_numeric($userId)) {
            $subscription['metadata']['user_id'] = (string) $userId;
        }

        if (is_string($customerId)) {
            $subscription['customer'] = $customerId;
        }

        $this->syncSubscription($subscription);
    }

    private function syncSubscription(mixed $subscription): void
    {
        if (! is_array($subscription)) {
            return;
        }

        $subscriptionId = data_get($subscription, 'id');
        $customerId = data_get($subscription, 'customer');
        $userId = data_get($subscription, 'metadata.user_id');

        $user = null;

        if (is_numeric($userId)) {
            $user = User::query()->find((int) $userId);
        }

        if (! $user && is_string($customerId)) {
            $user = User::query()->where('stripe_customer_id', $customerId)->first();
        }

        if (! $user) {
            return;
        }

        $status = (string) data_get($subscription, 'status', '');
        $periodEnd = data_get($subscription, 'current_period_end');
        $isPremium = in_array($status, ['active', 'trialing'], true);

        $user->forceFill([
            'subscription_plan' => $isPremium ? User::PLAN_PREMIUM : User::PLAN_FREE,
            'subscription_status' => $status ?: null,
            'stripe_customer_id' => is_string($customerId) ? $customerId : $user->stripe_customer_id,
            'stripe_subscription_id' => is_string($subscriptionId) ? $subscriptionId : $user->stripe_subscription_id,
            'premium_started_at' => $isPremium && $user->premium_started_at === null ? now() : $user->premium_started_at,
            'premium_ends_at' => is_numeric($periodEnd) ? Carbon::createFromTimestamp((int) $periodEnd) : $user->premium_ends_at,
        ])->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchSubscription(string $subscriptionId): array
    {
        $secret = config('services.stripe.secret');

        if (! is_string($secret) || $secret === '') {
            return [];
        }

        $response = Http::withToken($secret)
            ->acceptJson()
            ->get(self::STRIPE_API_BASE.'/subscriptions/'.$subscriptionId);

        if ($response->failed()) {
            return [];
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : [];
    }

    private function hasValidSignature(string $payload, string $signature, string $webhookSecret): bool
    {
        $parts = collect(explode(',', $signature))
            ->mapWithKeys(function (string $part) {
                [$key, $value] = array_pad(explode('=', $part, 2), 2, null);

                return $key && $value ? [$key => $value] : [];
            });

        $timestamp = $parts->get('t');
        $expectedSignature = $parts->get('v1');

        if (! is_string($timestamp) || ! is_string($expectedSignature)) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > self::WEBHOOK_TOLERANCE_SECONDS) {
            return false;
        }

        $signedPayload = $timestamp.'.'.$payload;
        $computedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);

        return hash_equals($computedSignature, $expectedSignature);
    }
}
