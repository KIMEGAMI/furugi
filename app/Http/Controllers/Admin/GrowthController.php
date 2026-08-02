<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuctionItem;
use App\Models\ContactInquiry;
use App\Models\SubscriptionCancellationFeedback;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GrowthController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $totalUsers = User::query()->count();
        $premiumUsers = User::query()
            ->whereIn('subscription_plan', [User::SUBSCRIPTION_ACTIVE, User::LEGACY_SUBSCRIPTION_PREMIUM])
            ->where(function ($query): void {
                $query
                    ->whereIn('subscription_status', ['active', 'trialing'])
                    ->orWhere('premium_ends_at', '>', now());
            })
            ->count();
        $recentUsers = User::query()
            ->latest('created_at')
            ->take(10)
            ->get(['id', 'name', 'email', 'subscription_status', 'created_at']);
        $activeUsers30Days = User::query()
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return view('admin.growth.index', [
            'summary' => [
                'total_users' => $totalUsers,
                'premium_users' => $premiumUsers,
                'free_users' => max(0, $totalUsers - $premiumUsers),
                'premium_rate' => $totalUsers > 0 ? round(($premiumUsers / $totalUsers) * 100, 1) : 0.0,
                'active_users_30_days' => $activeUsers30Days,
                'total_items' => AuctionItem::query()->count(),
                'sold_items' => AuctionItem::query()->where('status', AuctionItem::STATUS_SOLD)->count(),
                'open_inquiries' => ContactInquiry::query()->where('status', ContactInquiry::STATUS_OPEN)->count(),
                'cancellation_feedback_count' => SubscriptionCancellationFeedback::query()->count(),
            ],
            'recentUsers' => $recentUsers,
            'recentInquiries' => ContactInquiry::query()
                ->with('handledBy')
                ->latest('created_at')
                ->take(10)
                ->get(),
            'cancellationReasons' => SubscriptionCancellationFeedback::query()
                ->select('reason', DB::raw('COUNT(*) as total'))
                ->groupBy('reason')
                ->orderByDesc('total')
                ->get(),
            'recentCancellationFeedback' => SubscriptionCancellationFeedback::query()
                ->with('user')
                ->latest('created_at')
                ->take(10)
                ->get(),
        ]);
    }

    public function handleInquiry(Request $request, ContactInquiry $contactInquiry): RedirectResponse
    {
        $admin = $this->authorizeAdmin($request);

        $contactInquiry->forceFill([
            'status' => ContactInquiry::STATUS_HANDLED,
            'handled_at' => now(),
            'handled_by' => $admin->id,
        ])->save();

        return redirect()
            ->route('admin.growth.index')
            ->with('status', '問い合わせを対応済みにしました。');
    }

    private function authorizeAdmin(Request $request): User
    {
        $user = $request->user();

        abort_unless($user?->isAdmin(), 403);

        return $user;
    }
}
