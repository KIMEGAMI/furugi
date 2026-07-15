<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <section class="rounded-3xl bg-white p-6 shadow-xl md:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black text-blue-700">FURUGI PREMIUM</p>
                        <h1 class="mt-2 text-3xl font-black text-slate-900 md:text-4xl">月{{ number_format($price) }}円で本格運用へ</h1>
                        <p class="mt-4 max-w-2xl text-sm font-bold leading-7 text-slate-600">
                            無料プランは小さく試すためのプランです。Premiumでは、商品登録数、CSV取込、売上分析、ジャンル分析、改善提案を解放します。
                        </p>
                    </div>

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-center">
                        <p class="text-xs font-black tracking-wider text-blue-700">MONTHLY</p>
                        <p class="mt-2 text-4xl font-black text-slate-900">¥{{ number_format($price) }}</p>
                        <p class="mt-1 text-xs font-bold text-slate-500">税込表示はStripe設定に従います</p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-900">Free</h2>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">現在の基本プラン</span>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-600">まずは管理を試したい方向け。</p>
                        <ul class="mt-5 space-y-3 text-sm font-bold text-slate-700">
                            <li>商品登録 {{ number_format($freeItemLimit) }}件まで</li>
                            <li>商品画像登録</li>
                            <li>SOLD管理</li>
                            <li>基本ダッシュボード</li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border-2 border-blue-500 bg-blue-50 p-5 shadow-lg">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-900">Premium</h2>
                            <span class="rounded-full bg-blue-700 px-3 py-1 text-xs font-black text-white">おすすめ</span>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-700">古着販売を継続運用する方向け。</p>
                        <ul class="mt-5 space-y-3 text-sm font-bold text-slate-800">
                            <li>商品登録数の制限なし</li>
                            <li>CSV取込</li>
                            <li>売上管理とCSV出力</li>
                            <li>ジャンル別売上分析</li>
                            <li>Premium Insights</li>
                        </ul>

                        @if ($isPremium)
                            <div class="mt-6 rounded-xl bg-emerald-100 px-4 py-3 text-sm font-black text-emerald-800">
                                Premiumが有効です。
                            </div>
                            <form method="POST" action="{{ route('subscriptions.portal') }}" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-slate-800 px-5 py-3 text-sm font-black text-white shadow hover:bg-slate-900">
                                    支払い・解約を管理する
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('subscriptions.checkout') }}" class="mt-6">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white shadow hover:bg-blue-800">
                                    Premiumを開始する
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <h2 class="text-lg font-black text-amber-900">480円で劣らないために必要な見せ方</h2>
                    <p class="mt-3 text-sm font-bold leading-7 text-amber-800">
                        競合の在庫・売上管理ツールと比べると、価格だけでなく「登録制限」「分析」「CSV」「改善提案」が明確に見えることが重要です。
                        この画面ではFreeとPremiumの差を一目で比較できるようにしています。
                    </p>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
