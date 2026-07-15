<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-3xl bg-white p-6 shadow-xl md:p-8">
                <p class="text-sm font-black text-blue-700">ADMIN</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">メンテナンスモード</h1>
                <p class="mt-4 text-sm font-bold leading-7 text-slate-600">
                    有効にすると、一般ユーザーはログイン後もメンテナンス画面になります。管理者はログイン画面とこの管理画面を開けます。
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
        </div>
    </div>
</x-app-layout>
