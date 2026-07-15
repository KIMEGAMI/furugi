<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | FURUGI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="flex min-h-screen items-center justify-center bg-cover bg-center px-4 py-10" style="background-image: linear-gradient(rgba(2, 6, 23, 0.58), rgba(2, 6, 23, 0.82)), url('{{ asset('images/bg.png') }}');">
        <section class="w-full max-w-2xl rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-8 text-center shadow-2xl backdrop-blur-md">
            <img src="{{ asset('images/logo.png') }}" alt="FURUGI" class="mx-auto h-auto w-40">

            <p class="mt-8 text-sm font-black tracking-[0.25em] text-cyan-300">MAINTENANCE</p>
            <h1 class="mt-4 text-3xl font-black md:text-4xl">{{ $title }}</h1>
            <p class="mx-auto mt-5 max-w-xl text-sm font-semibold leading-7 text-slate-300">
                {{ $message }}
            </p>

            @if (($retryAfter ?? 0) > 0)
                <div class="mx-auto mt-6 inline-flex rounded-full bg-cyan-400/10 px-4 py-2 text-sm font-black text-cyan-200">
                    目安: 約{{ max(1, (int) ceil($retryAfter / 60)) }}分後に再度お試しください
                </div>
            @endif

            <div class="mt-8 rounded-2xl border border-white/10 bg-white/10 p-4 text-left">
                <p class="text-sm font-black text-white">ご利用中のお客様へ</p>
                <p class="mt-2 text-xs font-semibold leading-6 text-slate-300">
                    登録済みの商品データや画像は保持されます。作業完了後、通常どおりログインしてご利用いただけます。
                </p>
            </div>
        </section>
    </main>
</body>
</html>
