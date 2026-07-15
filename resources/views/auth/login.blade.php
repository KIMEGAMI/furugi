<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,follow">
    <title>ログイン | FURUGI</title>

    <x-pwa-head />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">
    <main class="flex min-h-screen items-center justify-center p-4">
        <div class="grid min-h-[720px] w-full max-w-7xl grid-cols-1 overflow-hidden rounded-lg bg-white shadow-2xl lg:grid-cols-2">
            <section class="relative hidden bg-blue-900 lg:block">
                <img
                    src="{{ asset('images/auth/login-hero.png') }}"
                    alt="FURUGI 古着販売向け在庫管理システム"
                    class="absolute inset-0 h-full w-full object-cover"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-blue-950/35 to-transparent"></div>
                <div class="absolute left-8 top-8 text-white">
                    <div class="text-sm font-bold tracking-[0.3em]">CLOTHING MANAGEMENT SYSTEM</div>
                    <div class="mt-4 h-1 w-24 rounded-full bg-white"></div>
                </div>
            </section>

            <section class="flex items-center justify-center px-6 py-10 lg:px-16">
                <div class="w-full max-w-xl">
                    <div class="mb-10 text-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="FURUGI" class="mx-auto mb-6 h-auto w-56">
                        </a>

                        <h1 class="text-4xl font-black tracking-tight text-blue-800">古着管理システム</h1>
                        <p class="mt-3 text-sm font-bold tracking-[0.25em] text-cyan-700">CLOTHING MANAGEMENT SYSTEM</p>
                        <p class="mt-6 font-semibold text-slate-600">在庫・入出庫・販売を、もっとスマートに。</p>
                        <div class="mx-auto mt-6 h-1 w-16 rounded-full bg-blue-600"></div>
                    </div>

                    <button
                        type="button"
                        data-pwa-install
                        hidden
                        disabled
                        class="mb-6 w-full rounded-md border border-cyan-300 bg-cyan-50 px-5 py-4 text-center text-sm font-black text-cyan-900 shadow-sm transition hover:bg-cyan-100 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        FURUGIをアプリとして追加
                    </button>

                    <section class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-black text-slate-900">Free</h2>
                                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-black text-slate-700">0円</span>
                            </div>
                            <p class="mt-2 text-xs font-bold leading-5 text-slate-600">
                                まず試したい方向け。商品登録30件まで、基本の商品管理とSOLD管理が使えます。
                            </p>
                        </div>

                        <div class="rounded-lg border-2 border-blue-500 bg-blue-50 p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-black text-slate-900">Premium</h2>
                                <span class="rounded-full bg-blue-700 px-3 py-1 text-xs font-black text-white">月480円</span>
                            </div>
                            <p class="mt-2 text-xs font-bold leading-5 text-slate-700">
                                商品登録数の上限拡張、CSV取込・出力、売上分析、ジャンル別分析が使えます。
                            </p>
                        </div>
                    </section>

                    @if (session('status'))
                        <div class="mb-6 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="rounded-lg border border-slate-200 bg-white p-8 shadow-xl">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700">メールアドレス</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="メールアドレスを入力してください"
                                class="mt-3 block w-full rounded-md border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="password" class="block text-sm font-bold text-slate-700">パスワード</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="パスワードを入力してください"
                                class="mt-3 block w-full rounded-md border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                ログイン状態を保存する
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-bold text-blue-700 hover:text-blue-900">
                                    パスワードをお忘れですか？
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="mt-8 w-full rounded-md bg-blue-700 px-6 py-4 text-center font-bold text-white shadow-lg transition hover:bg-blue-800">
                            ログイン
                        </button>

                        <div class="mt-6 text-center">
                            <a href="{{ route('register') }}" class="text-sm font-bold text-slate-900 hover:text-blue-700">
                                アカウントを新規作成する
                            </a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('login.demo') }}" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full rounded-md border border-blue-200 bg-blue-50 px-6 py-4 text-center font-bold text-blue-800 shadow-sm transition hover:border-blue-300 hover:bg-blue-100">
                            デモを見る
                        </button>
                    </form>

                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white px-2 text-slate-600">または</span>
                            </div>
                        </div>

                        <a href="{{ route('google.redirect') }}" class="mt-4 flex w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-gray-50">
                            Googleでログイン
                        </a>
                    </div>

                    <p class="mt-8 text-center text-xs text-slate-500">&copy; 2026 FURUGI All rights reserved.</p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
