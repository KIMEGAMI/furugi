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
}
