<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_with_role_labels(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create([
            'name' => '一般ユーザ',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee($user->email, false);
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
}
