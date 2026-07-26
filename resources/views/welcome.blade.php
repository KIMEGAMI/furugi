@php
    $siteName = config('seo.site_name', 'FURUGI MANAGER');
    $pageSeo = config('seo.pages.home', []);
    $title = $pageSeo['title'] ?? config('seo.title');
    $description = $pageSeo['description'] ?? config('seo.description');
    $keywords = config('seo.keywords');
    $canonical = route('home');
    $heroImage = asset('images/furugi-manager-hero.png');
    $valueImage = asset('images/furugi-manager-value.png');
    $premiumAmount = (int) config('services.stripe.premium_amount', config('seo.software.default_price', 480));
    $softwareSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => $siteName,
        'applicationCategory' => config('seo.software.category'),
        'operatingSystem' => config('seo.software.operating_system'),
        'description' => $description,
        'url' => $canonical,
        'image' => [$heroImage, $valueImage],
        'inLanguage' => 'ja',
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $premiumAmount,
            'priceCurrency' => config('seo.software.price_currency', 'JPY'),
            'availability' => 'https://schema.org/InStock',
        ],
        'featureList' => [
            '画像付き商品登録',
            '古着在庫管理',
            'SOLD管理',
            '売上・利益分析',
            'CSV登録',
            'ヤフオクCSV変換',
            '重複チェック',
            'PWA対応',
        ],
    ];
    $webSiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => $canonical,
        'description' => $description,
        'inLanguage' => 'ja',
    ];
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ config('seo.locale', 'ja_JP') }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $heroImage }}">
    <meta property="og:image:alt" content="FURUGI MANAGER 古着販売向け在庫管理システム">

    <meta name="twitter:card" content="{{ config('seo.twitter_card', 'summary_large_image') }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $heroImage }}">

    <x-pwa-head />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">@json($softwareSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    <script type="application/ld+json">@json($webSiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
</head>

<body class="bg-white text-slate-950 antialiased">
    <header class="fixed inset-x-0 top-0 z-30 border-b border-white/30 bg-white/82 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="FURUGI MANAGER トップページ">
                <span class="text-lg font-black tracking-[0.22em] text-cyan-700">FURUGI</span>
                <span class="hidden text-xs font-black tracking-[0.34em] text-cyan-700 sm:inline">MANAGER</span>
            </a>
            <nav class="flex flex-wrap justify-end gap-x-3 gap-y-2 text-sm font-black" aria-label="メインナビゲーション">
                <a href="{{ route('marketing.features') }}" class="px-2 py-2 text-cyan-800 hover:text-cyan-600">機能</a>
                <a href="{{ route('marketing.use-cases') }}" class="px-2 py-2 text-cyan-800 hover:text-cyan-600">活用例</a>
                <a href="{{ route('marketing.pricing') }}" class="px-2 py-2 text-cyan-800 hover:text-cyan-600">料金</a>
                <a href="{{ route('legal.faq') }}" class="px-2 py-2 text-cyan-800 hover:text-cyan-600">FAQ</a>
                <a href="{{ route('login') }}" class="rounded-md border border-cyan-300 px-4 py-2 text-cyan-800 hover:bg-cyan-50">ログイン</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="relative min-h-[88svh] overflow-hidden pt-20">
            <img src="{{ $heroImage }}" alt="FURUGI MANAGER 古着販売向け在庫管理システムの紹介画像" class="absolute inset-0 h-full w-full object-cover object-center">
            <div class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-white via-white/80 to-transparent"></div>

            <div class="relative mx-auto flex min-h-[calc(88svh-80px)] max-w-7xl items-end px-4 pb-10 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <h1 class="sr-only">古着販売の在庫管理・売上管理ならFURUGI MANAGER</h1>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="rounded-md bg-cyan-700 px-7 py-4 text-center text-sm font-black text-white shadow-lg shadow-cyan-900/20 hover:bg-cyan-800">
                            無料で始める
                        </a>
                        <form method="POST" action="{{ route('login.demo') }}">
                            @csrf
                            <button type="submit" class="rounded-md border border-cyan-300 bg-white/90 px-7 py-4 text-sm font-black text-black shadow-lg hover:bg-white">
                                デモを見る
                            </button>
                        </form>
                    </div>
                    <p class="mt-5 max-w-2xl text-sm font-black leading-7 text-cyan-950">
                        商品登録、画像管理、在庫状態、SOLD、売上、CSVをまとめて管理。古着販売の日々の作業を、もっと見やすく、もっと速くします。
                    </p>
                </div>
            </div>
        </section>

        <section id="features" class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-black tracking-[0.24em] text-cyan-700">FEATURES</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-cyan-950 sm:text-4xl">古着販売の管理を、ひとつの画面で。</h2>
                    <p class="mt-4 text-base font-bold leading-8 text-slate-700">
                        複数サービスへ出品していると、商品名、価格、画像、販売状況、利益の管理が散らかりやすくなります。FURUGI MANAGERは、その運用を毎日使える形に整理します。
                    </p>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['商品管理', '画像付きで商品を登録し、出品中・SOLD・下書きを整理できます。'],
                        ['CSV登録', '古着システムCSV、ヤフオクCSV変換、今後の販路CSV追加に備えた登録画面を用意しています。'],
                        ['売上分析', '売上、手数料、送料、実利益を確認し、販売の状態を追えます。'],
                        ['運用改善', '重複チェックやジャンル別分析で、毎日の管理ミスを減らします。'],
                    ] as [$heading, $body])
                        <article class="rounded-lg border border-cyan-100 bg-cyan-50/70 p-6">
                            <h3 class="text-lg font-black text-cyan-950">{{ $heading }}</h3>
                            <p class="mt-3 text-sm font-bold leading-7 text-slate-700">{{ $body }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white pb-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <img
                    src="{{ $valueImage }}"
                    alt="FURUGI MANAGER 月額{{ number_format($premiumAmount) }}円の古着管理システム案内"
                    class="w-full rounded-lg border border-cyan-100 shadow-xl"
                    loading="lazy"
                >
            </div>
        </section>

        <section id="workflow" class="bg-cyan-950 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black tracking-[0.24em] text-cyan-200">WORKFLOW</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">登録から振り返りまで、作業が途切れない。</h2>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach ([
                            ['1', '商品を登録', '画像、仕入れ価格、販売価格、ジャンルを入力します。'],
                            ['2', 'SOLDに更新', '販売後にSOLD化し、手数料や送料を反映します。'],
                            ['3', '利益を確認', '売上と実利益を見て、次の仕入れや値下げ判断へつなげます。'],
                        ] as [$step, $heading, $body])
                            <article class="rounded-lg border border-white/15 bg-white/10 p-5">
                                <p class="text-sm font-black text-cyan-200">STEP {{ $step }}</p>
                                <h3 class="mt-3 text-xl font-black">{{ $heading }}</h3>
                                <p class="mt-3 text-sm font-bold leading-7 text-cyan-50">{{ $body }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg border border-cyan-100 bg-cyan-50 p-6 sm:p-8">
                    <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div>
                            <p class="text-sm font-black tracking-[0.24em] text-cyan-700">PLAN</p>
                            <h2 class="mt-3 text-3xl font-black text-cyan-950">まず無料で試して、必要になったらPremiumへ。</h2>
                            <p class="mt-4 text-sm font-bold leading-7 text-slate-700">
                                Freeは小さく試すためのプランです。Premiumでは登録数の上限拡張、CSV、売上分析、ジャンル別分析、運用診断を使えます。
                            </p>
                        </div>
                        <div class="rounded-lg bg-white p-6 text-center shadow-sm">
                            <p class="text-sm font-black text-cyan-700">Premium</p>
                            <p class="mt-2 text-4xl font-black text-cyan-950">¥{{ number_format($premiumAmount) }}</p>
                            <p class="mt-1 text-xs font-bold text-slate-600">月額</p>
                            <a href="{{ route('register') }}" class="mt-5 inline-flex rounded-md bg-cyan-700 px-6 py-3 text-sm font-black text-white hover:bg-cyan-800">
                                はじめる
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-cyan-100 bg-white py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm font-bold text-cyan-900 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap gap-x-5 gap-y-2" aria-label="フッター">
                <a href="{{ route('marketing.features') }}" class="hover:text-cyan-600">機能</a>
                <a href="{{ route('marketing.use-cases') }}" class="hover:text-cyan-600">活用例</a>
                <a href="{{ route('marketing.pricing') }}" class="hover:text-cyan-600">料金</a>
                <a href="{{ route('legal.terms') }}" class="hover:text-cyan-600">利用規約</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-cyan-600">プライバシーポリシー</a>
                <a href="{{ route('legal.commercial') }}" class="hover:text-cyan-600">特定商取引法に基づく表記</a>
                <a href="{{ route('legal.faq') }}" class="hover:text-cyan-600">よくある質問</a>
                <a href="{{ route('legal.contact') }}" class="hover:text-cyan-600">お問い合わせ</a>
            </nav>
            <p>&copy; 2026 FURUGI MANAGER. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
