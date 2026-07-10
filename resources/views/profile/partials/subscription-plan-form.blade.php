<section class="space-y-6">
    @php
        $isPremium = $user->isPremium();
    @endphp

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border {{ $isPremium ? 'border-slate-200 bg-slate-50' : 'border-blue-300 bg-blue-50' }} p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h4 class="text-lg font-black text-slate-900">Free</h4>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        基本機能を無料で利用できます。
                    </p>
                </div>
                @unless ($isPremium)
                    <span class="rounded-full bg-blue-700 px-3 py-1 text-xs font-black text-white">
                        現在のプラン
                    </span>
                @endunless
            </div>
        </div>

        <div class="rounded-2xl border {{ $isPremium ? 'border-blue-300 bg-blue-50' : 'border-slate-200 bg-slate-50' }} p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h4 class="text-lg font-black text-slate-900">Premium</h4>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        分析と管理機能をより活用したい方向けのプランです。
                    </p>
                    @if ($isPremium && $user->subscribed_at)
                        <p class="mt-2 text-xs font-bold text-slate-500">
                            開始日: {{ $user->subscribed_at->format('Y年n月j日') }}
                        </p>
                    @endif
                </div>
                @if ($isPremium)
                    <span class="rounded-full bg-blue-700 px-3 py-1 text-xs font-black text-white">
                        現在のプラン
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if ($isPremium)
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <h4 class="text-base font-black text-red-800">
                契約を管理・解約
            </h4>
            <p class="mt-2 text-sm font-semibold leading-6 text-red-700">
                Premiumを解約するとFreeに戻ります。解約する場合は下の「契約を管理・解約する」ボタンから手続きしてください。
            </p>
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        @if ($isPremium)
            <form method="POST" action="{{ route('subscription.destroy') }}" onsubmit="return confirm('Premiumを解約してFreeに戻しますか？')">
                @csrf
                @method('DELETE')

                <button type="submit" class="rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white shadow transition hover:bg-red-700">
                    契約を管理・解約する
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('subscription.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="plan" value="{{ \App\Models\User::PLAN_PREMIUM }}">

                <button type="submit" class="rounded-xl bg-blue-700 px-6 py-3 text-sm font-bold text-white shadow transition hover:bg-blue-800">
                    Premiumに変更する
                </button>
            </form>
        @endif

        @if (session('status') === 'subscription-updated')
            <p class="text-sm font-bold text-blue-700">
                Premiumに変更しました。
            </p>
        @endif

        @if (session('status') === 'subscription-cancelled')
            <p class="text-sm font-bold text-red-700">
                解約しました。現在はFreeです。
            </p>
        @endif
    </div>
</section>
