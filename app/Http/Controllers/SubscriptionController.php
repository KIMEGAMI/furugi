<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'string', Rule::in([User::PLAN_PREMIUM])],
        ]);

        if ($validated['plan'] === User::PLAN_PREMIUM) {
            $request->user()->subscribeToPremium();
        }

        return back()->with('status', 'subscription-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->cancelSubscription();

        return back()->with('status', 'subscription-cancelled');
    }
}
