<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PremiumReportController extends Controller
{
    private const STALE_DAYS = 30;

    private const TARGET_PROFIT_RATE = 0.30;

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! ($user?->isPremium() ?? false)) {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '運用診断レポートはPremium限定機能です。');
        }

        $userId = (int) Auth::id();
        $sellingItems = AuctionItem::with(['category.parent'])
            ->where('user_id', $userId)
            ->where('status', AuctionItem::STATUS_SELLING)
            ->latest('created_at')
            ->get();
        $soldItems = AuctionItem::with(['category.parent'])
            ->where('user_id', $userId)
            ->where('status', AuctionItem::STATUS_SOLD)
            ->latest('sold_at')
            ->latest('updated_at')
            ->get();
        $recentSoldItems = $soldItems->filter(
            fn (AuctionItem $item): bool => $this->soldOrUpdatedAt($item)->greaterThanOrEqualTo(now()->subDays(90))
        );

        return view('premium.report', [
            'inventorySummary' => $this->inventorySummary($sellingItems),
            'turnoverSummary' => $this->turnoverSummary($sellingItems, $soldItems),
            'agingBuckets' => $this->agingBuckets($sellingItems),
            'staleItems' => $this->staleItems($sellingItems),
            'markdownCandidates' => $this->markdownCandidates($sellingItems),
            'purchaseCeilingSuggestions' => $this->purchaseCeilingSuggestions($recentSoldItems),
            'lowProfitItems' => $this->lowProfitItems($recentSoldItems),
            'platformPerformance' => $this->platformPerformance($recentSoldItems),
            'nextActions' => $this->nextActions($sellingItems, $recentSoldItems),
            'monthlyActionPlan' => $this->monthlyActionPlan($sellingItems, $recentSoldItems),
        ]);
    }

    private function inventorySummary(Collection $sellingItems): array
    {
        $inventoryCost = $sellingItems->sum(fn (AuctionItem $item): int => (int) ($item->purchase_price ?? 0));
        $expectedSales = $sellingItems->sum(fn (AuctionItem $item): int => (int) ($item->sold_price ?? 0));
        $staleItems = $sellingItems->filter(fn (AuctionItem $item): bool => $this->daysListed($item) >= self::STALE_DAYS);
        $staleCost = $staleItems->sum(fn (AuctionItem $item): int => (int) ($item->purchase_price ?? 0));

        return [
            'selling_count' => $sellingItems->count(),
            'inventory_cost' => $inventoryCost,
            'expected_sales' => $expectedSales,
            'stale_count' => $staleItems->count(),
            'stale_cost' => $staleCost,
            'stale_cost_rate' => $inventoryCost > 0 ? round(($staleCost / $inventoryCost) * 100, 1) : 0.0,
            'average_days_listed' => $sellingItems->count() > 0
                ? round($sellingItems->avg(fn (AuctionItem $item): int => $this->daysListed($item)), 1)
                : 0.0,
        ];
    }

    private function turnoverSummary(Collection $sellingItems, Collection $soldItems): array
    {
        $sold30 = $soldItems->filter(
            fn (AuctionItem $item): bool => $this->soldOrUpdatedAt($item)->greaterThanOrEqualTo(now()->subDays(30))
        )->count();
        $sold90 = $soldItems->filter(
            fn (AuctionItem $item): bool => $this->soldOrUpdatedAt($item)->greaterThanOrEqualTo(now()->subDays(90))
        )->count();
        $currentInventory = $sellingItems->count();
        $sellThrough30 = ($sold30 + $currentInventory) > 0
            ? round(($sold30 / ($sold30 + $currentInventory)) * 100, 1)
            : 0.0;
        $monthlyTurnover = $currentInventory > 0
            ? round($sold30 / $currentInventory, 2)
            : (float) $sold30;
        $averageDaysToSell = $soldItems->count() > 0
            ? round($soldItems->avg(fn (AuctionItem $item): int => $this->daysToSell($item)), 1)
            : 0.0;

        return [
            'sold_30' => $sold30,
            'sold_90' => $sold90,
            'sell_through_30' => $sellThrough30,
            'monthly_turnover' => $monthlyTurnover,
            'average_days_to_sell' => $averageDaysToSell,
        ];
    }

    private function agingBuckets(Collection $sellingItems): Collection
    {
        $buckets = collect([
            '0-29日' => ['min' => 0, 'max' => 29],
            '30-59日' => ['min' => 30, 'max' => 59],
            '60-89日' => ['min' => 60, 'max' => 89],
            '90日以上' => ['min' => 90, 'max' => null],
        ]);

        return $buckets->map(function (array $range, string $label) use ($sellingItems): array {
            $items = $sellingItems->filter(function (AuctionItem $item) use ($range): bool {
                $days = $this->daysListed($item);

                if ($range['max'] === null) {
                    return $days >= $range['min'];
                }

                return $days >= $range['min'] && $days <= $range['max'];
            });

            return [
                'label' => $label,
                'count' => $items->count(),
                'cost' => $items->sum(fn (AuctionItem $item): int => (int) ($item->purchase_price ?? 0)),
            ];
        })->values();
    }

    private function staleItems(Collection $sellingItems): Collection
    {
        return $sellingItems
            ->map(fn (AuctionItem $item): array => [
                'item' => $item,
                'days' => $this->daysListed($item),
                'cost' => (int) ($item->purchase_price ?? 0),
                'price' => (int) ($item->sold_price ?? 0),
            ])
            ->sortByDesc('days')
            ->take(10)
            ->values();
    }

    private function markdownCandidates(Collection $sellingItems): Collection
    {
        return $sellingItems
            ->filter(fn (AuctionItem $item): bool => $this->daysListed($item) >= self::STALE_DAYS && (int) ($item->sold_price ?? 0) > 0)
            ->sortByDesc(fn (AuctionItem $item): int => $this->daysListed($item))
            ->take(8)
            ->map(function (AuctionItem $item): array {
                $days = $this->daysListed($item);
                $currentPrice = (int) ($item->sold_price ?? 0);
                $discountRate = $this->markdownRate($days);
                $suggestedPrice = $this->roundPrice((int) floor($currentPrice * (1 - $discountRate)));
                $feeRate = (float) ($item->sales_fee_rate ?: $this->defaultSalesFeeRate((string) $item->platform));
                $expectedFee = (int) round($suggestedPrice * ($feeRate / 100));
                $expectedProfit = $suggestedPrice - (int) ($item->purchase_price ?? 0) - $expectedFee - (int) ($item->shipping_fee ?? 0);

                return [
                    'item' => $item,
                    'days' => $days,
                    'current_price' => $currentPrice,
                    'suggested_price' => max(0, $suggestedPrice),
                    'discount_rate' => (int) round($discountRate * 100),
                    'expected_profit' => $expectedProfit,
                ];
            })
            ->values();
    }

    private function purchaseCeilingSuggestions(Collection $soldItems): Collection
    {
        return $soldItems
            ->filter(fn (AuctionItem $item): bool => (int) ($item->sold_price ?? 0) > 0)
            ->groupBy(fn (AuctionItem $item): string => $this->groupLabel($item))
            ->map(function (Collection $items, string $label): array {
                $averageSoldPrice = (int) round($items->avg(fn (AuctionItem $item): int => (int) ($item->sold_price ?? 0)));
                $averageShipping = (int) round($items->avg(fn (AuctionItem $item): int => (int) ($item->shipping_fee ?? 0)));
                $averageFeeRate = (float) round($items->avg(fn (AuctionItem $item): float => (float) ($item->sales_fee_rate ?: $this->defaultSalesFeeRate((string) $item->platform))), 1);
                $averageFee = (int) round($averageSoldPrice * ($averageFeeRate / 100));
                $targetProfit = (int) round($averageSoldPrice * self::TARGET_PROFIT_RATE);
                $maxPurchasePrice = max(0, $this->roundPrice($averageSoldPrice - $averageFee - $averageShipping - $targetProfit));

                return [
                    'label' => $label,
                    'count' => $items->count(),
                    'average_sold_price' => $averageSoldPrice,
                    'average_shipping' => $averageShipping,
                    'average_fee_rate' => $averageFeeRate,
                    'target_profit' => $targetProfit,
                    'max_purchase_price' => $maxPurchasePrice,
                ];
            })
            ->filter(fn (array $row): bool => $row['count'] >= 2 && $row['max_purchase_price'] > 0)
            ->sortByDesc('count')
            ->take(8)
            ->values();
    }

    private function lowProfitItems(Collection $soldItems): Collection
    {
        return $soldItems
            ->filter(fn (AuctionItem $item): bool => (int) ($item->sold_price ?? 0) > 0)
            ->map(function (AuctionItem $item): array {
                $soldPrice = (int) ($item->sold_price ?? 0);
                $profit = $this->calculateProfit($item);

                return [
                    'item' => $item,
                    'profit' => $profit,
                    'profit_rate' => $soldPrice > 0 ? round(($profit / $soldPrice) * 100, 1) : 0,
                ];
            })
            ->filter(fn (array $row): bool => $row['profit_rate'] < 20 || $row['profit'] < 1000)
            ->sortBy('profit_rate')
            ->take(8)
            ->values();
    }

    private function platformPerformance(Collection $soldItems): Collection
    {
        return $soldItems
            ->groupBy(fn (AuctionItem $item): string => (string) ($item->platform ?: AuctionItem::PLATFORM_OTHER))
            ->map(function (Collection $items, string $platform): array {
                $sales = $items->sum(fn (AuctionItem $item): int => (int) ($item->sold_price ?? 0));
                $profit = $items->sum(fn (AuctionItem $item): int => $this->calculateProfit($item));

                return [
                    'platform' => $platform,
                    'count' => $items->count(),
                    'sales' => $sales,
                    'profit' => $profit,
                    'profit_rate' => $sales > 0 ? round(($profit / $sales) * 100, 1) : 0,
                    'average_profit' => $items->count() > 0 ? (int) round($profit / $items->count()) : 0,
                ];
            })
            ->sortByDesc('profit')
            ->values();
    }

    private function nextActions(Collection $sellingItems, Collection $soldItems): Collection
    {
        $actions = collect();
        $staleCount = $sellingItems->filter(fn (AuctionItem $item): bool => $this->daysListed($item) >= self::STALE_DAYS)->count();
        $lowProfitCount = $this->lowProfitItems($soldItems)->count();
        $noPriceCount = $sellingItems->filter(fn (AuctionItem $item): bool => (int) ($item->sold_price ?? 0) <= 0)->count();

        if ($staleCount > 0) {
            $actions->push('30日以上動いていない商品があります。写真、タイトル、価格を見直し、必要なら値下げ候補から順に処理してください。');
        }

        if ($lowProfitCount > 0) {
            $actions->push('低利益の商品があります。仕入れ上限提案を基準に、次回の仕入れ価格を抑えてください。');
        }

        if ($noPriceCount > 0) {
            $actions->push('販売価格が未設定の商品があります。分析精度が落ちるため、先に価格を埋めてください。');
        }

        if ($actions->isEmpty()) {
            $actions->push('在庫回転と利益率は安定しています。利益率の高いジャンルや出品先へ仕入れを寄せてください。');
        }

        return $actions;
    }

    private function monthlyActionPlan(Collection $sellingItems, Collection $soldItems): Collection
    {
        $markdownCount = $this->markdownCandidates($sellingItems)->count();
        $lowProfitCount = $this->lowProfitItems($soldItems)->count();
        $noPriceCount = $sellingItems->filter(fn (AuctionItem $item): bool => (int) ($item->sold_price ?? 0) <= 0)->count();
        $staleCount = $sellingItems->filter(fn (AuctionItem $item): bool => $this->daysListed($item) >= self::STALE_DAYS)->count();
        $sold30 = $soldItems->filter(
            fn (AuctionItem $item): bool => $this->soldOrUpdatedAt($item)->greaterThanOrEqualTo(now()->subDays(30))
        )->count();

        $actions = collect();

        if ($noPriceCount > 0) {
            $actions->push([
                'priority' => '最優先',
                'title' => '販売価格未設定の商品を埋める',
                'metric' => $noPriceCount.'件',
                'reason' => '価格が未設定だと利益、在庫回転、仕入れ上限の精度が落ちます。',
                'todo' => '商品一覧で価格未設定の商品を開き、販売価格と送料を入力してください。',
                'href' => route('auction-items.index', ['status' => AuctionItem::STATUS_SELLING]),
                'cta' => '商品一覧で確認',
            ]);
        }

        if ($markdownCount > 0) {
            $actions->push([
                'priority' => '今週中',
                'title' => '値下げ候補を上から処理する',
                'metric' => $markdownCount.'件',
                'reason' => '30日以上動いていない在庫は、写真、タイトル、価格の見直しで回転が改善しやすいです。',
                'todo' => '値下げ候補の提案価格を見て、利益が残る商品から価格を調整してください。',
                'href' => route('premium.report').'#markdown-candidates',
                'cta' => '値下げ候補を見る',
            ]);
        }

        if ($lowProfitCount > 0) {
            $actions->push([
                'priority' => '仕入れ前',
                'title' => '低利益商品の仕入れ上限を見直す',
                'metric' => $lowProfitCount.'件',
                'reason' => '売れていても利益が薄い商品は、次回の仕入れ価格を下げないと手元に残りません。',
                'todo' => '仕入れ上限の自動提案を基準に、次回から上限額を超えないようにしてください。',
                'href' => route('premium.report').'#purchase-ceiling',
                'cta' => '仕入れ上限を見る',
            ]);
        }

        if ($staleCount > 0) {
            $actions->push([
                'priority' => '月内',
                'title' => '売れ残り在庫を再撮影・再出品する',
                'metric' => $staleCount.'件',
                'reason' => '滞留在庫は資金を止めます。古い順に処理すると在庫原価率を下げやすくなります。',
                'todo' => '売れ残り上位から、写真、タイトル、説明文、出品先を見直してください。',
                'href' => route('premium.report').'#stale-items',
                'cta' => '売れ残りを見る',
            ]);
        }

        if ($sold30 <= 0) {
            $actions->push([
                'priority' => '今日',
                'title' => 'SOLD登録漏れを確認する',
                'metric' => '30日販売 0件',
                'reason' => '販売済みの商品がSOLD化されていないと、月別利益と在庫回転が正しく見えません。',
                'todo' => '販売済みの商品を商品一覧からSOLDに変更してください。',
                'href' => route('auction-items.index', ['status' => AuctionItem::STATUS_SELLING]),
                'cta' => 'SOLD登録へ',
            ]);
        }

        if ($actions->isEmpty()) {
            $actions->push([
                'priority' => '継続',
                'title' => '利益率の高い型を増やす',
                'metric' => '良好',
                'reason' => '大きな詰まりはありません。売れて利益が残るジャンルに仕入れを寄せる段階です。',
                'todo' => '出品先別パフォーマンスと仕入れ上限を見て、利益率の高いカテゴリを増やしてください。',
                'href' => route('premium.report').'#platform-performance',
                'cta' => '実績を見る',
            ]);
        }

        return $actions->take(4)->values();
    }

    private function daysListed(AuctionItem $item): int
    {
        return $item->created_at ? max(0, (int) $item->created_at->diffInDays(now())) : 0;
    }

    private function daysToSell(AuctionItem $item): int
    {
        if (! $item->created_at) {
            return 0;
        }

        return max(0, (int) $item->created_at->diffInDays($this->soldOrUpdatedAt($item)));
    }

    private function soldOrUpdatedAt(AuctionItem $item)
    {
        return $item->sold_at ?: $item->updated_at ?: now();
    }

    private function calculateProfit(AuctionItem $item): int
    {
        $soldPrice = (int) ($item->sold_price ?? 0);
        $purchasePrice = (int) ($item->purchase_price ?? 0);
        $salesFee = (int) ($item->sales_fee ?? 0);

        if ($salesFee <= 0) {
            $salesFee = (int) round($soldPrice * ((float) ($item->sales_fee_rate ?? 0) / 100));
        }

        return $soldPrice - $purchasePrice - $salesFee - (int) ($item->shipping_fee ?? 0);
    }

    private function groupLabel(AuctionItem $item): string
    {
        $category = $item->category?->name ?: '未分類';
        $platform = $item->platform ?: '未設定';

        return $platform.' / '.$category;
    }

    private function markdownRate(int $days): float
    {
        if ($days >= 90) {
            return 0.2;
        }

        if ($days >= 60) {
            return 0.15;
        }

        return 0.1;
    }

    private function roundPrice(int $price): int
    {
        if ($price >= 1000) {
            return (int) floor($price / 100) * 100;
        }

        return (int) floor($price / 10) * 10;
    }

    private function defaultSalesFeeRate(string $platform): float
    {
        return AuctionItem::SALES_FEE_RATES[$platform] ?? AuctionItem::SALES_FEE_RATES[AuctionItem::PLATFORM_OTHER];
    }
}
