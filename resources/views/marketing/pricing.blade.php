@php
    $pageSeo = config('seo.pages')['marketing.pricing'] ?? [];
    $premiumAmount = (int) config('services.stripe.premium_amount', config('seo.software.default_price', 480));
    $schema = [[
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => config('seo.site_name').' Premium',
        'description' => $pageSeo['description'],
        'brand' => [
            '@type' => 'Brand',
            'name' => config('seo.site_name'),
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $premiumAmount,
            'priceCurrency' => 'JPY',
            'availability' => 'https://schema.org/InStock',
            'url' => route('marketing.pricing'),
        ],
    ]];
@endphp

<x-marketing-layout
    :title="$pageSeo['title']"
    :description="$pageSeo['description']"
    :canonical="route('marketing.pricing')"
    :schema="$schema"
>
    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-black tracking-[0.24em] text-cyan-200">PRICING</p>
            <h1 class="mt-4 max-w-4xl text-4xl font-black leading-tight md:text-5xl">無料で試して、必要な機能だけPremiumで広げる。</h1>
            <p class="mt-5 max-w-3xl text-base font-semibold leading-8 text-cyan-100">
                小規模な古着販売はFreeで始められます。CSV登録、売上分析、ジャンル別分析など継続運用に必要な機能はPremiumで利用できます。
            </p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto grid max-w-5xl gap-5 px-4 sm:px-6 md:grid-cols-2 lg:px-8">
            <article class="rounded-lg border border-slate-200 bg-white p-6">
                <h2 class="text-2xl font-black text-slate-950">Free</h2>
                <p class="mt-3 text-4xl font-black text-slate-950">0円</p>
                <p class="mt-2 text-sm font-bold text-slate-600">まず操作感を試したい方向け</p>
                <ul class="mt-6 space-y-3 font-semibold text-slate-700">
                    <li>商品登録の基本機能</li>
                    <li>画像付き商品管理</li>
                    <li>SOLD管理</li>
                    <li>基本ダッシュボード</li>
                </ul>
            </article>

            <article class="rounded-lg border-2 border-cyan-500 bg-white p-6 shadow-lg">
                <h2 class="text-2xl font-black text-slate-950">Premium</h2>
                <p class="mt-3 text-4xl font-black text-cyan-800">{{ number_format($premiumAmount) }}円/月</p>
                <p class="mt-2 text-sm font-bold text-slate-600">本格的に古着販売を管理したい方向け</p>
                <ul class="mt-6 space-y-3 font-semibold text-slate-700">
                    <li>商品登録数の上限拡張</li>
                    <li>CSV登録・CSV出力</li>
                    <li>ヤフオクCSV変換</li>
                    <li>売上・利益分析</li>
                    <li>ジャンル別分析</li>
                    <li>運用診断レポート</li>
                    <li>今後のバックアップ・エクスポート強化対象</li>
                </ul>
            </article>
        </div>
    </section>
</x-marketing-layout>
