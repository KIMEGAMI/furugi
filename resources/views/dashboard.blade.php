<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-900">
                    HOME
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    ヤフオク出品の登録・販売状況・売上をここから管理します。
                </p>
            </div>

            <a
                href="{{ route('auction-items.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow hover:bg-blue-800 transition"
            >
                ＋ 出品を登録する
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <a
                    href="{{ route('auction-items.index') }}"
                    class="bg-white rounded-2xl shadow p-6 border border-slate-200 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <p class="text-sm font-bold text-slate-500">出品中</p>
                    <p class="mt-3 text-3xl font-black text-blue-700">
                        {{ number_format($sellingCount) }}
                    </p>
                </a>

                <a
                    href="{{ route('auction-items.index') }}"
                    class="bg-white rounded-2xl shadow p-6 border border-slate-200 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <p class="text-sm font-bold text-slate-500">売却済み</p>
                    <p class="mt-3 text-3xl font-black text-red-600">
                        {{ number_format($soldCount) }}
                    </p>
                </a>

                <a
                    href="{{ route('sales.index') }}"
                    class="bg-white rounded-2xl shadow p-6 border border-slate-200 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <p class="text-sm font-bold text-slate-500">累計売上</p>
                    <p class="mt-3 text-3xl font-black text-blue-700">
                        ¥{{ number_format($totalSales) }}
                    </p>
                </a>

                <a
                    href="{{ route('sales.index') }}"
                    class="bg-white rounded-2xl shadow p-6 border border-slate-200 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <p class="text-sm font-bold text-slate-500">累計利益</p>
                    <p class="mt-3 text-3xl font-black {{ $totalProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        ¥{{ number_format($totalProfit) }}
                    </p>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <a
                    href="{{ route('auction-items.create') }}"
                    class="group bg-white rounded-3xl shadow border border-slate-200 p-8 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <div class="w-14 h-14 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-2xl font-black">
                        ＋
                    </div>
                    <h3 class="mt-6 text-xl font-black text-slate-900">
                        出品を登録する
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        画像、管理ID、タイトル、コメント、仕入れ値、売値を登録します。
                    </p>
                    <p class="mt-6 text-sm font-bold text-blue-700 group-hover:text-blue-900">
                        登録画面へ →
                    </p>
                </a>

                <a
                    href="{{ route('auction-items.index') }}"
                    class="group bg-white rounded-3xl shadow border border-slate-200 p-8 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-black">
                        一
                    </div>
                    <h3 class="mt-6 text-xl font-black text-slate-900">
                        出品一覧を見る
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        出品中・売却済みの商品を一覧で確認し、編集・SOLD化できます。
                    </p>
                    <p class="mt-6 text-sm font-bold text-blue-700 group-hover:text-blue-900">
                        一覧画面へ →
                    </p>
                </a>

                <a
                    href="{{ route('sales.index') }}"
                    class="group bg-white rounded-3xl shadow border border-slate-200 p-8 hover:-translate-y-1 hover:shadow-xl transition"
                >
                    <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-2xl font-black">
                        ¥
                    </div>
                    <h3 class="mt-6 text-xl font-black text-slate-900">
                        売上管理
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        累計売上、月別売上、累計利益、月別利益を確認します。
                    </p>
                    <p class="mt-6 text-sm font-bold text-blue-700 group-hover:text-blue-900">
                        売上画面へ →
                    </p>
                </a>
            </div>

            <div class="mt-8 bg-white rounded-3xl shadow border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-200">
                    <h3 class="text-xl font-black text-slate-900">
                        最近の出品
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        直近で登録・更新された商品を確認できます。
                    </p>
                </div>

                @if($recentItems->count() > 0)
                    <div class="divide-y divide-slate-200">
                        @foreach($recentItems as $item)
                            <a
                                href="{{ route('auction-items.show', $item) }}"
                                class="flex flex-col gap-4 p-6 hover:bg-slate-50 transition sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="flex items-center gap-4">
                                    @if($item->image_path)
                                        <img
                                            src="{{ asset('storage/' . $item->image_path) }}"
                                            alt="{{ $item->title }}"
                                            class="h-16 w-16 rounded-2xl object-cover border border-slate-200"
                                        >
                                    @else
                                        <div class="h-16 w-16 rounded-2xl bg-slate-200 flex items-center justify-center text-xs font-black text-slate-500">
                                            NO IMAGE
                                        </div>
                                    @endif

                                    <div>
                                        <p class="text-xs font-black tracking-widest text-blue-700">
                                            {{ $item->management_id }}
                                        </p>
                                        <p class="mt-1 font-black text-slate-900">
                                            {{ $item->title }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($item->status === 'sold')
                                        <span class="rounded-full bg-red-600 px-4 py-2 text-xs font-black text-white">
                                            SOLD
                                        </span>
                                    @elseif($item->status === 'draft')
                                        <span class="rounded-full bg-slate-600 px-4 py-2 text-xs font-black text-white">
                                            下書き
                                        </span>
                                    @else
                                        <span class="rounded-full bg-blue-700 px-4 py-2 text-xs font-black text-white">
                                            出品中
                                        </span>
                                    @endif

                                    <span class="text-sm font-bold text-slate-500">
                                        詳細へ →
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-10 text-center">
                        <p class="font-bold text-slate-500">
                            まだ出品データがありません。
                        </p>

                        <a
                            href="{{ route('auction-items.create') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-xl bg-blue-700 px-6 py-4 text-sm font-bold text-white shadow hover:bg-blue-800 transition"
                        >
                            最初の出品を登録する
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-8 bg-white rounded-3xl shadow border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-200">
                    <h3 class="text-xl font-black text-slate-900">
                        機能メニュー
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        よく使う機能へすぐに移動できます。
                    </p>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a
                        href="{{ route('auction-items.create') }}"
                        class="rounded-2xl bg-blue-50 border border-blue-100 p-5 hover:bg-blue-100 transition"
                    >
                        <p class="font-black text-blue-800">出品登録</p>
                        <p class="mt-2 text-sm text-slate-500">新しい出品データを追加します。</p>
                    </a>

                    <a
                        href="{{ route('auction-items.index') }}"
                        class="rounded-2xl bg-blue-50 border border-blue-100 p-5 hover:bg-blue-100 transition"
                    >
                        <p class="font-black text-blue-800">出品一覧</p>
                        <p class="mt-2 text-sm text-slate-500">登録済みの商品を確認します。</p>
                    </a>

                    <a
                        href="{{ route('sales.index') }}"
                        class="rounded-2xl bg-blue-50 border border-blue-100 p-5 hover:bg-blue-100 transition"
                    >
                        <p class="font-black text-blue-800">売上管理</p>
                        <p class="mt-2 text-sm text-slate-500">売上と利益を確認します。</p>
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="rounded-2xl bg-blue-50 border border-blue-100 p-5 hover:bg-blue-100 transition"
                    >
                        <p class="font-black text-blue-800">プロフィール</p>
                        <p class="mt-2 text-sm text-slate-500">ユーザー情報を変更します。</p>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>