<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-black text-blue-400">
                プロフィール設定
            </h2>
            <p class="mt-1 text-sm text-cyan-200">
                アカウント情報、メールアドレス、パスワードを確認・変更できます。
            </p>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="mx-auto max-w-5xl space-y-8 px-4 sm:px-6 lg:px-8">
            @foreach (['success' => 'blue', 'error' => 'red'] as $flashKey => $color)
                @if(session($flashKey))
                    <div class="rounded-2xl border border-{{ $color }}-200 bg-{{ $color }}-50 px-6 py-5">
                        <p class="font-bold text-{{ $color }}-700">{{ session($flashKey) }}</p>
                    </div>
                @endif
            @endforeach

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow">
                <div class="border-b border-slate-200 bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-6">
                    <h3 class="text-xl font-black text-white">基本情報</h3>
                    <p class="mt-1 text-sm text-blue-50">
                        ユーザー名とメールアドレスを変更できます。
                    </p>
                </div>

                <div class="p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow">
                <div class="border-b border-slate-200 px-8 py-6">
                    <h3 class="text-xl font-black text-slate-900">パスワード変更</h3>
                    <p class="mt-1 text-sm text-cyan-200">
                        安全のため、定期的なパスワード変更をおすすめします。
                    </p>
                </div>

                <div class="p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-cyan-200 bg-white shadow">
                <div class="border-b border-cyan-200 px-8 py-6">
                    <h3 class="text-xl font-black text-slate-900">サブスクリプション</h3>
                    <p class="mt-1 text-sm font-bold text-slate-600">
                        Premiumの開始、支払い方法の変更、解約はStripeの安全な画面で管理します。
                    </p>
                </div>

                <div class="p-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-black text-cyan-700">現在のプラン</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ $isPremium ? 'Premium' : 'Free' }}
                            </p>
                            <p class="mt-2 text-sm font-bold leading-7 text-slate-600">
                                @if ($isPremium)
                                    解約は「支払い・解約を管理」からStripeの管理画面を開いて行います。解約後も、請求期間の終了まではPremium機能を利用できる場合があります。
                                @else
                                    Premiumは月{{ number_format($premiumPrice) }}円です。決済はStripe Checkoutで行います。
                                @endif
                            </p>
                        </div>

                        <div class="w-full lg:w-72">
                            @if ($isPremium && $canOpenBillingPortal)
                                <form method="POST" action="{{ route('subscriptions.portal') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl bg-cyan-700 px-5 py-3 text-sm font-black text-white shadow transition hover:bg-cyan-800">
                                        支払い・解約を管理
                                    </button>
                                </form>
                            @elseif ($isPremium)
                                <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-black text-amber-800">
                                    Stripe決済情報がありません。デモまたは管理者用Premiumの可能性があります。
                                </div>
                            @else
                                <form method="POST" action="{{ route('subscriptions.checkout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl bg-cyan-700 px-5 py-3 text-sm font-black text-white shadow transition hover:bg-cyan-800">
                                        StripeでPremiumを開始
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($canDeleteAccount)
            <div class="overflow-hidden rounded-3xl border border-red-100 bg-white shadow">
                <div class="border-b border-red-100 bg-red-50 px-8 py-6">
                    <h3 class="text-xl font-black text-red-700">アカウント削除</h3>
                    <p class="mt-1 text-sm text-red-500">
                        アカウントを削除すると復元できません。
                    </p>
                </div>

                <div class="p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
