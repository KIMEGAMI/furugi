<?php

use App\Http\Controllers\Admin\BulkMailController;
use App\Http\Controllers\Admin\GrowthController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\NoticeController as AdminNoticeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuctionItemController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\MaintenanceLoginController;
use App\Http\Controllers\CategorySalesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\MarketingPageController;
use App\Http\Controllers\NoticeController;
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
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/maintenance', [MaintenanceController::class, 'index'])
        ->name('admin.maintenance.index');

    Route::patch('/admin/maintenance', [MaintenanceController::class, 'update'])
        ->name('admin.maintenance.update');

    Route::post('/admin/notices', [AdminNoticeController::class, 'store'])
        ->name('admin.notices.store');

    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');

    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])
        ->name('admin.users.destroy');

    Route::get('/admin/bulk-mail', [BulkMailController::class, 'index'])
        ->name('admin.bulk-mail.index');

    Route::post('/admin/bulk-mail', [BulkMailController::class, 'store'])
        ->name('admin.bulk-mail.store');

    Route::get('/admin/growth', [GrowthController::class, 'index'])
        ->name('admin.growth.index');

    Route::patch('/admin/growth/inquiries/{contactInquiry}', [GrowthController::class, 'handleInquiry'])
        ->name('admin.growth.inquiries.handle');

    Route::get('/notices', [NoticeController::class, 'index'])
        ->name('notices.index');

    Route::get('/notices/{notice}', [NoticeController::class, 'show'])
        ->name('notices.show');

    Route::get('/auction-items/csv-import', [AuctionItemController::class, 'csvImport'])
        ->middleware('premium')
        ->name('auction-items.csv-import');

    Route::post('/auction-items/import', [AuctionItemController::class, 'importCsv'])
        ->middleware('premium')
        ->name('auction-items.import');

    Route::post('/auction-items/import/yahoo-auctions', [AuctionItemController::class, 'importYahooAuctionCsv'])
        ->middleware('premium')
        ->name('auction-items.import.yahoo-auctions');

    Route::post('/auction-items/import/mercari-shops', [AuctionItemController::class, 'importMercariShopsCsv'])
        ->middleware('premium')
        ->name('auction-items.import.mercari-shops');

    Route::get('/auction-items/duplicates', [AuctionItemController::class, 'duplicates'])
        ->middleware('premium')
        ->name('auction-items.duplicates');

    Route::delete('/auction-items/duplicates', [AuctionItemController::class, 'deleteDuplicates'])
        ->middleware('premium')
        ->name('auction-items.duplicates.destroy');

    Route::get('/auction-items/delete-all/confirm', [AuctionItemController::class, 'confirmBulkDestroy'])
        ->name('auction-items.bulk-destroy.confirm');

    Route::delete('/auction-items/delete-all', [AuctionItemController::class, 'bulkDestroy'])
        ->name('auction-items.bulk-destroy');

    Route::resource('auction-items', AuctionItemController::class);

    Route::patch('/auction-items/{auctionItem}/sold', [AuctionItemController::class, 'markAsSold'])
        ->name('auction-items.sold');

    Route::patch('/auction-items/{auctionItem}/selling', [AuctionItemController::class, 'markAsSelling'])
        ->name('auction-items.selling');

    Route::get('/sales', [SalesController::class, 'index'])
        ->middleware('premium')
        ->name('sales.index');

    Route::get('/sales/csv', [SalesController::class, 'downloadCsv'])
        ->middleware('premium')
        ->name('sales.csv');

    Route::get('/sales/backup-csv', [SalesController::class, 'downloadBackupCsv'])
        ->middleware('premium')
        ->name('sales.backup-csv');

    Route::get('/sales/restore-csv', [SalesController::class, 'downloadRestoreCsv'])
        ->middleware('premium')
        ->name('sales.restore-csv');

    Route::get('/sales/selling-csv', [SalesController::class, 'downloadSellingCsv'])
        ->middleware('premium')
        ->name('sales.selling-csv');

    Route::get('/category-sales', [CategorySalesController::class, 'index'])
        ->middleware('premium')
        ->name('category-sales.index');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/billing', [SubscriptionController::class, 'index'])
        ->name('subscriptions.index');

    Route::get('/premium', fn () => redirect()->route('subscriptions.index'))
        ->name('subscriptions.legacy');

    Route::post('/billing/checkout', [SubscriptionController::class, 'checkout'])
        ->name('subscriptions.checkout');

    Route::post('/billing/portal', [SubscriptionController::class, 'portal'])
        ->name('subscriptions.portal');

    Route::post('/billing/cancel-feedback', [SubscriptionController::class, 'cancelFeedback'])
        ->name('subscriptions.cancel-feedback');

    Route::get('/billing/success', [SubscriptionController::class, 'success'])
        ->name('subscriptions.success');
});

require __DIR__.'/auth.php';
