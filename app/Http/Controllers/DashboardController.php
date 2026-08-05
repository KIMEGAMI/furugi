<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const STALE_INVENTORY_DAYS = 30;

    private const MIN_MONTHLY_SALES_TARGET = 50000;

    private const SALES_TARGET_GROWTH_RATE = 1.15;

    private const SALES_TARGET_ROUND_UNIT = 10000;

    private const LOW_PROFIT_MARGIN_PERCENT = 25;

    public function index(): View
    {
        $userId = (int) Auth::id();
        $user = Auth::user();
        $now = now();
        $year = (int) $now->format('Y');

        $sellingCount = AuctionItem::where('user_id', $userId)->where('status', AuctionItem::STATUS_SELLING)->count();
        $soldCount = AuctionItem::where('user_id', $userId)->where('status', AuctionItem::STATUS_SOLD)->count();
        $draftCount = AuctionItem::where('user_id', $userId)->where('status', AuctionItem::STATUS_DRAFT)->count();
        $soldItems = AuctionItem::where('user_id', $userId)->where('status', AuctionItem::STATUS_SOLD)->get();
        $sellingItems = AuctionItem::where('user_id', $userId)->where('status', AuctionItem::STATUS_SELLING)->get();

        $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $totalSalesFee = $soldItems->sum(fn ($item) => (int) ($item->sales_fee ?? 0));
        $totalShippingFee = $soldItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0));
        $totalProfit = $soldItems->sum(fn ($item) => (int) ($item->profit ?? 0));
        $profitMargin = $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 1) : 0.0;
        $monthlyStats = $this->monthlyStats($userId, $year);
        $currentMonthStats = $monthlyStats->firstWhere('label', ((int) $now->format('n')).'月') ?? ['sales' => 0, 'profit' => 0, 'count' => 0];
        $previousMonthStats = $monthlyStats->firstWhere('label', ((int) $now->copy()->subMonth()->format('n')).'月') ?? ['sales' => 0, 'profit' => 0, 'count' => 0];
        $monthlySalesAverage = (int) round($monthlyStats->where('sales', '>', 0)->avg('sales') ?? 0);
        $monthlySalesTarget = max(
            self::MIN_MONTHLY_SALES_TARGET,
            (int) ceil(max($monthlySalesAverage, (int) $currentMonthStats['sales']) * self::SALES_TARGET_GROWTH_RATE / self::SALES_TARGET_ROUND_UNIT) * self::SALES_TARGET_ROUND_UNIT
        );
        $monthlyTargetProgress = $monthlySalesTarget > 0 ? min(100, round(((int) $currentMonthStats['sales'] / $monthlySalesTarget) * 100, 1)) : 0;
        $salesTrendPercent = (int) $previousMonthStats['sales'] > 0
            ? round((((int) $currentMonthStats['sales'] - (int) $previousMonthStats['sales']) / (int) $previousMonthStats['sales']) * 100, 1)
            : null;
        $profitTrendPercent = (int) $previousMonthStats['profit'] > 0
            ? round((((int) $currentMonthStats['profit'] - (int) $previousMonthStats['profit']) / (int) $previousMonthStats['profit']) * 100, 1)
            : null;
        $currentMonthProfitMargin = (int) $currentMonthStats['sales'] > 0
            ? round(((int) $currentMonthStats['profit'] / (int) $currentMonthStats['sales']) * 100, 1)
            : 0.0;
        $inventoryCost = $sellingItems->sum(fn ($item) => (int) ($item->purchase_price ?? 0));
        $inventoryPotentialSales = $sellingItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $inventoryPotentialProfit = $sellingItems->sum(fn ($item) => $this->estimateItemProfit($item));
        $staleItems = $sellingItems->filter(fn ($item) => $item->created_at && $item->created_at->lte($now->copy()->subDays(self::STALE_INVENTORY_DAYS)));
        $staleInventoryCost = $staleItems->sum(fn ($item) => (int) ($item->purchase_price ?? 0));
        $averageProfit = $soldCount > 0 ? (int) round($totalProfit / $soldCount) : 0;
        $averageDaysToSell = (int) round($soldItems
            ->filter(fn ($item) => $item->created_at && $item->sold_at)
            ->avg(fn ($item) => $item->created_at->diffInDays($item->sold_at)) ?? 0);

        return view('dashboard', [
            'year' => $year,
            'sellingCount' => $sellingCount,
            'soldCount' => $soldCount,
            'draftCount' => $draftCount,
            'totalSales' => $totalSales,
            'totalSalesFee' => $totalSalesFee,
            'totalShippingFee' => $totalShippingFee,
            'totalProfit' => $totalProfit,
            'monthlyStats' => $monthlyStats,
            'maxMonthlySales' => max(1, (int) $monthlyStats->max('sales')),
            'maxMonthlyProfit' => max(1, (int) $monthlyStats->max('profit')),
            'platformStats' => $this->platformStats($userId),
            'hasActiveSubscription' => $user?->hasActiveSubscription() ?? false,
            'freeItemLimit' => User::FREE_AUCTION_ITEM_LIMIT,
            'recentItems' => AuctionItem::where('user_id', $userId)->latest('updated_at')->take(6)->get(),
            'dashboardNotices' => Notice::query()
                ->published()
                ->latest('published_at')
                ->paginate(Notice::DASHBOARD_LIMIT, ['*'], 'notice_page')
                ->withQueryString(),
            'businessInsights' => [
                'current_month_sales' => (int) $currentMonthStats['sales'],
                'current_month_profit' => (int) $currentMonthStats['profit'],
                'current_month_count' => (int) $currentMonthStats['count'],
                'current_month_profit_margin' => $currentMonthProfitMargin,
                'monthly_sales_target' => $monthlySalesTarget,
                'monthly_target_progress' => $monthlyTargetProgress,
                'sales_trend_percent' => $salesTrendPercent,
                'profit_trend_percent' => $profitTrendPercent,
                'profit_margin' => $profitMargin,
                'average_profit' => $averageProfit,
                'inventory_cost' => $inventoryCost,
                'inventory_potential_sales' => $inventoryPotentialSales,
                'inventory_potential_profit' => $inventoryPotentialProfit,
                'stale_count' => $staleItems->count(),
                'stale_inventory_cost' => $staleInventoryCost,
                'average_days_to_sell' => $averageDaysToSell,
                'actions' => $this->insightActions($staleItems->count(), $profitMargin, (int) $currentMonthStats['sales'], $monthlySalesTarget, $monthlyTargetProgress),
            ],
        ]);
    }

    private function estimateItemProfit(AuctionItem $item): int
    {
        $soldPrice = (int) ($item->sold_price ?? 0);
        $purchasePrice = (int) ($item->purchase_price ?? 0);
        $shippingFee = (int) ($item->shipping_fee ?? 0);
        $salesFee = (int) ($item->sales_fee ?? 0);

        if ($salesFee <= 0 && $soldPrice > 0) {
            $salesFee = (int) round($soldPrice * ((float) ($item->sales_fee_rate ?? 0) / 100));
        }

        return $soldPrice - $purchasePrice - $salesFee - $shippingFee;
    }

    private function monthlyStats(int $userId, int $year)
    {
        $monthlyRows = AuctionItem::query()
            ->where('user_id', $userId)
            ->where('status', AuctionItem::STATUS_SOLD)
            ->whereNotNull('sold_at')
            ->whereYear('sold_at', $year)
            ->get()
            ->groupBy(fn (AuctionItem $item) => (int) $item->sold_at->format('n'))
            ->map(fn ($items) => [
                'sales_total' => $items->sum(fn (AuctionItem $item) => (int) ($item->sold_price ?? 0)),
                'profit_total' => $items->sum(fn (AuctionItem $item) => (int) ($item->profit ?? 0)),
                'sold_count' => $items->count(),
            ]);

        return collect(range(1, 12))->map(function ($month) use ($monthlyRows) {
            $row = $monthlyRows->get($month);

            return [
                'label' => $month.'月',
                'sales' => (int) ($row['sales_total'] ?? 0),
                'profit' => (int) ($row['profit_total'] ?? 0),
                'count' => (int) ($row['sold_count'] ?? 0),
            ];
        });
    }

    private function platformStats(int $userId)
    {
        $platformRows = AuctionItem::selectRaw('platform, COUNT(*) as total')
            ->where('user_id', $userId)
            ->groupBy('platform')
            ->pluck('total', 'platform');
        $platformRows = $platformRows->reduce(function ($rows, $count, $platform) {
            $name = AuctionItem::normalizePlatformName($platform);
            $name = $name !== '' ? $name : '未設定';
            $rows[$name] = ($rows[$name] ?? 0) + (int) $count;

            return $rows;
        }, collect());
        $platformTotal = max(0, (int) $platformRows->sum());

        return $platformRows
            ->map(fn ($count, $platform) => [
                'name' => $platform ?: '未設定',
                'count' => (int) $count,
                'percent' => $platformTotal > 0 ? round(((int) $count / $platformTotal) * 100, 1) : 0,
            ])
            ->sortByDesc('count')
            ->values();
    }

    private function insightActions(int $staleCount, float $profitMargin, int $currentMonthSales, int $monthlySalesTarget, float $monthlyTargetProgress)
    {
        $actions = collect();

        if ($staleCount > 0) {
            $actions->push([
                'tone' => 'amber',
                'title' => '長期在庫を確認してください',
                'body' => self::STALE_INVENTORY_DAYS.'日以上動いていない商品が'.$staleCount.'件あります。価格や説明文の見直し候補です。',
                'href' => route('auction-items.index', ['status' => AuctionItem::STATUS_SELLING]),
                'label' => '在庫を見る',
            ]);
        }

        if ($profitMargin > 0 && $profitMargin < self::LOW_PROFIT_MARGIN_PERCENT) {
            $actions->push([
                'tone' => 'rose',
                'title' => '利益率を確認してください',
                'body' => '現在の利益率は'.$profitMargin.'%です。送料、手数料、仕入れ上限を見直す余地があります。',
                'href' => route('sales.index'),
                'label' => '売上を見る',
            ]);
        }

        if ($currentMonthSales < $monthlySalesTarget) {
            $actions->push([
                'tone' => 'cyan',
                'title' => '目標まであと¥'.number_format($monthlySalesTarget - $currentMonthSales),
                'body' => '今月の売上目標進捗は'.$monthlyTargetProgress.'%です。出品中商品の見直しと追加登録を検討してください。',
                'href' => route('auction-items.create'),
                'label' => '商品を登録',
            ]);
        }

        if ($actions->isEmpty()) {
            $actions->push([
                'tone' => 'emerald',
                'title' => '良い状態を維持できています',
                'body' => '売上、利益率、在庫状況が安定しています。ジャンル別分析で次の仕入れ候補を探してください。',
                'href' => route('category-sales.index'),
                'label' => 'ジャンル分析',
            ]);
        }

        return $actions->take(3)->values();
    }
}
