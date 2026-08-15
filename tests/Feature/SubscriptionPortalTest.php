<?php

namespace Tests\Feature;

use App\Models\SubscriptionCancellationFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_stripe_user_can_open_billing_portal(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/customers/cus_test' => Http::response([
                'id' => 'cus_test',
            ]),
            'https://api.stripe.com/v1/billing_portal/sessions' => Http::response([
                'url' => 'https://billing.stripe.test/session',
            ]),
        ]);

        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_test',
            'stripe_subscription_id' => 'sub_test',
        ]);

        $this
            ->actingAs($user)
            ->post(route('subscriptions.portal'))
            ->assertRedirect('https://billing.stripe.test/session');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/billing_portal/sessions'
            && $request['customer'] === 'cus_test');
    }

    public function test_unverified_email_does_not_resolve_billing_portal_customer_by_email(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/customers?email=claim%40example.com*' => Http::response([
                'data' => [
                    ['id' => 'cus_existing'],
                ],
            ]),
            'https://api.stripe.com/v1/subscriptions?customer=cus_existing*' => Http::response([
                'data' => [
                    [
                        'id' => 'sub_existing',
                        'customer' => 'cus_existing',
                        'status' => 'active',
                        'current_period_end' => now()->addMonth()->timestamp,
                    ],
                ],
            ]),
            'https://api.stripe.com/v1/billing_portal/sessions' => Http::response([
                'url' => 'https://billing.stripe.test/session',
            ]),
        ]);

        $user = User::factory()->unverified()->create([
            'email' => 'claim@example.com',
            'subscription_plan' => User::SUBSCRIPTION_INACTIVE,
            'subscription_status' => null,
            'stripe_customer_id' => null,
            'stripe_subscription_id' => null,
        ]);

        $this
            ->actingAs($user)
            ->post(route('subscriptions.portal'))
            ->assertRedirect(route('subscriptions.index'));

        Http::assertNotSent(fn ($request) => str_starts_with($request->url(), 'https://api.stripe.com/v1/customers?email='));
        Http::assertNotSent(fn ($request) => $request->url() === 'https://api.stripe.com/v1/billing_portal/sessions');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'subscription_plan' => User::SUBSCRIPTION_INACTIVE,
            'subscription_status' => null,
            'stripe_customer_id' => null,
            'stripe_subscription_id' => null,
        ]);
    }

    public function test_portal_not_configured_shows_actionable_error(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/customers/cus_test' => Http::response([
                'id' => 'cus_test',
            ]),
            'https://api.stripe.com/v1/billing_portal/sessions' => Http::response([
                'error' => [
                    'type' => 'invalid_request_error',
                    'code' => 'billing_portal_not_configured',
                ],
            ], 400),
        ]);

        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_test',
            'stripe_subscription_id' => 'sub_test',
        ]);

        $this
            ->actingAs($user)
            ->post(route('subscriptions.portal'))
            ->assertRedirect(route('subscriptions.index'))
            ->assertSessionHas('error', '契約管理画面を作成できませんでした。StripeダッシュボードでCustomer portalの設定を保存してください。');
    }

    public function test_cancel_feedback_is_recorded_before_portal_redirect(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/customers/cus_test' => Http::response([
                'id' => 'cus_test',
            ]),
            'https://api.stripe.com/v1/billing_portal/sessions' => Http::response([
                'url' => 'https://billing.stripe.test/session',
            ]),
        ]);

        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_test',
            'stripe_subscription_id' => 'sub_test',
        ]);

        $this
            ->actingAs($user)
            ->post(route('subscriptions.cancel-feedback'), [
                'reason' => SubscriptionCancellationFeedback::REASON_MISSING_FEATURE,
                'detail' => '追加機能がほしいです。',
            ])
            ->assertRedirect('https://billing.stripe.test/session');

        $this->assertDatabaseHas('subscription_cancellation_feedback', [
            'user_id' => $user->id,
            'reason' => SubscriptionCancellationFeedback::REASON_MISSING_FEATURE,
        ]);
    }
}
