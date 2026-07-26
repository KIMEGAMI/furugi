<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_with_plan_labels(): void
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
    }

    public function test_admin_can_force_delete_regular_user(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();

        AuctionItem::query()->create([
            'user_id' => $user->id,
            'management_id' => 'FORCE-DELETE-001',
            'title' => '退会対象の商品',
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'ユーザーを強制退会しました。');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        $this->assertDatabaseMissing('auction_items', [
            'management_id' => 'FORCE-DELETE-001',
        ]);
    }

    public function test_admin_cannot_force_delete_self(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', '自分自身のアカウントは強制退会できません。');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_force_delete_cancels_stripe_subscription_first(): void
    {
        config(['services.stripe.secret' => 'sk_test_example']);

        Http::fake([
            'https://api.stripe.com/v1/subscriptions/sub_test_123' => Http::response([
                'id' => 'sub_test_123',
            ]),
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
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'ユーザーを強制退会しました。');

        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && $request->url() === 'https://api.stripe.com/v1/subscriptions/sub_test_123');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
