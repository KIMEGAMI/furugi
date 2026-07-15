@props([
    'title',
    'description',
    'canonical' => url()->current(),
])

@php
    $siteName = config('seo.site_name', 'FURUGI');
    $image = asset(ltrim(config('seo.image', '/images/logo.png'), '/'));
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:card" content="summary_large_image">
    <x-pwa-head />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-black">
                <img src="{{ asset('images/logo.png') }}" alt="FURUGI" class="h-10 w-auto">
                <span>FURUGI</span>
            </a>
            <nav class="flex flex-wrap justify-end gap-x-4 gap-y-2 text-sm font-bold text-slate-700">
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
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm font-semibold text-slate-600 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-x-5 gap-y-2">
                <a href="{{ route('legal.terms') }}" class="hover:text-slate-950">利用規約</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-slate-950">プライバシーポリシー</a>
                <a href="{{ route('legal.commercial') }}" class="hover:text-slate-950">特定商取引法に基づく表記</a>
                <a href="{{ route('legal.contact') }}" class="hover:text-slate-950">お問い合わせ</a>
            </div>
            <p>&copy; 2026 FURUGI. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
