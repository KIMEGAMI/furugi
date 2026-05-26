<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録 | 古着管理システム</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="min-h-screen bg-slate-100">
    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-7xl min-h-[720px] bg-white rounded-[2rem] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

            <section class="relative hidden lg:block bg-blue-900">
                <img
                    src="{{ asset('images/auth/register-hero.png') }}"
                    alt="古着管理システム"
                    class="absolute inset-0 w-full h-full object-cover"
                >

                <div class="absolute inset-0 bg-gradient-to-r from-blue-950/40 to-transparent"></div>

                <div class="absolute top-8 left-8 text-white">
                    <div class="text-sm tracking-[0.3em] font-bold">
                        CLOTHING MANAGEMENT SYSTEM
                    </div>
                    <div class="mt-4 h-1 w-24 bg-white rounded-full"></div>
                </div>

                <div class="absolute bottom-10 left-8 right-8 text-white">
                    <h2 class="text-4xl font-black leading-tight">
                        出品管理を、<br>
                        もっとスマートに。
                    </h2>

                    <p class="mt-5 text-sm leading-7 text-blue-50">
                        画像・管理ID・タイトル・コメント・仕入れ値・売値・利益をまとめて管理できます。
                    </p>
                </div>
            </section>

            <section class="flex items-center justify-center px-6 py-10 lg:px-16">
                <div class="w-full max-w-xl">
                    <div class="text-center mb-8">
                       <img
    src="{{ asset('images/logo.png') }}"
    alt="古着管理システム"
    class="w-56 h-auto mx-auto mb-6"
>

                        <h1 class="text-4xl font-black text-blue-800 tracking-tight">
                            アカウント作成
                        </h1>

                        <p class="mt-3 text-sm tracking-[0.25em] text-slate-400 font-bold">
                            CREATE ACCOUNT
                        </p>

                        <p class="mt-6 text-slate-500 font-semibold">
                            古着管理システムをはじめましょう。
                        </p>

                        <div class="mx-auto mt-6 h-1 w-16 bg-blue-600 rounded-full"></div>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="bg-white border border-slate-200 rounded-3xl shadow-xl p-8">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700">
                                ユーザー名
                            </label>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="ユーザー名を入力してください"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="email" class="block text-sm font-bold text-slate-700">
                                メールアドレス
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="メールアドレスを入力してください"
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
                                autocomplete="new-password"
                                placeholder="パスワードを入力してください"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            <div class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div id="password-strength-bar" class="h-full w-0 bg-blue-600 transition-all duration-300"></div>
                            </div>

                            <p id="password-strength-text" class="mt-2 text-xs font-bold text-slate-400">
                                パスワード強度：未入力
                            </p>

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700">
                                パスワード確認
                            </label>

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="もう一度パスワードを入力してください"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            <p id="password-match-text" class="mt-2 text-xs font-bold text-slate-400">
                                パスワード確認を入力してください
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="mt-8 w-full rounded-xl bg-blue-700 px-6 py-4 text-center text-white font-bold shadow-lg hover:bg-blue-800 transition"
                        >
                            新規登録
                        </button>

                        <div class="mt-6 text-center">
                            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-500 hover:text-blue-700">
                                すでにアカウントをお持ちの方はこちら
                            </a>
                        </div>
                    </form>

                    <p class="mt-8 text-center text-xs text-slate-400">
                        © 2026 古着管理システム All rights reserved.
                    </p>
                </div>
            </section>
        </div>
    </main>

    <script>
        $(function () {
            const $password = $('#password')
            const $passwordConfirmation = $('#password_confirmation')
            const $strengthBar = $('#password-strength-bar')
            const $strengthText = $('#password-strength-text')
            const $matchText = $('#password-match-text')

            function updatePasswordStrength() {
                const value = $password.val()
                let score = 0

                if (value.length >= 8) {
                    score += 1
                }

                if (/[A-Z]/.test(value)) {
                    score += 1
                }

                if (/[0-9]/.test(value)) {
                    score += 1
                }

                if (/[^A-Za-z0-9]/.test(value)) {
                    score += 1
                }

                if (!value) {
                    $strengthBar.css('width', '0%')
                    $strengthText.text('パスワード強度：未入力').removeClass().addClass('mt-2 text-xs font-bold text-slate-400')
                    return
                }

                if (score <= 1) {
                    $strengthBar.css('width', '25%')
                    $strengthText.text('パスワード強度：弱い').removeClass().addClass('mt-2 text-xs font-bold text-red-500')
                    return
                }

                if (score === 2 || score === 3) {
                    $strengthBar.css('width', '65%')
                    $strengthText.text('パスワード強度：普通').removeClass().addClass('mt-2 text-xs font-bold text-blue-500')
                    return
                }

                $strengthBar.css('width', '100%')
                $strengthText.text('パスワード強度：強い').removeClass().addClass('mt-2 text-xs font-bold text-blue-700')
            }

            function updatePasswordMatch() {
                const password = $password.val()
                const confirmation = $passwordConfirmation.val()

                if (!confirmation) {
                    $matchText.text('パスワード確認を入力してください').removeClass().addClass('mt-2 text-xs font-bold text-slate-400')
                    return
                }

                if (password === confirmation) {
                    $matchText.text('パスワードが一致しています').removeClass().addClass('mt-2 text-xs font-bold text-blue-700')
                    return
                }

                $matchText.text('パスワードが一致していません').removeClass().addClass('mt-2 text-xs font-bold text-red-500')
            }

            $password.on('input', function () {
                updatePasswordStrength()
                updatePasswordMatch()
            })

            $passwordConfirmation.on('input', function () {
                updatePasswordMatch()
            })
        })
    </script>
</body>
</html>