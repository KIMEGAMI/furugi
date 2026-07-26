<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    private const STRIPE_API_BASE = 'https://api.stripe.com/v1';

    private const USERS_PER_PAGE = 20;

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.users.index', [
            'users' => User::query()
                ->withCount('auctionItems')
                ->latest('created_at')
                ->paginate(self::USERS_PER_PAGE),
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $admin = $this->authorizeAdmin($request);

        if ($admin->is($user)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', '自分自身のアカウントは強制退会できません。');
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', '管理者アカウントは強制退会できません。');
        }

        if (! $this->cancelStripeSubscriptionIfNeeded($user)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Stripe契約の停止に失敗したため、強制退会を中止しました。');
        }

        $this->deleteUserAuctionItemImages($user->id);

        DB::transaction(function () use ($user): void {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'ユーザーを強制退会しました。');
    }

    private function authorizeAdmin(Request $request): User
    {
        $user = $request->user();

        abort_unless($user?->isAdmin(), 403);

        return $user;
    }

    private function deleteUserAuctionItemImages(int $userId): void
    {
        AuctionItem::query()
            ->where('user_id', $userId)
            ->select(['id', 'image_path', 'sold_image_path'])
            ->chunkById(100, function ($items): void {
                $paths = $items
                    ->flatMap(fn (AuctionItem $item) => [$item->image_path, $item->sold_image_path])
                    ->filter(fn (?string $path) => $this->isSafeAuctionItemImagePath($path))
                    ->unique()
                    ->values()
                    ->all();

                if ($paths !== []) {
                    Storage::disk('public')->delete($paths);
                }
            });
    }

    private function isSafeAuctionItemImagePath(?string $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        if (str_contains($path, '..') || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            return false;
        }

        return str_starts_with($path, 'auction-items/');
    }

    private function cancelStripeSubscriptionIfNeeded(User $user): bool
    {
        if (! is_string($user->stripe_subscription_id) || $user->stripe_subscription_id === '') {
            return true;
        }

        $secret = config('services.stripe.secret');

        if (! is_string($secret) || $secret === '') {
            return false;
        }

        $response = Http::asForm()
            ->withToken($secret)
            ->delete(self::STRIPE_API_BASE.'/subscriptions/'.rawurlencode($user->stripe_subscription_id));

        return $response->successful();
    }
}
