@php
    $siteName = $seo['site_name'] ?? config('app.name', 'FURUGI');
    $title = $seo['title'] ?? $siteName;
    $description = $seo['description'] ?? '';
    $keywords = $seo['keywords'] ?? '';
    $canonical = route('home');
    $image = asset(ltrim($seo['image'] ?? 'images/logo.png', '/'));
    $premiumAmount = (int) config('services.stripe.premium_amount', 480);
    $applicationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => $siteName,
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Web',
        'description' => $description,
        'url' => $canonical,
        'image' => $image,
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $premiumAmount,
            'priceCurrency' => 'JPY',
            'availability' => 'https://schema.org/InStock',
        ],
        'featureList' => [
            '古着販売の商品登録',
            '在庫管理とSOLD管理',
            '画像付き商品管理',
            'CSV取込とCSV出力',
            '売上分析とジャンル別分析',
            'FreeプランとPremiumプラン',
        ],
    ];
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => $canonical,
        'logo' => $image,
    ];
    $websiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => $canonical,
        'description' => $description,
    ];
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'FURUGIは何を管理できますか？',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => '古着販売の商品登録、画像管理、在庫ステータス、SOLD管理、売上集計、CSV取込、CSV出力、ジャンル別分析を管理できます。',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'FreeプランとPremiumプランの違いは何ですか？',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Freeプランは商品登録件数に制限があります。Premiumプランは登録数の上限を広げ、CSV機能、売上分析、ジャンル別分析などを利用できます。',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'メルカリやヤフオクなどの販売管理に使えますか？',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => '複数の販売先に出品する古着販売者が、商品名、仕入れ、販売価格、状態、画像、SOLD状況をまとめて管理する用途を想定しています。',
                ],
            ],
        ],
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
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">
    <x-pwa-head />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">@json($applicationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    <script type="application/ld+json">@json($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    <script type="application/ld+json">@json($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    <script type="application/ld+json">@json($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <header class="border-b border-white/10 bg-slate-950/95">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="FURUGI トップ">
                <img src="{{ asset('images/logo.png') }}" alt="FURUGI" class="h-10 w-auto">
                <span class="text-sm font-black tracking-[0.24em] text-cyan-200">FURUGI</span>
            </a>
            <nav class="flex flex-wrap justify-end gap-x-4 gap-y-2 text-sm font-bold">
                <a href="{{ route('marketing.features') }}" class="text-slate-200 hover:text-white">機能</a>
                <a href="{{ route('marketing.use-cases') }}" class="text-slate-200 hover:text-white">活用例</a>
                <a href="{{ route('marketing.pricing') }}" class="text-slate-200 hover:text-white">料金</a>
                <a href="{{ route('login') }}" class="rounded-md border border-white/20 px-4 py-2 text-white hover:bg-white/10">ログイン</a>
                <a href="{{ route('register') }}" class="rounded-md bg-cyan-300 px-4 py-2 text-slate-950 hover:bg-cyan-200">無料で始める</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <img src="{{ asset('images/bg.png') }}" alt="古着販売の在庫管理イメージ" class="absolute inset-0 h-full w-full object-cover opacity-35">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-950/82 to-slate-950"></div>
            <div class="relative mx-auto grid min-h-[calc(100vh-73px)] max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.08fr_0.92fr] lg:px-8">
                <div>
                    <p class="text-sm font-black tracking-[0.28em] text-cyan-200">USED CLOTHING INVENTORY</p>
                    <h1 class="mt-5 max-w-4xl text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">古着販売の在庫管理、SOLD管理、売上分析をひとつに。</h1>
                    <p class="mt-6 max-w-2xl text-base font-semibold leading-8 text-slate-200 sm:text-lg">FURUGIは、メルカリ・ヤフオク・ラクマ・フリマ販売など複数の販売先で増えがちな商品情報を整理する古着販売向け管理システムです。画像付き商品登録、仕入れ価格、販売価格、在庫ステータス、CSV、ジャンル別分析まで日々の運用に必要な機能をまとめます。</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="rounded-md bg-cyan-300 px-6 py-4 text-center text-sm font-black text-slate-950 shadow-lg shadow-cyan-950/30 hover:bg-cyan-200">無料で始める</a>
                        <form method="POST" action="{{ route('login.demo') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md border border-white/25 px-6 py-4 text-sm font-black text-white hover:bg-white/10 sm:w-auto">デモを見る</button>
                        </form>
                    </div>
                </div>
                <section class="rounded-lg border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black text-white">古着販売で散らかりやすい情報を整理</h2>
                    <div class="mt-5 grid gap-3">
                        <div class="rounded-md bg-slate-950/55 p-4">
                            <h3 class="font-black text-cyan-200">商品登録と画像管理</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-200">タイトル、仕入れ、価格、ジャンル、状態、画像をまとめて登録。スマホPWAではカメラ撮影から商品画像を掲載できます。</p>
                        </div>
                        <div class="rounded-md bg-slate-950/55 p-4">
                            <h3 class="font-black text-cyan-200">SOLD管理と売上分析</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-200">販売済みの商品を記録し、売上、利益、ジャンル別の傾向を確認できます。</p>
                        </div>
                        <div class="rounded-md bg-slate-950/55 p-4">
                            <h3 class="font-black text-cyan-200">CSV取込・出力</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-200">まとめて登録したい商品や売上データをCSVで扱えるため、日々の作業時間を短縮できます。</p>
                        </div>
                    </div>
                </section>
            </div>
        </section>

        <section class="bg-white py-16 text-slate-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-black tracking-[0.24em] text-cyan-700">SEO KEYWORDS</p>
                    <h2 class="mt-3 text-3xl font-black">古着管理、フリマ在庫管理、売上管理を探している方へ。</h2>
                    <p class="mt-4 font-semibold leading-7 text-slate-600">出品数が増えるほど、商品名、価格、画像、販売状況、利益計算は手作業では追いにくくなります。FURUGIは小さく始めて、販売量が増えたらPremiumで運用を広げられる設計です。</p>
                </div>
                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <a href="{{ route('marketing.features') }}" class="rounded-lg border border-slate-200 p-5 hover:border-cyan-500">
                        <h3 class="text-lg font-black">機能を見る</h3>
                        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">商品登録、画像管理、SOLD管理、CSV、分析機能を確認できます。</p>
                    </a>
                    <a href="{{ route('marketing.use-cases') }}" class="rounded-lg border border-slate-200 p-5 hover:border-cyan-500">
                        <h3 class="text-lg font-black">活用例を見る</h3>
                        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">メルカリ、ヤフオク、フリマ販売での使い方を確認できます。</p>
                    </a>
                    <a href="{{ route('marketing.pricing') }}" class="rounded-lg border border-slate-200 p-5 hover:border-cyan-500">
                        <h3 class="text-lg font-black">料金を見る</h3>
                        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">FreeとPremiumの違い、月額{{ number_format($premiumAmount) }}円の内容を確認できます。</p>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 bg-slate-950 py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm font-semibold text-slate-300 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-x-5 gap-y-2">
                <a href="{{ route('marketing.features') }}" class="hover:text-white">機能</a>
                <a href="{{ route('marketing.use-cases') }}" class="hover:text-white">活用例</a>
                <a href="{{ route('marketing.pricing') }}" class="hover:text-white">料金</a>
                <a href="{{ route('legal.terms') }}" class="hover:text-white">利用規約</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-white">プライバシーポリシー</a>
                <a href="{{ route('legal.commercial') }}" class="hover:text-white">特定商取引法に基づく表記</a>
                <a href="{{ route('legal.faq') }}" class="hover:text-white">よくある質問</a>
                <a href="{{ route('legal.contact') }}" class="hover:text-white">お問い合わせ</a>
            </div>
            <p>&copy; 2026 FURUGI. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
