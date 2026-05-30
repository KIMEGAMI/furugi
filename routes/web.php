<?php

use App\Http\Controllers\AuctionItemController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Models\AuctionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $userId = Auth::id();

    $sellingCount = AuctionItem::where('user_id', $userId)
        ->where('status', 'selling')
        ->count();

    $soldCount = AuctionItem::where('user_id', $userId)
        ->where('status', 'sold')
        ->count();

    $draftCount = AuctionItem::where('user_id', $userId)
        ->where('status', 'draft')
        ->count();

    $soldItems = AuctionItem::where('user_id', $userId)
        ->where('status', 'sold')
        ->get();

    $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));

    $totalSalesFee = $soldItems->sum(function ($item) {
        $soldPrice = (int) ($item->sold_price ?? 0);
        $rate = (float) ($item->sales_fee_rate ?? 0);
        $storedFee = (int) ($item->sales_fee ?? 0);

        return $storedFee > 0 ? $storedFee : (int) round($soldPrice * ($rate / 100));
    });

    $totalShippingFee = $soldItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0));

    $totalProfit = $soldItems->sum(function ($item) {
        $soldPrice = (int) ($item->sold_price ?? 0);
        $purchasePrice = (int) ($item->purchase_price ?? 0);
        $rate = (float) ($item->sales_fee_rate ?? 0);
        $storedFee = (int) ($item->sales_fee ?? 0);
        $salesFee = $storedFee > 0 ? $storedFee : (int) round($soldPrice * ($rate / 100));
        $shippingFee = (int) ($item->shipping_fee ?? 0);

        return $soldPrice - $purchasePrice - $salesFee - $shippingFee;
    });

    $recentItems = AuctionItem::where('user_id', $userId)
        ->latest('updated_at')
        ->take(5)
        ->get();

    return view('dashboard', compact(
        'sellingCount',
        'soldCount',
        'draftCount',
        'totalSales',
        'totalSalesFee',
        'totalShippingFee',
        'totalProfit',
        'recentItems'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
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
