<x-marketing-layout
    title="料金プラン | FURUGI Free・Premium 月480円"
    description="FURUGIの料金プラン。FreeプランとPremiumプランの違い、商品登録数、CSV、売上分析、ジャンル別分析、運用診断レポートの利用可否を確認できます。"
>
    @php($premiumAmount = (int) config('services.stripe.premium_amount', 480))

    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-black tracking-[0.24em] text-cyan-200">PRICING</p>
            <h1 class="mt-4 max-w-4xl text-4xl font-black leading-tight md:text-5xl">まず無料で試して、必要になったらPremiumへ。</h1>
            <p class="mt-5 max-w-3xl text-base font-semibold leading-8 text-slate-200">FURUGIは小さく始められるFreeプランと、本格運用向けのPremiumプランを用意しています。</p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto grid max-w-5xl gap-5 px-4 sm:px-6 md:grid-cols-2 lg:px-8">
            <article class="rounded-lg border border-slate-200 p-6">
                <h2 class="text-2xl font-black">Free</h2>
                <p class="mt-3 text-4xl font-black">0円</p>
                <ul class="mt-6 space-y-3 font-semibold text-slate-700">
                    <li>商品登録30件まで</li>
                    <li>画像付き商品管理</li>
                    <li>SOLD管理</li>
                    <li>基本ダッシュボード</li>
                </ul>
            </article>

            <article class="rounded-lg border-2 border-cyan-500 p-6 shadow-lg">
                <h2 class="text-2xl font-black">Premium</h2>
                <p class="mt-3 text-4xl font-black">{{ number_format($premiumAmount) }}円/月</p>
                <ul class="mt-6 space-y-3 font-semibold text-slate-700">
                    <li>商品登録数の上限拡張</li>
                    <li>CSV取込・CSV出力</li>
                    <li>売上分析</li>
                    <li>ジャンル別分析</li>
                    <li>運用診断レポート</li>
                    <li>値下げ候補・低利益商品の自動抽出</li>
                </ul>
            </article>
        </div>
    </section>
</x-marketing-layout>
