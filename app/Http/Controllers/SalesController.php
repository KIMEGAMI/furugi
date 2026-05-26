<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        $soldItems = AuctionItem::where('user_id', Auth::id())
            ->where('status', 'sold')
            ->latest('sold_at')
            ->get();

        $totalSales = $soldItems->sum('sold_price');

        $totalProfit = $soldItems->sum('profit');

        $monthlySales = $soldItems
            ->groupBy(function ($item) {
                return optional($item->sold_at)->format('Y-m');
            })
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'sales' => $items->sum('sold_price'),
                    'profit' => $items->sum('profit'),
                ];
            });

        return view('sales.index', [
            'soldItems' => $soldItems,
            'totalSales' => $totalSales,
            'totalProfit' => $totalProfit,
            'monthlySales' => $monthlySales,
        ]);
    }
}