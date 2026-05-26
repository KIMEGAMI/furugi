<?php

use App\Http\Controllers\AuctionItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/auction-items', [AuctionItemController::class, 'index'])->name('auction-items.index');
    Route::get('/auction-items/create', [AuctionItemController::class, 'create'])->name('auction-items.create');
    Route::post('/auction-items', [AuctionItemController::class, 'store'])->name('auction-items.store');
    Route::get('/auction-items/{auctionItem}', [AuctionItemController::class, 'show'])->name('auction-items.show');
    Route::get('/auction-items/{auctionItem}/edit', [AuctionItemController::class, 'edit'])->name('auction-items.edit');
    Route::patch('/auction-items/{auctionItem}', [AuctionItemController::class, 'update'])->name('auction-items.update');


    Route::patch('/auction-items/{auctionItem}/sold', [AuctionItemController::class, 'markAsSold'])->name('auction-items.sold');

    Route::delete('/auction-items/{auctionItem}', [AuctionItemController::class, 'destroy'])->name('auction-items.destroy');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';