<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

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

        $recentItems = AuctionItem::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'sellingCount',
            'soldCount',
            'totalSales',
            'totalProfit',
            'recentItems'
        ));
    }
}