<?php

use App\Http\Controllers\AuctionItemController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Models\AuctionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $userId = Auth::id();
    $year = (int) now()->format('Y');

    $sellingCount = AuctionItem::where('user_id', $userId)->where('status', 'selling')->count();
    $soldCount = AuctionItem::where('user_id', $userId)->where('status', 'sold')->count();
    $draftCount = AuctionItem::where('user_id', $userId)->where('status', 'draft')->count();

    $soldItems = AuctionItem::where('user_id', $userId)->where('status', 'sold')->get();

    $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
    $totalSalesFee = $soldItems->sum(fn ($item) => (int) ($item->sales_fee ?? 0));
    $totalShippingFee = $soldItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0));
    $totalProfit = $soldItems->sum(fn ($item) => (int) ($item->profit ?? 0));

    $monthlyRows = AuctionItem::selectRaw('
            MONTH(sold_at) as month_number,
            SUM(sold_price) as sales_total,
            SUM(profit) as profit_total
        ')
        ->where('user_id', $userId)
        ->where('status', 'sold')
        ->whereNotNull('sold_at')
        ->whereYear('sold_at', $year)
        ->groupBy(DB::raw('MONTH(sold_at)'))
        ->get()
        ->keyBy('month_number');

    $monthlyStats = collect(range(1, 12))->map(function ($month) use ($monthlyRows) {
        $row = $monthlyRows->get($month);

        return [
            'label' => $month.'月',
            'sales' => (int) ($row->sales_total ?? 0),
            'profit' => (int) ($row->profit_total ?? 0),
        ];
    });

    $maxMonthlySales = max(1, (int) $monthlyStats->max('sales'));
    $maxMonthlyProfit = max(1, (int) $monthlyStats->max('profit'));

    $platformNames = ['メルカリ', 'ヤフオク', 'ラクマ', 'PayPayフリマ', 'その他'];

    $platformRows = AuctionItem::selectRaw('platform, COUNT(*) as total')
        ->where('user_id', $userId)
        ->groupBy('platform')
        ->pluck('total', 'platform');

    $platformTotal = max(0, (int) $platformRows->sum());

    $platformStats = collect($platformNames)->map(function ($platform) use ($platformRows, $platformTotal) {
        $count = (int) ($platformRows[$platform] ?? 0);

        return [
            'name' => $platform,
            'count' => $count,
            'percent' => $platformTotal > 0 ? round(($count / $platformTotal) * 100, 1) : 0,
        ];
    });

    $recentItems = AuctionItem::where('user_id', $userId)
        ->latest('updated_at')
        ->take(6)
        ->get();

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
        'recentItems'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/auction-items/import', [AuctionItemController::class, 'importCsv'])
        ->name('auction-items.import');

    Route::resource('auction-items', AuctionItemController::class);

    Route::patch('/auction-items/{auctionItem}/sold', [AuctionItemController::class, 'markAsSold'])
        ->name('auction-items.sold');

    Route::patch('/auction-items/{auctionItem}/selling', [AuctionItemController::class, 'markAsSelling'])
        ->name('auction-items.selling');

    Route::get('/sales', [SalesController::class, 'index'])
        ->name('sales.index');

    Route::get('/sales/csv', [SalesController::class, 'downloadCsv'])
        ->name('sales.csv');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
