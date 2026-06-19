<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $year = (int) now()->year;

        $sellingCount = AuctionItem::where('user_id', $userId)
            ->where('status', 'selling')
            ->count();

        $soldCount = AuctionItem::where('user_id', $userId)
            ->where('status', 'sold')
            ->count();

        $totalSales = AuctionItem::where('user_id', $userId)
            ->where('status', 'sold')
            ->sum('sold_price');

        $totalProfit = AuctionItem::where('user_id', $userId)
            ->where('status', 'sold')
            ->sum('profit');

        $totalSalesFee = AuctionItem::where('user_id', $userId)
            ->where('status', 'sold')
            ->sum('sales_fee');

        $totalShippingFee = AuctionItem::where('user_id', $userId)
            ->where('status', 'sold')
            ->sum('shipping_fee');

        $monthlyStats = collect();

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

            $items = AuctionItem::where('user_id', $userId)
                ->where('status', 'sold')
                ->whereBetween('sold_at', [$startOfMonth, $endOfMonth])
                ->get();

            $monthlyStats->push([
                'label' => $month.'月',
                'sales' => (int) $items->sum('sold_price'),
                'profit' => (int) $items->sum('profit'),
            ]);
        }

        $maxMonthlySales = max($monthlyStats->max('sales') ?? 0, 1);
        $maxMonthlyProfit = max($monthlyStats->max('profit') ?? 0, 1);

        $platformNames = ['メルカリ', 'ヤフオク', 'ラクマ', 'PayPayフリマ', 'その他'];
        $totalItems = max($sellingCount + $soldCount, 1);

        $platformStats = collect($platformNames)->map(function ($platform) use ($userId, $totalItems) {
            $count = AuctionItem::where('user_id', $userId)
                ->where(function ($query) use ($platform) {
                    if ($platform === 'その他') {
                        $query->whereNull('platform')
                            ->orWhere('platform', '')
                            ->orWhere('platform', 'その他');
                    } else {
                        $query->where('platform', $platform);
                    }
                })
                ->count();

            return [
                'name' => $platform,
                'count' => $count,
                'percent' => round(($count / $totalItems) * 100, 1),
            ];
        });

        $recentItems = AuctionItem::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'year',
            'sellingCount',
            'soldCount',
            'totalSales',
            'totalProfit',
            'totalSalesFee',
            'totalShippingFee',
            'monthlyStats',
            'maxMonthlySales',
            'maxMonthlyProfit',
            'platformStats',
            'recentItems'
        ));
    }
}
