<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class YahooAuctionCsvImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_user_can_import_yahoo_auction_sales_csv_as_sold_items(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
        ]);

        $csv = implode("\n", [
            '取扱内容,商品ID,取扱日,状態,売上,決済金額,落札システム利用料,販売手数料,送料,受取金額',
            '"売上金チャージ","-","2026年7月18日 20時45分","チャージ完了","-","-","-","-","-","3518"',
            '"＜まとめ買い＞ エクストララージ XLARGE X-LARGE 黒 バックプリント ","z645266786","2026年7月18日 20時21分","売上金","3518","4597","-","229","850","-"',
            '"X-LARGE Tシャツ Lサイズ 古着 エクストララージ 黒","1236212833","2026年7月16日 19時43分","売上金","1140","1499","149","-","210","-"',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('auction-items.import.yahoo-auctions'), [
                'yahoo_csv_file' => UploadedFile::fake()->createWithContent('saleslist.csv', $csv),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('auction-items.index', ['status' => AuctionItem::STATUS_SOLD]));

        $this->assertDatabaseHas('auction_items', [
            'user_id' => $user->id,
            'management_id' => 'z645266786',
            'platform' => AuctionItem::PLATFORM_YAHOO,
            'status' => AuctionItem::STATUS_SOLD,
            'sold_price' => 4597,
            'sales_fee' => 229,
            'shipping_fee' => 850,
            'profit' => 3518,
        ]);

        $this->assertDatabaseHas('auction_items', [
            'user_id' => $user->id,
            'management_id' => '1236212833',
            'sold_price' => 1499,
            'sales_fee' => 149,
            'shipping_fee' => 210,
            'profit' => 1140,
        ]);

        $this->assertDatabaseMissing('auction_items', [
            'management_id' => '-',
        ]);
    }

    public function test_free_user_cannot_import_yahoo_auction_sales_csv(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_FREE,
            'subscription_status' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('auction-items.import.yahoo-auctions'), [
                'yahoo_csv_file' => UploadedFile::fake()->createWithContent(
                    'saleslist.csv',
                    "取扱内容,商品ID,取扱日,状態,売上,決済金額,落札システム利用料,販売手数料,送料,受取金額\n"
                ),
            ]);

        $response->assertRedirect(route('subscriptions.index'));
    }
}
