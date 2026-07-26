<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MercariShopsCsvImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_user_can_import_mercari_shops_sales_csv_as_sold_items(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::PLAN_PREMIUM,
            'subscription_status' => 'active',
        ]);

        $csv = implode("\n", [
            '注文番号,明細番号,明細種別,購入日,商品名,数量,通貨,販売利益,売上（税込）,メルカリ便送料（税込）,送料（税込）,販売手数料（税込）,販売手数料率（%）,ショップ名',
            '"order_123","detail_123","購入","2026/7/18 20:21","XLARGE Tシャツ","1","JPY","690","1000","210","0","100","10","テストショップ"',
            '"order_124","detail_124","キャンセル","2026/7/18 20:21","キャンセル商品","1","JPY","-690","-1000","0","0","0","10","テストショップ"',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('auction-items.import.mercari-shops'), [
                'mercari_shops_csv_file' => UploadedFile::fake()->createWithContent('2026-07_report.csv', $csv),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('auction-items.index', ['status' => AuctionItem::STATUS_SOLD]));

        $this->assertDatabaseHas('auction_items', [
            'user_id' => $user->id,
            'management_id' => 'detail_123',
            'platform' => AuctionItem::PLATFORM_MERCARI,
            'status' => AuctionItem::STATUS_SOLD,
            'sold_price' => 1000,
            'sales_fee' => 100,
            'shipping_fee' => 210,
            'profit' => 690,
        ]);

        $this->assertDatabaseMissing('auction_items', [
            'management_id' => 'detail_124',
        ]);
    }
}
