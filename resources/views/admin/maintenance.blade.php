<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-5 flex flex-wrap gap-3">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-black text-white hover:bg-slate-800">
                    ユーザー一覧
                </a>
                <a href="{{ route('notices.index') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800">
                    お知らせ一覧
                </a>
            </div>

            <section class="rounded-3xl bg-white p-6 shadow-xl md:p-8">
                <p class="text-sm font-black text-blue-700">ADMIN</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">メンテナンスモード</h1>
                <p class="mt-4 text-sm font-bold leading-7 text-slate-700">
                    有効にすると、一般ユーザーはメンテナンス画面に切り替わります。管理者はログイン画面と管理画面を開けます。
                </p>

                <div class="mt-6 rounded-2xl border {{ $enabled ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50' }} p-5">
                    <p class="text-sm font-black {{ $enabled ? 'text-red-800' : 'text-emerald-800' }}">
                        現在: {{ $enabled ? 'メンテナンス中' : '通常稼働中' }}
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <form method="POST" action="{{ route('admin.maintenance.update') }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="enabled" value="1">
                        <button type="submit" class="w-full rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white shadow hover:bg-red-700">
                            メンテナンスに切り替える
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.maintenance.update') }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="enabled" value="0">
                        <button type="submit" class="w-full rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow hover:bg-emerald-700">
                            通常稼働に戻す
                        </button>
                    </form>
                </div>
            </section>

            <section class="mt-6 rounded-3xl bg-white p-6 shadow-xl md:p-8">
                <p class="text-sm font-black text-blue-700">NOTICE</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900">お知らせを投稿</h2>
                <p class="mt-3 text-sm font-bold leading-7 text-slate-700">
                    投稿したお知らせは、ダッシュボードに最新5件まで表示されます。題名をクリックすると詳細ページで本文を確認できます。
                </p>

                <form method="POST" action="{{ route('admin.notices.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-black text-slate-900">題名</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="80" class="mt-2 w-full rounded-xl border-slate-300 bg-white text-slate-950 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        @error('title')
                            <p class="mt-2 text-sm font-bold text-black">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="body" class="block text-sm font-black text-slate-900">本文</label>
                        <textarea id="body" name="body" rows="6" maxlength="1000" class="mt-2 w-full rounded-xl border-slate-300 bg-white text-slate-950 shadow-sm focus:border-blue-600 focus:ring-blue-600">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-2 text-sm font-bold text-black">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white shadow hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        お知らせを投稿
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
