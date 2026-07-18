<?php

use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\AuctionItemController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\MaintenanceLoginController;
use App\Http\Controllers\CategorySalesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\MarketingPageController;
use App\Http\Controllers\PremiumReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::get('/manifest.webmanifest', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/service-worker.js', [PwaController::class, 'serviceWorker'])->name('pwa.service-worker');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

Route::get('/terms', [LegalPageController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalPageController::class, 'privacy'])->name('legal.privacy');
Route::get('/commercial-transactions', [LegalPageController::class, 'commercial'])->name('legal.commercial');
Route::get('/faq', [LegalPageController::class, 'faq'])->name('legal.faq');
Route::get('/contact', [ContactController::class, 'create'])->name('legal.contact');
Route::post('/contact', [ContactController::class, 'store'])->name('legal.contact.store');

Route::get('/features', [MarketingPageController::class, 'features'])->name('marketing.features');
Route::get('/pricing', [MarketingPageController::class, 'pricing'])->name('marketing.pricing');
Route::get('/use-cases', [MarketingPageController::class, 'useCases'])->name('marketing.use-cases');

Route::get('/maintenance-login', MaintenanceLoginController::class)->name('maintenance.login');

Route::get('/', HomeController::class)->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/maintenance', [MaintenanceController::class, 'index'])
        ->name('admin.maintenance.index');

    Route::patch('/admin/maintenance', [MaintenanceController::class, 'update'])
        ->name('admin.maintenance.update');

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

    Route::get('/premium', [SubscriptionController::class, 'index'])
        ->name('subscriptions.index');

    Route::post('/premium/checkout', [SubscriptionController::class, 'checkout'])
        ->name('subscriptions.checkout');

    Route::post('/premium/portal', [SubscriptionController::class, 'portal'])
        ->name('subscriptions.portal');

    Route::get('/premium/success', [SubscriptionController::class, 'success'])
        ->name('subscriptions.success');

    Route::get('/premium/report', [PremiumReportController::class, 'index'])
        ->name('premium.report');

});

require __DIR__.'/auth.php';
