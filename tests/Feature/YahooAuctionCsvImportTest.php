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

    public function test_premium_user_can_import_csv_items(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
        ]);

        $csv = implode("\n", [
            'management_id,title,platform,purchase_price,sold_price,shipping_fee,status',
            'CSV-001,CSV商品,メルカリ,1000,2500,210,sold',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('auction-items.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('items.csv', $csv),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('auction-items.index'));

        $this->assertDatabaseHas('auction_items', [
            'user_id' => $user->id,
            'management_id' => 'CSV-001',
            'title' => 'CSV商品',
            'status' => AuctionItem::STATUS_SOLD,
        ]);
    }

    public function test_premium_user_can_import_mercari_shops_csv_items(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
        ]);

        $csv = implode("\n", [
            '注文番号,明細番号,明細種別,購入日,支払日,発送日,売上移転日,キャンセル日,商品名,数量,通貨,販売利益,売上（税込）,メルカリ便送料（税込）,送料（税込）,販売手数料（税込）,販売手数料率（%）,クーポン割引金額,クーポンID,ショップ名,インボイス対象金額合計（販売手数料 + メルカリ便送料）,インボイス対象税率,伝票番号,請求書発行事業者',
            'ORDER-001,DETAIL-001,売上,2026-08-01,2026-08-01,2026-08-02,,,リーバイス デニム,1,JPY,2250,3000,750,0,300,10,,,FURUGI SHOP,1050,10%,TRACK-001,',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('auction-items.import.mercari-shops'), [
                'mercari_shops_csv_file' => UploadedFile::fake()->createWithContent('mercari-shops.csv', $csv),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('auction-items.index', ['status' => AuctionItem::STATUS_SOLD]));

        $this->assertDatabaseHas('auction_items', [
            'user_id' => $user->id,
            'management_id' => 'ORDER-001-DETAIL-001',
            'title' => 'リーバイス デニム',
            'platform' => AuctionItem::PLATFORM_MERCARI,
            'status' => AuctionItem::STATUS_SOLD,
            'sold_price' => 3000,
            'sales_fee' => 300,
            'shipping_fee' => 750,
            'profit' => 1950,
        ]);
    }

    public function test_premium_user_can_import_backup_csv_with_japanese_headers(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
        ]);

        $csv = implode("\n", [
            '管理ID,商品タイトル,大ジャンル,小ジャンル,出品先,ステータス,仕入れ値,販売価格,販売手数料率,販売手数料,送料,実利益,SOLD日,商品画像URL,SOLD画像URL,コメント,作成日,更新日',
            'BACKUP-001,バックアップ商品,,,メルカリ,SOLD,1000,2500,10%,250,210,1040,2026-08-03,,,CSVバックアップから復元,,',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('auction-items.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('backup.csv', $csv),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('auction-items.index'));

        $this->assertDatabaseHas('auction_items', [
            'user_id' => $user->id,
            'management_id' => 'BACKUP-001',
            'title' => 'バックアップ商品',
            'platform' => AuctionItem::PLATFORM_MERCARI,
            'status' => AuctionItem::STATUS_SOLD,
            'purchase_price' => 1000,
            'sold_price' => 2500,
            'sales_fee_rate' => 10,
            'sales_fee' => 250,
            'shipping_fee' => 210,
            'profit' => 1040,
            'comment' => 'CSVバックアップから復元',
        ]);
    }
}
