<x-app-layout>
    @php
        $cancellationReasons = \App\Models\SubscriptionCancellationFeedback::REASONS;
        $isAdmin = $user->isAdmin();
        $hasStripeSubscription = $hasStripeSubscription ?? false;
    @endphp

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @unless ($hasActiveSubscription || $isAdmin)
                <section class="mb-6 rounded-lg border border-cyan-200 bg-white p-6 shadow-xl">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-black tracking-[0.18em] text-cyan-700">UPGRADE</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-900">
                                {{ session('upgrade_title', 'Premiumプランで制限を解除できます。') }}
                            </h2>
                            <p class="mt-3 max-w-2xl text-sm font-bold leading-7 text-slate-600">
                                {{ session('upgrade_description', 'Premiumは月額480円で、商品登録数とカテゴリ数の制限がなくなり、CSV登録、売上分析、ジャンル別分析、重複チェックを利用できます。') }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('subscriptions.checkout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-cyan-700 px-6 py-3 text-sm font-black text-white shadow hover:bg-cyan-800 sm:w-auto">
                                Premiumに登録する
                            </button>
                        </form>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach (session('upgrade_features', [
                            '商品登録数の制限なし',
                            'カテゴリ数の制限なし',
                            'CSV登録・CSV変換登録',
                            '売上分析・CSV出力',
                            'ジャンル別売上分析',
                            '重複チェック',
                        ]) as $feature)
                            <div class="rounded-lg bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-950">
                                {{ $feature }}
                            </div>
                        @endforeach
                    </div>
                </section>
            @endunless

            <section class="rounded-lg bg-white p-6 shadow-xl md:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black tracking-[0.18em] text-cyan-700">STRIPE BILLING</p>
                        <h1 class="mt-2 text-3xl font-black text-slate-900 md:text-4xl">契約・解約</h1>
                        <p class="mt-4 max-w-2xl text-sm font-bold leading-7 text-slate-600">
                            Stripeで契約登録、支払い方法の変更、領収書確認、解約を行います。管理者アカウントはStripe契約者とは分けて表示します。
                        </p>
                    </div>

                    <div class="rounded-lg border border-cyan-100 bg-cyan-50 p-5 text-center">
                        <p class="text-xs font-black tracking-wider text-cyan-700">MONTHLY</p>
                        <p class="mt-2 text-4xl font-black text-slate-900">¥{{ number_format($price) }}</p>
                        <p class="mt-1 text-xs font-bold text-slate-500">金額と税込設定はStripe設定に従います</p>
                    </div>
                </div>

                <div class="mt-8 rounded-lg border border-slate-200 p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black text-slate-900">現在の契約状態</h2>
                            @if ($isAdmin && ! $hasStripeSubscription)
                                <p class="mt-2 text-sm font-bold text-blue-700">管理者アカウントです。Stripe契約者ではありません。</p>
                            @elseif ($hasActiveSubscription)
                                <p class="mt-2 text-sm font-bold text-emerald-700">契約中です。</p>
                            @else
                                <p class="mt-2 text-sm font-bold text-slate-600">有効な契約は確認できていません。</p>
                            @endif

                            @if ($user->subscription_status)
                                <p class="mt-1 text-xs font-bold text-slate-500">
                                    Stripeステータス: {{ $user->subscription_status }}
                                </p>
                            @endif

                            @if ($isAdmin && ! $hasStripeSubscription)
                                <p class="mt-1 text-xs font-bold text-slate-500">
                                    管理者権限で利用可能。stripe_customer_id / stripe_subscription_id は未登録です。
                                </p>
                            @endif
                        </div>

                        @if ($hasActiveSubscription && $hasStripeSubscription)
                            <div class="flex flex-col gap-3 sm:items-end">
                                <form method="POST" action="{{ route('subscriptions.portal') }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-slate-900 px-6 py-3 text-sm font-black text-white shadow hover:bg-slate-800">
                                        契約・支払いを管理する
                                    </button>
                                </form>
                                <p class="text-xs font-bold text-slate-500">解約は下の「解約に進む」から行えます。</p>
                            </div>
                        @elseif ($isAdmin)
                            <div class="max-w-xs rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm font-bold leading-6 text-blue-900">
                                管理者権限で運用機能を利用できます。Stripeの契約管理・解約は、実際にStripe契約したユーザーのみ表示されます。
                            </div>
                        @else
                            <form method="POST" action="{{ route('subscriptions.checkout') }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-cyan-700 px-6 py-3 text-sm font-black text-white shadow hover:bg-cyan-800">
                                    Stripeで契約登録する
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-8 rounded-lg border border-amber-200 bg-amber-50 p-5">
                    <h2 class="text-lg font-black text-amber-900">解約について</h2>
                    <p class="mt-3 text-sm font-bold leading-7 text-amber-800">
                        解約ボタンはStripeの契約管理画面内に表示されます。解約理由を送信するとStripeの契約管理画面へ移動します。
                    </p>

                    @if ($hasActiveSubscription && $hasStripeSubscription)
                        <form method="POST" action="{{ route('subscriptions.cancel-feedback') }}" class="mt-5 space-y-4">
                            @csrf

                            <div>
                                <label for="reason" class="block text-sm font-black text-amber-950">解約理由</label>
                                <select id="reason" name="reason" class="mt-2 w-full rounded-lg border-amber-300 text-slate-950 shadow-sm focus:border-amber-600 focus:ring-amber-600" required>
                                    <option value="">選択してください</option>
                                    @foreach ($cancellationReasons as $reasonValue => $reasonLabel)
                                        <option value="{{ $reasonValue }}" @selected(old('reason') === $reasonValue)>{{ $reasonLabel }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>

                            <div>
                                <label for="detail" class="block text-sm font-black text-amber-950">詳細 任意</label>
                                <textarea id="detail" name="detail" rows="4" maxlength="1000" class="mt-2 w-full rounded-lg border-amber-300 text-slate-950 shadow-sm focus:border-amber-600 focus:ring-amber-600">{{ old('detail') }}</textarea>
                                <x-input-error :messages="$errors->get('detail')" class="mt-2" />
                            </div>

                            <button type="submit" class="rounded-lg bg-amber-700 px-6 py-3 text-sm font-black text-white shadow hover:bg-amber-800">
                                解約に進む
                            </button>
                        </form>
                    @elseif ($isAdmin)
                        <div class="mt-5 rounded-lg border border-blue-200 bg-white p-4 text-sm font-bold leading-7 text-blue-900">
                            このadminユーザーはDB上の管理者権限で利用しているため、Stripe上の解約対象はありません。
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
