<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuctionItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'canDeleteAccount' => ! $this->isProtectedAccount($request->user()),
            'isPremium' => $request->user()->isPremium(),
            'premiumPrice' => config('services.stripe.premium_amount', 480),
            'canOpenBillingPortal' => is_string($request->user()->stripe_customer_id) && $request->user()->stripe_customer_id !== '',
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($this->isProtectedAccount($user)) {
            return Redirect::route('profile.edit')
                ->with('status', 'protected-account');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $this->deleteUserAuctionItemImages($user->id);
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function isProtectedAccount(?User $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin() || $user->email === User::DEMO_EMAIL;
    }

    private function deleteUserAuctionItemImages(int $userId): void
    {
        AuctionItem::query()
            ->where('user_id', $userId)
            ->select(['id', 'image_path', 'sold_image_path'])
            ->chunkById(100, function ($items) {
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
}
