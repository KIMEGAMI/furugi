<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionItemDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_duplicate_item_groups(): void
    {
        $user = $this->premiumUser();
        $otherUser = User::factory()->create();

        $this->createAuctionItem($user, 'A-1', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);
        $this->createAuctionItem($user, 'A-2', '  xlarge　tシャツ  ', AuctionItem::PLATFORM_YAHOO);
        $this->createAuctionItem($user, 'A-3', 'XLARGE Tシャツ', AuctionItem::PLATFORM_MERCARI);
        $this->createAuctionItem($otherUser, 'A-4', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);

        $response = $this
            ->actingAs($user)
            ->get(route('auction-items.duplicates'));

        $response->assertOk();
        $response->assertSee('A-1');
        $response->assertSee('A-2');
        $response->assertDontSee('A-3');
        $response->assertDontSee('A-4');
    }

    public function test_user_can_delete_duplicate_items_except_selected_keep_item(): void
    {
        $user = $this->premiumUser();
        $otherUser = User::factory()->create();

        $keepItem = $this->createAuctionItem($user, 'A-1', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);
        $deleteItem = $this->createAuctionItem($user, 'A-2', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);
        $otherPlatformItem = $this->createAuctionItem($user, 'A-3', 'XLARGE Tシャツ', AuctionItem::PLATFORM_MERCARI);
        $otherUserItem = $this->createAuctionItem($otherUser, 'A-4', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);

        $response = $this
            ->actingAs($user)
            ->delete(route('auction-items.duplicates.destroy'), [
                'keep_item_id' => $keepItem->id,
            ]);

        $response->assertRedirect(route('auction-items.duplicates'));

        $this->assertNotNull($keepItem->fresh());
        $this->assertNull($deleteItem->fresh());
        $this->assertNotNull($otherPlatformItem->fresh());
        $this->assertNotNull($otherUserItem->fresh());
    }

    public function test_user_can_delete_duplicate_items_except_latest_item(): void
    {
        $user = $this->premiumUser();

        $oldItem = $this->createAuctionItem($user, 'A-1', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);
        $latestItem = $this->createAuctionItem($user, 'A-2', '  xlarge　tシャツ  ', AuctionItem::PLATFORM_YAHOO);
        $otherPlatformItem = $this->createAuctionItem($user, 'A-3', 'XLARGE Tシャツ', AuctionItem::PLATFORM_MERCARI);

        $oldItem->forceFill(['created_at' => now()->subDay()])->save();
        $latestItem->forceFill(['created_at' => now()])->save();

        $response = $this
            ->actingAs($user)
            ->delete(route('auction-items.duplicates.destroy'), [
                'delete_mode' => 'latest',
                'duplicate_key' => AuctionItem::PLATFORM_YAHOO.'|xlarge tシャツ',
            ]);

        $response->assertRedirect(route('auction-items.duplicates'));

        $this->assertNull($oldItem->fresh());
        $this->assertNotNull($latestItem->fresh());
        $this->assertNotNull($otherPlatformItem->fresh());
    }

    public function test_user_can_delete_all_duplicate_groups_except_each_latest_item(): void
    {
        $user = $this->premiumUser();
        $otherUser = User::factory()->create();

        $oldShirt = $this->createAuctionItem($user, 'A-1', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);
        $latestShirt = $this->createAuctionItem($user, 'A-2', 'xlarge tシャツ', AuctionItem::PLATFORM_YAHOO);
        $oldPants = $this->createAuctionItem($user, 'A-3', 'Levis Denim', AuctionItem::PLATFORM_MERCARI);
        $latestPants = $this->createAuctionItem($user, 'A-4', ' levis　denim ', AuctionItem::PLATFORM_MERCARI);
        $otherPlatformItem = $this->createAuctionItem($user, 'A-5', 'XLARGE Tシャツ', AuctionItem::PLATFORM_MERCARI);
        $otherUserItem = $this->createAuctionItem($otherUser, 'A-6', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);

        $oldShirt->forceFill(['created_at' => now()->subDays(2)])->save();
        $latestShirt->forceFill(['created_at' => now()->subDay()])->save();
        $oldPants->forceFill(['created_at' => now()->subDays(2)])->save();
        $latestPants->forceFill(['created_at' => now()])->save();

        $response = $this
            ->actingAs($user)
            ->delete(route('auction-items.duplicates.destroy'), [
                'delete_mode' => 'all_latest',
            ]);

        $response
            ->assertSessionHas('success')
            ->assertRedirect(route('auction-items.duplicates'));

        $this->assertNull($oldShirt->fresh());
        $this->assertNotNull($latestShirt->fresh());
        $this->assertNull($oldPants->fresh());
        $this->assertNotNull($latestPants->fresh());
        $this->assertNotNull($otherPlatformItem->fresh());
        $this->assertNotNull($otherUserItem->fresh());
    }

    public function test_user_cannot_delete_non_duplicate_item_from_duplicate_delete_endpoint(): void
    {
        $user = $this->premiumUser();
        $item = $this->createAuctionItem($user, 'A-1', 'XLARGE Tシャツ', AuctionItem::PLATFORM_YAHOO);

        $response = $this
            ->actingAs($user)
            ->delete(route('auction-items.duplicates.destroy'), [
                'keep_item_id' => $item->id,
            ]);

        $response
            ->assertSessionHas('error')
            ->assertRedirect(route('auction-items.duplicates'));

        $this->assertNotNull($item->fresh());
    }

    private function createAuctionItem(User $user, string $managementId, string $title, string $platform): AuctionItem
    {
        return AuctionItem::create([
            'user_id' => $user->id,
            'management_id' => $managementId,
            'title' => $title,
            'comment' => null,
            'platform' => $platform,
            'image_path' => null,
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
    }

    private function premiumUser(): User
    {
        return User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
        ]);
    }
}
