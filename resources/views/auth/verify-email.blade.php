<x-guest-layout>
    <div class="mb-4 text-sm text-cyan-200">
        {{ __('ご登録ありがとうございます。メールに記載されたリンクをクリックして認証してください。') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('新しい認証リンクを送信しました。') }}
        </div>
    @endif

    @if ($errors->has('email'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
            {{ $errors->first('email') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('認証メールを再送信') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-cyan-200 hover:text-cyan-50 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('ログアウト') }}
            </button>
        </form>
    </div>
</x-guest-layout>
