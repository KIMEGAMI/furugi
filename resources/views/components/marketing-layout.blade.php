@props([
    'title',
    'description',
    'canonical' => url()->current(),
    'schema' => [],
])

@php
    $siteName = config('seo.site_name', 'FURUGI MANAGER');
    $image = asset(ltrim(config('seo.image', '/images/furugi-manager-hero.png'), '/'));
    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => config('seo.organization.name'),
        'url' => config('seo.organization.url'),
        'logo' => asset(ltrim(config('seo.organization.logo'), '/')),
    ];
    $webPage = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $title,
        'description' => $description,
        'url' => $canonical,
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => route('home'),
        ],
        'inLanguage' => 'ja',
        'dateModified' => config('seo.updated_at'),
    ];
    $schemas = array_filter(array_merge([$organization, $webPage], is_array($schema) ? $schema : []));
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ config('seo.locale', 'ja_JP') }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:alt" content="{{ $siteName }} 古着管理システム">

    <meta name="twitter:card" content="{{ config('seo.twitter_card', 'summary_large_image') }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">

    <x-pwa-head />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @foreach ($schemas as $schemaItem)
        <script type="application/ld+json">@json($schemaItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    @endforeach
</head>
<body class="bg-white text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-black" aria-label="FURUGI MANAGER トップページ">
                <img src="{{ asset('images/logo.png') }}" alt="FURUGI MANAGER" class="h-10 w-auto">
                <span>FURUGI MANAGER</span>
            </a>
            <nav class="flex flex-wrap justify-end gap-x-4 gap-y-2 text-sm font-bold text-slate-700" aria-label="公開ページ">
                <a href="{{ route('marketing.features') }}" class="hover:text-slate-950">機能</a>
                <a href="{{ route('marketing.use-cases') }}" class="hover:text-slate-950">活用例</a>
                <a href="{{ route('marketing.pricing') }}" class="hover:text-slate-950">料金</a>
                <a href="{{ route('legal.faq') }}" class="hover:text-slate-950">FAQ</a>
                <a href="{{ route('login') }}" class="hover:text-slate-950">ログイン</a>
            </nav>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <footer class="border-t border-slate-200 bg-slate-50 py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm font-semibold text-slate-700 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap gap-x-5 gap-y-2" aria-label="フッター">
                <a href="{{ route('marketing.features') }}" class="hover:text-slate-950">機能</a>
                <a href="{{ route('marketing.use-cases') }}" class="hover:text-slate-950">活用例</a>
                <a href="{{ route('marketing.pricing') }}" class="hover:text-slate-950">料金</a>
                <a href="{{ route('legal.faq') }}" class="hover:text-slate-950">よくある質問</a>
                <a href="{{ route('legal.terms') }}" class="hover:text-slate-950">利用規約</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-slate-950">プライバシーポリシー</a>
                <a href="{{ route('legal.commercial') }}" class="hover:text-slate-950">特定商取引法に基づく表記</a>
                <a href="{{ route('legal.contact') }}" class="hover:text-slate-950">お問い合わせ</a>
            </nav>
            <p>&copy; 2026 FURUGI MANAGER. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
