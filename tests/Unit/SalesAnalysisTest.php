<?php

namespace Tests\Unit;

use App\Models\AuctionItem;
use App\Models\Category;
use App\Services\CategorySalesAnalysisService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SalesAnalysisTest extends TestCase
{
    public function test_cross_analysis_handles_unset_category_and_platform(): void
    {
        $parent = new Category(['name' => 'トップス']);
        $child = new Category(['name' => 'Tシャツ']);
        $child->setRelation('parent', $parent);

        $categorizedItem = new AuctionItem([
            'platform' => 'メルカリ',
            'sold_price' => 1000,
            'purchase_price' => 300,
            'sales_fee' => 100,
            'shipping_fee' => 200,
        ]);
        $categorizedItem->setRelation('category', $child);

        $unsetItem = new AuctionItem([
            'platform' => null,
            'sold_price' => 0,
            'purchase_price' => 0,
            'sales_fee' => 0,
            'shipping_fee' => 0,
        ]);
        $unsetItem->setRelation('category', null);

        $rows = $this->invokePrivateMethod(
            'buildCategoryPlatformCrossSales',
            [
                collect([$categorizedItem, $unsetItem]),
                collect(['メルカリ', '未設定']),
            ]
        );

        $this->assertCount(2, $rows);
        $this->assertSame(
            1,
            $rows->firstWhere('category', '未設定')['platforms']->get('未設定')['count']
        );
    }

    public function test_ranking_share_is_zero_when_total_sales_is_zero(): void
    {
        $rows = collect([
            [
                'label' => '未設定',
                'count' => 1,
                'sales' => 0,
                'purchase' => 0,
                'sales_fee' => 0,
                'shipping_fee' => 0,
                'profit' => 0,
            ],
        ]);

        $ranking = $this->invokePrivateMethod('addRankingAndShare', [$rows, 0]);

        $this->assertSame(1, $ranking->first()['rank']);
        $this->assertSame(0.0, $ranking->first()['share']);
    }

    private function invokePrivateMethod(string $method, array $arguments): Collection
    {
        $service = new CategorySalesAnalysisService;
        $reflection = new ReflectionMethod($service, $method);

        return $reflection->invokeArgs($service, $arguments);
    }
}
