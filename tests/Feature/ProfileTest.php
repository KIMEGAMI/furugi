<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_demo_user_cannot_see_delete_account_section(): void
    {
        $user = User::factory()->create([
            'email' => User::DEMO_EMAIL,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertDontSee('profile.destroy');
        $response->assertDontSee('confirm-user-deletion');
    }

    public function test_admin_user_cannot_see_delete_account_section(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertDontSee('profile.destroy');
        $response->assertDontSee('confirm-user-deletion');
    }

    public function test_demo_user_cannot_delete_their_account(): void
    {
        $user = User::factory()->create([
            'email' => User::DEMO_EMAIL,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHas('status', 'protected-account')
            ->assertRedirect('/profile');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }

    public function test_admin_user_cannot_delete_their_account(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHas('status', 'protected-account')
            ->assertRedirect('/profile');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }

    public function test_user_deletion_removes_their_auction_item_images(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Storage::disk('public')->put('auction-items/item.jpg', 'item-image');
        Storage::disk('public')->put('auction-items/item-sold.jpg', 'sold-image');
        Storage::disk('public')->put('auction-items/other-user.jpg', 'other-user-image');

        AuctionItem::create([
            'user_id' => $user->id,
            'management_id' => 'FRG-0001',
            'title' => 'Test item',
            'comment' => null,
            'platform' => AuctionItem::PLATFORM_OTHER,
            'image_path' => 'auction-items/item.jpg',
            'sold_image_path' => 'auction-items/item-sold.jpg',
            'status' => AuctionItem::STATUS_SOLD,
            'purchase_price' => 100,
            'sold_price' => 200,
            'sales_fee_rate' => 10,
            'sales_fee' => 20,
            'shipping_fee' => 30,
            'profit' => 50,
            'sold_at' => now(),
        ]);

        AuctionItem::create([
            'user_id' => $otherUser->id,
            'management_id' => 'FRG-0002',
            'title' => 'Other item',
            'comment' => null,
            'platform' => AuctionItem::PLATFORM_OTHER,
            'image_path' => 'auction-items/other-user.jpg',
            'sold_image_path' => null,
            'status' => AuctionItem::STATUS_SELLING,
            'purchase_price' => 100,
            'sold_price' => 200,
            'sales_fee_rate' => 10,
            'sales_fee' => 20,
            'shipping_fee' => 30,
            'profit' => 0,
            'sold_at' => null,
        ]);

        $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        Storage::disk('public')->assertMissing('auction-items/item.jpg');
        Storage::disk('public')->assertMissing('auction-items/item-sold.jpg');
        Storage::disk('public')->assertExists('auction-items/other-user.jpg');
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
