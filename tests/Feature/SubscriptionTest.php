<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_premium_plan(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('subscription.update'), [
                'plan' => User::PLAN_PREMIUM,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame(User::PLAN_PREMIUM, $user->subscription_plan);
        $this->assertTrue($user->isPremium());
        $this->assertNotNull($user->subscribed_at);
        $this->assertNull($user->subscription_cancelled_at);
    }

    public function test_user_can_cancel_premium_plan(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscribed_at' => now(),
            'subscription_cancelled_at' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('subscription.destroy'));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame(User::PLAN_FREE, $user->subscription_plan);
        $this->assertFalse($user->isPremium());
        $this->assertNotNull($user->subscription_cancelled_at);
    }

    public function test_unexpected_plan_cannot_be_selected(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('subscription.update'), [
                'plan' => 'legacy',
            ]);

        $response
            ->assertSessionHasErrors('plan')
            ->assertRedirect(route('profile.edit'));

        $this->assertSame(User::PLAN_FREE, $user->refresh()->subscription_plan);
    }
}
