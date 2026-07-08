<?php

namespace App\Services;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CategorySalesAnalysisService
{
    public function analyze(int $userId, array $filters = []): array
    {
        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');
        $selectedMonth = $this->parseMonth($filters['month'] ?? null);
        $allSoldItems = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', $userId)
            ->where('status', AuctionItem::STATUS_SOLD)
            ->get();
        $soldItems = $selectedMonth
            ? $allSoldItems->filter(
                fn ($item) => $this->itemDate($item, $hasSoldAt)->format('Y-m') === $selectedMonth->format('Y-m')
            )->values()
            : $allSoldItems;

        $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $totalProfit = $soldItems->sum(fn ($item) => $this->calculateItemProfit($item));
        $parentCategorySales = $this->summarizeSalesGroups($soldItems->groupBy(fn ($item) => $this->parentCategoryLabel($item)));
        $childCategorySales = $this->summarizeSalesGroups($soldItems->groupBy(fn ($item) => $this->categoryLabel($item)));
        $platformNames = collect(AuctionItem::PLATFORMS)
            ->merge($soldItems->map(fn ($item) => $this->platformLabel($item)))
            ->unique()
            ->values();

        return [
            'summary' => [
                'sales' => $totalSales,
                'profit' => $totalProfit,
                'count' => $soldItems->count(),
                'profit_rate' => $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 1) : 0.0,
            ],
            'selectedMonth' => $selectedMonth,
            'parentCategorySales' => $parentCategorySales,
            'childCategorySales' => $childCategorySales,
            'parentCategoryRanking' => $this->addRankingAndShare($parentCategorySales, $totalSales),
            'childCategoryRanking' => $this->addRankingAndShare($childCategorySales, $totalSales),
            'categoryPlatformCrossSales' => $this->buildCategoryPlatformCrossSales($soldItems, $platformNames),
            'platformNames' => $platformNames,
            'chartLabels' => $parentCategorySales->pluck('label')->values(),
            'chartSalesData' => $parentCategorySales->pluck('sales')->values(),
            'chartProfitData' => $parentCategorySales->pluck('profit')->values(),
            'chartCountData' => $parentCategorySales->pluck('count')->values(),
        ];
    }

    private function parseMonth(mixed $month): ?Carbon
    {
        if (! is_string($month) || trim($month) === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            return null;
        }
    }

    private function itemDate(AuctionItem $item, bool $hasSoldAt): Carbon
    {
        return $hasSoldAt && $item->sold_at
            ? Carbon::parse($item->sold_at)
            : Carbon::parse($item->updated_at);
    }

    private function summarizeSalesGroups(Collection $groups): Collection
    {
        return $groups
            ->map(function ($items, $label) {
                return [
                    'label' => $label,
                    'count' => $items->count(),
                    'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
                    'purchase' => $items->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
                    'sales_fee' => $items->sum(fn ($item) => $this->calculateSalesFee($item)),
                    'shipping_fee' => $items->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
                    'profit' => $items->sum(fn ($item) => $this->calculateItemProfit($item)),
                ];
            })
            ->sortByDesc('sales')
            ->values();
    }

    private function addRankingAndShare(Collection $rows, int $totalSales): Collection
    {
        return $rows
            ->sortByDesc('sales')
            ->values()
            ->map(function (array $row, int $index) use ($totalSales) {
                $row['rank'] = $index + 1;
                $row['share'] = $totalSales > 0 ? round(($row['sales'] / $totalSales) * 100, 1) : 0.0;

                return $row;
            });
    }

    private function buildCategoryPlatformCrossSales(Collection $soldItems, Collection $platformNames): Collection
    {
        return $soldItems
            ->groupBy(fn ($item) => $this->parentCategoryLabel($item))
            ->map(function (Collection $categoryItems, string $category) use ($platformNames) {
                $platforms = $platformNames->mapWithKeys(function (string $platform) use ($categoryItems) {
                    $items = $categoryItems->filter(fn ($item) => $this->platformLabel($item) === $platform);

                    return [
                        $platform => [
                            'count' => $items->count(),
                            'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
                            'profit' => $items->sum(fn ($item) => $this->calculateItemProfit($item)),
                        ],
                    ];
                });

                return [
                    'category' => $category,
                    'platforms' => $platforms,
                    'count' => $categoryItems->count(),
                    'sales' => $categoryItems->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
                    'profit' => $categoryItems->sum(fn ($item) => $this->calculateItemProfit($item)),
                ];
            })
            ->sortByDesc('sales')
            ->values();
    }

    private function calculateItemProfit(AuctionItem $item): int
    {
        return (int) ($item->sold_price ?? 0)
            - (int) ($item->purchase_price ?? 0)
            - $this->calculateSalesFee($item)
            - (int) ($item->shipping_fee ?? 0);
    }

    private function calculateSalesFee(AuctionItem $item): int
    {
        $storedSalesFee = (int) ($item->sales_fee ?? 0);

        if ($storedSalesFee > 0) {
            return $storedSalesFee;
        }

        return (int) round((int) ($item->sold_price ?? 0) * ((float) ($item->sales_fee_rate ?? 0) / 100));
    }

    private function categoryLabel(AuctionItem $item): string
    {
        if (! $item->category) {
            return '未設定';
        }

        if (! $item->category->parent) {
            return $item->category->name;
        }

        return $item->category->parent->name.' / '.$item->category->name;
    }

    private function parentCategoryLabel(AuctionItem $item): string
    {
        return $item->category?->parent?->name
            ?? $item->category?->name
            ?? '未設定';
    }

    private function platformLabel(AuctionItem $item): string
    {
        $platform = trim((string) $item->platform);

        return $platform !== '' ? $platform : '未設定';
    }
}
