<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $now = now();
        $year = (int) $now->format('Y');

        $sellingCount = AuctionItem::where('user_id', $userId)->where('status', 'selling')->count();
        $soldCount = AuctionItem::where('user_id', $userId)->where('status', 'sold')->count();
        $draftCount = AuctionItem::where('user_id', $userId)->where('status', 'draft')->count();

        $soldItems = AuctionItem::where('user_id', $userId)->where('status', 'sold')->get();
        $sellingItems = AuctionItem::where('user_id', $userId)->where('status', 'selling')->get();

        $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $totalSalesFee = $soldItems->sum(fn ($item) => (int) ($item->sales_fee ?? 0));
        $totalShippingFee = $soldItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0));
        $totalProfit = $soldItems->sum(fn ($item) => (int) ($item->profit ?? 0));
        $profitMargin = $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 1) : 0;

        $monthlyStats = $this->monthlyStats($userId, $year);
        $maxMonthlySales = max(1, (int) $monthlyStats->max('sales'));
        $maxMonthlyProfit = max(1, (int) $monthlyStats->max('profit'));

        $currentMonthStats = $monthlyStats->firstWhere('label', ((int) $now->format('n')).'月') ?? [
            'sales' => 0,
            'profit' => 0,
            'count' => 0,
        ];
        $previousMonthStats = $monthlyStats->firstWhere('label', ((int) $now->copy()->subMonth()->format('n')).'月') ?? [
            'sales' => 0,
            'profit' => 0,
            'count' => 0,
        ];

        $monthlySalesAverage = (int) round($monthlyStats->where('sales', '>', 0)->avg('sales') ?? 0);
        $monthlySalesTarget = max(
            50000,
            (int) ceil(max($monthlySalesAverage, (int) $currentMonthStats['sales']) * 1.15 / 10000) * 10000
        );
        $monthlyTargetProgress = $monthlySalesTarget > 0
            ? min(100, round(((int) $currentMonthStats['sales'] / $monthlySalesTarget) * 100, 1))
            : 0;
        $salesTrendPercent = (int) $previousMonthStats['sales'] > 0
            ? round((((int) $currentMonthStats['sales'] - (int) $previousMonthStats['sales']) / (int) $previousMonthStats['sales']) * 100, 1)
            : null;
        $profitTrendPercent = (int) $previousMonthStats['profit'] > 0
            ? round((((int) $currentMonthStats['profit'] - (int) $previousMonthStats['profit']) / (int) $previousMonthStats['profit']) * 100, 1)
            : null;

        $inventoryCost = $sellingItems->sum(fn ($item) => (int) ($item->purchase_price ?? 0));
        $staleItems = $sellingItems->filter(fn ($item) => $item->created_at && $item->created_at->lte($now->copy()->subDays(30)));
        $staleInventoryCost = $staleItems->sum(fn ($item) => (int) ($item->purchase_price ?? 0));
        $averageProfit = $soldCount > 0 ? (int) round($totalProfit / $soldCount) : 0;
        $averageDaysToSell = (int) round($soldItems
            ->filter(fn ($item) => $item->created_at && $item->sold_at)
            ->avg(fn ($item) => $item->created_at->diffInDays($item->sold_at)) ?? 0);

        $platformStats = $this->platformStats($userId);
        $recentItems = AuctionItem::where('user_id', $userId)
            ->latest('updated_at')
            ->take(6)
            ->get();

        $premiumInsights = [
            'current_month_sales' => (int) $currentMonthStats['sales'],
            'current_month_profit' => (int) $currentMonthStats['profit'],
            'current_month_count' => (int) $currentMonthStats['count'],
            'monthly_sales_target' => $monthlySalesTarget,
            'monthly_target_progress' => $monthlyTargetProgress,
            'sales_trend_percent' => $salesTrendPercent,
            'profit_trend_percent' => $profitTrendPercent,
            'profit_margin' => $profitMargin,
            'average_profit' => $averageProfit,
            'inventory_cost' => $inventoryCost,
            'stale_count' => $staleItems->count(),
            'stale_inventory_cost' => $staleInventoryCost,
            'average_days_to_sell' => $averageDaysToSell,
            'actions' => $this->insightActions(
                $staleItems->count(),
                $profitMargin,
                (int) $currentMonthStats['sales'],
                $monthlySalesTarget,
                $monthlyTargetProgress
            ),
        ];

        return view('dashboard', compact(
            'year',
            'sellingCount',
            'soldCount',
            'draftCount',
            'totalSales',
            'totalSalesFee',
            'totalShippingFee',
            'totalProfit',
            'monthlyStats',
            'maxMonthlySales',
            'maxMonthlyProfit',
            'platformStats',
            'recentItems',
            'premiumInsights'
        ));
    }

    private function monthlyStats(int $userId, int $year)
    {
        $monthlyRows = AuctionItem::selectRaw('
                MONTH(sold_at) as month_number,
                SUM(sold_price) as sales_total,
                SUM(profit) as profit_total,
                COUNT(*) as sold_count
            ')
            ->where('user_id', $userId)
            ->where('status', 'sold')
            ->whereNotNull('sold_at')
            ->whereYear('sold_at', $year)
            ->groupBy(DB::raw('MONTH(sold_at)'))
            ->get()
            ->keyBy('month_number');

        return collect(range(1, 12))->map(function ($month) use ($monthlyRows) {
            $row = $monthlyRows->get($month);

            return [
                'label' => $month.'月',
                'sales' => (int) ($row->sales_total ?? 0),
                'profit' => (int) ($row->profit_total ?? 0),
                'count' => (int) ($row->sold_count ?? 0),
            ];
        });
    }

    private function platformStats(int $userId)
    {
        $platformRows = AuctionItem::selectRaw('platform, COUNT(*) as total')
            ->where('user_id', $userId)
            ->groupBy('platform')
            ->pluck('total', 'platform');

        $platformTotal = max(0, (int) $platformRows->sum());

        return $platformRows
            ->map(function ($count, $platform) use ($platformTotal) {
                return [
                    'name' => $platform ?: 'その他',
                    'count' => (int) $count,
                    'percent' => $platformTotal > 0 ? round(((int) $count / $platformTotal) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    private function insightActions(
        int $staleCount,
        float $profitMargin,
        int $currentMonthSales,
        int $monthlySalesTarget,
        float $monthlyTargetProgress
    ) {
        $actions = collect();

        if ($staleCount > 0) {
            $actions->push([
                'tone' => 'amber',
                'title' => '滞留在庫を見直す',
                'body' => '30日以上動いていない出品中の商品が'.$staleCount.'件あります。価格・写真・説明文を見直すと売上回転が上がります。',
                'href' => route('auction-items.index', ['status' => 'selling']),
                'label' => '在庫を見る',
            ]);
        }

        if ($profitMargin > 0 && $profitMargin < 25) {
            $actions->push([
                'tone' => 'rose',
                'title' => '利益率を改善する',
                'body' => '累計利益率が'.$profitMargin.'%です。送料・販売手数料・仕入れ価格の見直し余地があります。',
                'href' => route('sales.index'),
                'label' => '売上を確認',
            ]);
        }

        if ($currentMonthSales < $monthlySalesTarget) {
            $actions->push([
                'tone' => 'cyan',
                'title' => '今月目標まであと'.number_format($monthlySalesTarget - $currentMonthSales).'円',
                'body' => '今月の売上進捗は'.$monthlyTargetProgress.'%です。出品数を増やすか、回転の早いカテゴリを優先しましょう。',
                'href' => route('auction-items.create'),
                'label' => '商品を登録',
            ]);
        }

        if ($actions->isEmpty()) {
            $actions->push([
                'tone' => 'emerald',
                'title' => '運用状態は良好です',
                'body' => '売上・利益率・在庫回転のバランスが取れています。カテゴリ別分析で伸びているジャンルを深掘りしましょう。',
                'href' => route('category-sales.index'),
                'label' => 'カテゴリ分析へ',
            ]);
        }

        return $actions->take(3)->values();
    }
}
