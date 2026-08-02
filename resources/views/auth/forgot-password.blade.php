<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-black text-white">パスワード再設定</h1>
        <p class="mt-3 text-sm font-semibold leading-7 text-cyan-100">
            登録済みのメールアドレスを入力してください。パスワードを再設定するためのURLをメールで送信します。
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="メールアドレス" />
            <x-text-input
                id="email"
                class="mt-2 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="登録済みのメールアドレス"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('login') }}" class="text-sm font-bold text-cyan-200 hover:text-white">
                ログイン画面へ戻る
            </a>

            <x-primary-button>
                再設定URLを送信
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
