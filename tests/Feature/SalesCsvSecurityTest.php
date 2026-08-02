<?php

namespace Tests\Feature;

use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesCsvSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_downloaded_sales_csv_escapes_formula_like_cells(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
        ]);

        AuctionItem::create([
            'user_id' => $user->id,
            'management_id' => '=IMPORTXML("https://example.com","//title")',
            'title' => '+SUM(1,1)',
            'comment' => null,
            'platform' => '@malicious',
            'status' => 'sold',
            'purchase_price' => 100,
            'sold_price' => 200,
            'sales_fee_rate' => 10,
            'sales_fee' => 20,
            'shipping_fee' => 30,
            'profit' => 50,
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('sales.csv'));

        $response->assertOk();

        $csv = $response->content();

        $this->assertStringContainsString('"\'=IMPORTXML(""https://example.com"",""//title"")"', $csv);
        $this->assertStringContainsString("\"'+SUM(1,1)\"", $csv);
        $this->assertStringContainsString('"\'@malicious"', $csv);
    }
}
