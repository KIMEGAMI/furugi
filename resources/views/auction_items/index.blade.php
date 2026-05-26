<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-900">
                    出品一覧
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    登録したヤフオク出品データを一覧で管理します。
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

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-6 py-5">
                    <p class="font-bold text-blue-700">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <div class="mb-8 rounded-3xl bg-white p-4 shadow border border-slate-200">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    <a
                        href="{{ route('auction-items.index') }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ empty($status) ? 'bg-blue-700 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        ALL
                    </a>

                    <a
                        href="{{ route('auction-items.index', ['status' => 'selling']) }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ $status === 'selling' ? 'bg-blue-700 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        出品中
                    </a>

                    <a
                        href="{{ route('auction-items.index', ['status' => 'sold']) }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ $status === 'sold' ? 'bg-red-600 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        SOLD
                    </a>

                    <a
                        href="{{ route('auction-items.index', ['status' => 'draft']) }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ $status === 'draft' ? 'bg-slate-700 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        下書き
                    </a>
                </div>
            </div>

            @if($auctionItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($auctionItems as $item)
                        <article class="bg-white rounded-3xl shadow border border-slate-200 overflow-hidden">
                            <div class="relative bg-slate-200">
                                @if($item->image_path)
                                    <img
                                        src="{{ asset('storage/' . $item->image_path) }}"
                                        alt="{{ $item->title }}"
                                        class="w-full h-64 object-cover"
                                    >
                                @else
                                    <div class="w-full h-64 flex items-center justify-center bg-slate-200 text-slate-500 font-bold">
                                        NO IMAGE
                                    </div>
                                @endif

                                <div class="absolute top-4 left-4">
                                    @if($item->status === 'sold')
                                        <span class="rounded-full bg-red-600 px-4 py-2 text-xs font-black text-white shadow">
                                            SOLD
                                        </span>
                                    @elseif($item->status === 'draft')
                                        <span class="rounded-full bg-slate-600 px-4 py-2 text-xs font-black text-white shadow">
                                            下書き
                                        </span>
                                    @else
                                        <span class="rounded-full bg-blue-700 px-4 py-2 text-xs font-black text-white shadow">
                                            出品中
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-black tracking-widest text-blue-700">
                                        {{ $item->management_id }}
                                    </p>

                                    <p class="text-sm font-bold text-slate-500">
                                        仕入れ ¥{{ number_format($item->purchase_price) }}
                                    </p>
                                </div>

                                <h3 class="mt-3 text-xl font-black text-slate-900 line-clamp-2">
                                    {{ $item->title }}
                                </h3>

                                <p class="mt-3 text-sm leading-6 text-slate-500 line-clamp-3">
                                    {{ $item->comment ?: 'コメントはありません。' }}
                                </p>

                                @if($item->status === 'sold')
                                    <div class="mt-5 rounded-2xl bg-red-50 border border-red-100 p-4">
                                        <p class="text-sm font-black text-red-700">
                                            売値 ¥{{ number_format($item->sold_price) }}
                                        </p>
                                        <p class="mt-1 text-sm font-black text-red-700">
                                            利益 ¥{{ number_format($item->profit) }}
                                        </p>
                                    </div>
                                @else
                                    <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                        <p class="text-sm font-black text-slate-700">
                                            売値
                                            @if($item->sold_price > 0)
                                                ¥{{ number_format($item->sold_price) }}
                                            @else
                                                未入力
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <a
                                        href="{{ route('auction-items.show', $item) }}"
                                        class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50 transition"
                                    >
                                        詳細
                                    </a>

                                    <a
                                        href="{{ route('auction-items.edit', $item) }}"
                                        class="rounded-xl bg-blue-700 px-4 py-3 text-center text-sm font-bold text-white hover:bg-blue-800 transition"
                                    >
                                        編集
                                    </a>
                                </div>

                                @if($item->status !== 'sold')
                                    <form
                                        action="{{ route('auction-items.sold', $item) }}"
                                        method="POST"
                                        class="mt-3"
                                        onsubmit="return confirm('この商品をSOLDにしますか？')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="w-full rounded-xl bg-amber-500 px-4 py-3 text-center text-sm font-bold text-white hover:bg-amber-600 transition"
                                        >
                                            SOLDにする
                                        </button>
                                    </form>
                                @endif

                                <form
                                    action="{{ route('auction-items.destroy', $item) }}"
                                    method="POST"
                                    class="mt-3"
                                    onsubmit="return confirm('この商品を削除しますか？画像も削除されます。')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="w-full rounded-xl bg-red-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-red-700 transition"
                                    >
                                        削除
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $auctionItems->links() }}
                </div>
            @else
                <div class="bg-white rounded-3xl shadow border border-slate-200 p-12 text-center">
                    <div class="mx-auto w-20 h-20 rounded-3xl bg-blue-50 flex items-center justify-center text-4xl">
                        📦
                    </div>

                    <h3 class="mt-6 text-2xl font-black text-slate-900">
                        表示できる出品データがありません
                    </h3>

                    <p class="mt-3 text-slate-500 font-semibold">
                        条件を変更するか、新しい出品を登録してください。
                    </p>

                    <a
                        href="{{ route('auction-items.create') }}"
                        class="mt-8 inline-flex items-center justify-center rounded-xl bg-blue-700 px-6 py-4 text-sm font-bold text-white shadow hover:bg-blue-800 transition"
                    >
                        出品を登録する
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>