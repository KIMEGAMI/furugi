@props([
    'title',
    'eyebrow' => 'INFORMATION',
    'description' => null,
    'schema' => [],
])

@php
    $siteName = config('seo.site_name', config('app.name', 'FURUPRO'));
    $pageTitle = $title.' | '.$siteName;
    $metaDescription = $description ?? $siteName.'の'.$title.'ページです。サービス内容、個人情報の取り扱い、よくある質問を確認できます。';
    $canonical = url()->current();
    $image = asset(ltrim(config('seo.image', '/images/furugi-manager-hero.png'), '/'));
    $webPage = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $pageTitle,
        'description' => $metaDescription,
        'url' => $canonical,
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => route('home'),
        ],
        'inLanguage' => 'ja',
        'dateModified' => config('seo.updated_at'),
    ];
    $schemas = array_filter(array_merge([$webPage], is_array($schema) ? $schema : []));
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="article">
    <meta property="og:locale" content="{{ config('seo.locale', 'ja_JP') }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:card" content="{{ config('seo.twitter_card', 'summary_large_image') }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $image }}">
    <x-pwa-head />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @foreach ($schemas as $schemaItem)
        <script type="application/ld+json">@json($schemaItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    @endforeach
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-black text-slate-950" aria-label="FURUPRO トップページ">
                <span>FURUPRO</span>
            </a>
            <nav class="flex flex-wrap justify-end gap-x-4 gap-y-2 text-sm font-bold text-slate-700" aria-label="公開ページ">
                <a href="{{ route('home') }}" class="hover:text-slate-950">トップ</a>
                <a href="{{ route('marketing.features') }}" class="hover:text-slate-950">機能</a>
                <a href="{{ route('marketing.pricing') }}" class="hover:text-slate-950">料金</a>
                <a href="{{ route('legal.faq') }}" class="hover:text-slate-950">FAQ</a>
                <a href="{{ route('legal.contact') }}" class="hover:text-slate-950">お問い合わせ</a>
                <a href="{{ route('login') }}" class="hover:text-slate-950">ログイン</a>
            </nav>
        </div>
    </header>
    <main class="py-10">
        <article class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm md:p-10">
                <div class="border-b border-slate-200 pb-6">
                    <p class="text-sm font-black tracking-[0.2em] text-cyan-700">{{ $eyebrow }}</p>
                    <h1 class="mt-3 text-3xl font-black leading-tight text-slate-950 md:text-4xl">{{ $title }}</h1>
                    <p class="mt-4 text-sm font-bold text-slate-500">最終更新日: 2026年8月9日</p>
                </div>
                <div class="legal-content mt-8">
                    {{ $slot }}
                </div>
            </div>
        </article>
    </main>
    <style>
        .legal-content { color: #334155; font-size: 1rem; line-height: 1.95; }
        .legal-content h2 { margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; color: #0f172a; font-size: 1.35rem; font-weight: 900; line-height: 1.45; }
        .legal-content h2:first-child { margin-top: 0; padding-top: 0; border-top: 0; }
        .legal-content h3 { margin-top: 1.75rem; color: #0f172a; font-size: 1.05rem; font-weight: 900; line-height: 1.6; }
        .legal-content p { margin-top: 0.9rem; font-weight: 600; }
        .legal-content ul, .legal-content ol { margin-top: 1rem; padding-left: 1.5rem; }
        .legal-content li { margin-top: 0.7rem; font-weight: 600; }
        .legal-content form, .legal-content .not-prose { line-height: 1.5; }
    </style>
</body>
</html>
