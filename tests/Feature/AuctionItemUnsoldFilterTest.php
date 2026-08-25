<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionItemUnsoldFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_filter_unsold_items_for_ten_days_or_more_ordered_by_oldest_first(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $oldestItem = $this->createAuctionItem($user, 'A-1', '30日売れていない商品');
        $olderItem = $this->createAuctionItem($user, 'A-2', '11日売れていない商品');
        $recentItem = $this->createAuctionItem($user, 'A-3', '9日目の商品');
        $soldItem = $this->createAuctionItem($user, 'A-4', '売却済みの商品', AuctionItem::STATUS_SOLD);
        $otherUserItem = $this->createAuctionItem($otherUser, 'A-5', '他ユーザーの商品');

        $oldestItem->forceFill(['created_at' => now()->subDays(30)])->save();
        $olderItem->forceFill(['created_at' => now()->subDays(11)])->save();
        $recentItem->forceFill(['created_at' => now()->subDays(9)])->save();
        $soldItem->forceFill(['created_at' => now()->subDays(30), 'sold_at' => now()])->save();
        $otherUserItem->forceFill(['created_at' => now()->subDays(30)])->save();

        $response = $this
            ->actingAs($user)
            ->get(route('auction-items.index', ['unsold' => '1']));

        $response->assertOk();
        $response->assertSee('10日以上未売却', false);
        $response->assertSeeInOrder([
            '30日売れていない商品',
            '11日売れていない商品',
        ]);
        $response->assertDontSee('9日目の商品', false);
        $response->assertDontSee('売却済みの商品', false);
        $response->assertDontSee('他ユーザーの商品', false);
    }

    public function test_user_can_filter_selling_items_registered_on_or_before_selected_unsold_date(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $baseDate = now()->setDate(2026, 7, 23)->startOfDay();

        $beforeItem = $this->createAuctionItem($user, 'B-1', '基準日前の商品');
        $sameDayItem = $this->createAuctionItem($user, 'B-2', '基準日当日の商品');
        $afterItem = $this->createAuctionItem($user, 'B-3', '基準日後の商品');
        $soldItem = $this->createAuctionItem($user, 'B-4', '基準日前の売却済み商品', AuctionItem::STATUS_SOLD);
        $otherUserItem = $this->createAuctionItem($otherUser, 'B-5', '他ユーザーの基準日前商品');

        $beforeItem->forceFill(['created_at' => $baseDate->copy()->subDay()])->save();
        $sameDayItem->forceFill(['created_at' => $baseDate->copy()->setTime(23, 59, 59)])->save();
        $afterItem->forceFill(['created_at' => $baseDate->copy()->addDay()])->save();
        $soldItem->forceFill(['created_at' => $baseDate->copy()->subDay(), 'sold_at' => now()])->save();
        $otherUserItem->forceFill(['created_at' => $baseDate->copy()->subDay()])->save();

        $response = $this
            ->actingAs($user)
            ->get(route('auction-items.index', ['unsold_before' => '20260723']));

        $response->assertOk();
        $response->assertSee('2026/07/23以前の未売却', false);
        $response->assertSeeInOrder([
            '基準日前の商品',
            '基準日当日の商品',
        ]);
        $response->assertDontSee('基準日後の商品', false);
        $response->assertDontSee('基準日前の売却済み商品', false);
        $response->assertDontSee('他ユーザーの基準日前商品', false);
    }

    private function createAuctionItem(
        User $user,
        string $managementId,
        string $title,
        string $status = AuctionItem::STATUS_SELLING
    ): AuctionItem {
        return AuctionItem::query()->create([
            'user_id' => $user->id,
            'management_id' => $managementId,
            'title' => $title,
            'comment' => null,
            'platform' => AuctionItem::PLATFORM_OTHER,
            'image_path' => null,
            'sold_image_path' => null,
            'status' => $status,
            'purchase_price' => 100,
            'sold_price' => 200,
            'sales_fee_rate' => 10,
            'sales_fee' => 20,
            'shipping_fee' => 30,
            'profit' => $status === AuctionItem::STATUS_SOLD ? 50 : 0,
            'sold_at' => $status === AuctionItem::STATUS_SOLD ? now() : null,
        ]);
    }
}
