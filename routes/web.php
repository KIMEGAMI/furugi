<?php

use App\Http\Controllers\AuctionItemController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CategorySalesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

    Route::get('/category-sales', [CategorySalesController::class, 'index'])
        ->name('category-sales.index');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';
