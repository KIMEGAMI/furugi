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
            ->whereNotNull('sold_at')
            ->where('sold_at', '>=', now()->subDays(90)->toDateString())
            ->latest('sold_at')
            ->get();

        return view('premium.report', [
            'inventorySummary' => $this->inventorySummary($sellingItems),
            'agingBuckets' => $this->agingBuckets($sellingItems),
            'markdownCandidates' => $this->markdownCandidates($sellingItems),
            'lowProfitItems' => $this->lowProfitItems($soldItems),
            'platformPerformance' => $this->platformPerformance($soldItems),
            'nextActions' => $this->nextActions($sellingItems, $soldItems),
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
            'stale_cost_rate' => $inventoryCost > 0 ? round(($staleCost / $inventoryCost) * 100, 1) : 0,
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

    private function lowProfitItems(Collection $soldItems): Collection
    {
        return $soldItems
            ->filter(fn (AuctionItem $item): bool => (int) ($item->sold_price ?? 0) > 0)
            ->map(function (AuctionItem $item): array {
                $soldPrice = (int) ($item->sold_price ?? 0);
                $profit = (int) ($item->profit ?? 0);

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
                $profit = $items->sum(fn (AuctionItem $item): int => (int) ($item->profit ?? 0));

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
            $actions->push('30日以上動いていない商品を値下げ・写真差し替え・タイトル見直しの順で処理してください。');
        }

        if ($lowProfitCount > 0) {
            $actions->push('利益率20%未満または利益1,000円未満の商品は、仕入れ上限と送料設定を見直してください。');
        }

        if ($noPriceCount > 0) {
            $actions->push('販売価格が未設定の商品があります。価格未設定は分析精度を落とすため、先に埋めてください。');
        }

        if ($actions->isEmpty()) {
            $actions->push('在庫回転と利益率は良好です。直近で利益率が高い出品先・ジャンルへ仕入れを寄せてください。');
        }

        return $actions;
    }

    private function daysListed(AuctionItem $item): int
    {
        return $item->created_at ? max(0, (int) $item->created_at->diffInDays(now())) : 0;
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
