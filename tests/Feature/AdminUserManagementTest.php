<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_with_plan_and_role_labels(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $freeUser = User::factory()->create([
            'name' => 'Free User',
            'subscription_plan' => User::PLAN_FREE,
        ]);
        $premiumUser = User::factory()->create([
            'name' => 'Premium User',
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee($freeUser->email, false);
        $response->assertSee($premiumUser->email, false);
        $response->assertSee('Free', false);
        $response->assertSee('Premium', false);
        $response->assertSee('管理者', false);
        $response->assertSee('一般', false);
    }

    public function test_admin_can_delete_regular_user_with_matching_confirmation_email(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();

        AuctionItem::query()->create([
            'user_id' => $user->id,
            'management_id' => 'FORCE-DELETE-001',
            'title' => '削除対象の商品',
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user), [
                'confirmation_email' => $user->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'ユーザを削除しました。');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        $this->assertDatabaseMissing('auction_items', [
            'management_id' => 'FORCE-DELETE-001',
        ]);
    }

    public function test_admin_cannot_delete_regular_user_without_matching_confirmation_email(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user), [
                'confirmation_email' => 'wrong@example.com',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', '確認用メールアドレスが一致しないため、ユーザを削除しませんでした。');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_regular_user_without_confirmation_email(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertSessionHasErrors('confirmation_email');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin), [
                'confirmation_email' => $admin->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', '自分自身のアカウントは削除できません。');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $anotherAdmin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $anotherAdmin), [
                'confirmation_email' => $anotherAdmin->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', '管理者アカウントは削除できません。');

        $this->assertDatabaseHas('users', [
            'id' => $anotherAdmin->id,
        ]);
    }

    public function test_admin_delete_cancels_stripe_subscription_first(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/subscriptions?customer=cus_test_123*' => Http::response([
                'data' => [
                    [
                        'id' => 'sub_test_123',
                        'status' => 'active',
                    ],
                    [
                        'id' => 'sub_test_extra',
                        'status' => 'past_due',
                    ],
                ],
            ]),
            'https://api.stripe.com/v1/subscriptions/sub_test_123' => Http::response([
                'id' => 'sub_test_123',
            ]),
            'https://api.stripe.com/v1/subscriptions/sub_test_extra' => Http::response([
                'id' => 'sub_test_extra',
            ]),
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_test_123',
            'stripe_subscription_id' => 'sub_test_123',
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user), [
                'confirmation_email' => $user->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'ユーザを削除しました。');

        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && $request->url() === 'https://api.stripe.com/v1/subscriptions/sub_test_123');
        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && $request->url() === 'https://api.stripe.com/v1/subscriptions/sub_test_extra');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_delete_stops_when_stripe_cancellation_fails(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/subscriptions/sub_test_123' => Http::response([], 500),
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
            'stripe_subscription_id' => 'sub_test_123',
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user), [
                'confirmation_email' => $user->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', 'Stripe契約の停止に失敗したため、ユーザを削除しませんでした。');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_delete_stops_when_stripe_customer_subscription_lookup_fails(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/subscriptions?customer=cus_lookup_failure*' => Http::response([], 500),
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_lookup_failure',
            'stripe_subscription_id' => null,
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user), [
                'confirmation_email' => $user->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', 'Stripe契約の停止に失敗したため、ユーザを削除しませんでした。');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_delete_stops_when_stripe_customer_subscription_lookup_times_out(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake(function () {
            throw new ConnectionException('Connection timed out.');
        });

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_lookup_timeout',
            'stripe_subscription_id' => null,
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user), [
                'confirmation_email' => $user->email,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', 'Stripe契約の停止に失敗したため、ユーザを削除しませんでした。');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}
