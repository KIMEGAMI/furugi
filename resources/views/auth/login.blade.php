<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン | 古着管理システム</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">
    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-7xl min-h-[720px] bg-white rounded-[2rem] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

            <section class="relative hidden lg:block bg-blue-900">
                <img
                    src="{{ asset('images/auth/login-hero.png') }}"
                    alt="古着管理システム"
                    class="absolute inset-0 w-full h-full object-cover"
                >

                <div class="absolute inset-0 bg-gradient-to-r from-blue-950/30 to-transparent"></div>

                <div class="absolute top-8 left-8 text-white">
                    <div class="text-sm tracking-[0.3em] font-bold">
                        CLOTHING MANAGEMENT SYSTEM
                    </div>
                    <div class="mt-4 h-1 w-24 bg-white rounded-full"></div>
                </div>
            </section>

            <section class="flex items-center justify-center px-6 py-10 lg:px-16">
                <div class="w-full max-w-xl">
                    <div class="text-center mb-10">
                        <img
    src="{{ asset('images/logo.png') }}"
    alt="古着管理システム"
    class="w-56 h-auto mx-auto mb-6"
>

                        <h1 class="text-4xl font-black text-blue-800 tracking-tight">
                            古着管理システム
                        </h1>

                        <p class="mt-3 text-sm tracking-[0.25em] text-slate-400 font-bold">
                            CLOTHING MANAGEMENT SYSTEM
                        </p>

                        <p class="mt-6 text-slate-500 font-semibold">
                            在庫・入出庫・販売を、もっとスマートに。
                        </p>

                        <div class="mx-auto mt-6 h-1 w-16 bg-blue-600 rounded-full"></div>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="bg-white border border-slate-200 rounded-3xl shadow-xl p-8">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700">
                                ユーザーID / メールアドレス
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="text"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="ID または メールアドレスを入力してください"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="password" class="block text-sm font-bold text-slate-700">
                                パスワード
                            </label>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="パスワードを入力してください"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                >
                                ログイン状態を保持する
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-bold text-blue-700 hover:text-blue-900">
                                    パスワードをお忘れですか？
                                </a>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="mt-8 w-full rounded-xl bg-blue-700 px-6 py-4 text-center text-white font-bold shadow-lg hover:bg-blue-800 transition"
                        >
                            ログイン
                        </button>

                        <div class="mt-8 rounded-2xl bg-blue-50 border border-blue-100 px-5 py-5">
                            <p class="text-blue-800 font-bold">
                                お試し用のログイン
                            </p>

                            <p class="mt-2 text-slate-700 font-semibold">
                                ID：user　PW：12345678　です
                            </p>
                        </div>

                        <div class="mt-6 text-center">
                            <a href="{{ route('register') }}" class="text-sm font-bold text-slate-500 hover:text-blue-700">
                                アカウントを新規作成する
                            </a>
                        </div>
                    </form>
<div class="mt-6">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="bg-white px-2 text-gray-500">または</span>
        </div>
    </div>

    <a
        href="{{ route('google.redirect') }}"
        class="mt-4 flex w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
    >
        Googleでログイン
    </a>
</div>

                    <p class="mt-8 text-center text-xs text-slate-400">
                        © 2026 古着管理システム All rights reserved.
                    </p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>