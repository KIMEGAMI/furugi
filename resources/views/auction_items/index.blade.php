<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-400">
                    出品一覧
                </h2>

                <p class="mt-1 text-sm text-cyan-200">
                    登録したフリマアプリ・オークション出品データを一覧で管理します。
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

            @if(session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                    <p class="font-bold text-red-700">
                        {{ session('error') }}
                    </p>
                </div>
            @endif

            @if($errors->has('csv_file'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                    <p class="font-bold text-red-700">
                        {{ $errors->first('csv_file') }}
                    </p>
                </div>
            @endif

            <div class="mb-6 rounded-3xl bg-white p-5 shadow border border-slate-200">
                <form
                    action="{{ route('auction-items.import') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end"
                >
                    @csrf

                    <div class="lg:col-span-8">
                        <label for="csv_file" class="block text-sm font-black text-slate-700">
                            CSVインポート
                        </label>

                        <input
                            id="csv_file"
                            type="file"
                            name="csv_file"
                            accept=".csv,.txt"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600"
                        >

                        <p class="mt-2 text-xs font-semibold text-cyan-200">
                            ヘッダー例：management_id,title,comment,platform,大ジャンル,小ジャンル,purchase_price,sold_price,shipping_fee,sales_fee_rate,status
                        </p>
                    </div>

                    <div class="lg:col-span-4">
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-800 px-5 py-3 text-sm font-bold text-white shadow hover:bg-slate-900 transition"
                        >
                            CSVを取り込む
                        </button>
                    </div>
                </form>
            </div>

            <div class="mb-6 rounded-3xl bg-white p-4 shadow border border-slate-200">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <a
                        href="{{ route('auction-items.index', array_filter(['platform' => $platform, 'keyword' => $keyword, 'parent_category_id' => $parentCategoryId, 'category_id' => $categoryId])) }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ empty($status) ? 'bg-blue-700 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        ALL
                    </a>

                    <a
                        href="{{ route('auction-items.index', array_filter(['status' => 'selling', 'platform' => $platform, 'keyword' => $keyword, 'parent_category_id' => $parentCategoryId, 'category_id' => $categoryId])) }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ $status === 'selling' ? 'bg-blue-700 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        出品中
                    </a>

                    <a
                        href="{{ route('auction-items.index', array_filter(['status' => 'sold', 'platform' => $platform, 'keyword' => $keyword, 'parent_category_id' => $parentCategoryId, 'category_id' => $categoryId])) }}"
                        class="rounded-2xl px-4 py-3 text-center text-sm font-black transition {{ $status === 'sold' ? 'bg-red-600 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        SOLD
                    </a>
                </div>
            </div>

            <div class="mb-8 rounded-3xl bg-white p-6 shadow border border-slate-200">
                <form method="GET" action="{{ route('auction-items.index') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-3">
                        <label for="platform" class="block text-sm font-black text-slate-700">
                            出品先
                        </label>

                        <select
                            id="platform"
                            name="platform"
                            class="mt-2 w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">すべて</option>
                            <option value="ヤフオク" @selected($platform === 'ヤフオク')>ヤフオク</option>
                            <option value="メルカリ" @selected($platform === 'メルカリ')>メルカリ</option>
                            <option value="ラクマ" @selected($platform === 'ラクマ')>ラクマ</option>
                            <option value="PayPayフリマ" @selected($platform === 'PayPayフリマ')>PayPayフリマ</option>
                            <option value="その他" @selected($platform === 'その他')>その他</option>
                        </select>
                    </div>

                    <div class="lg:col-span-6">
                        @include('auction_items.partials.category-selects', [
                            'parentCategories' => $parentCategories,
                            'parentSelectId' => 'filter_parent_category_id',
                            'categorySelectId' => 'filter_category_id',
                            'parentPlaceholder' => 'すべて',
                            'categoryPlaceholder' => 'すべて',
                            'selectedParentId' => $parentCategoryId,
                            'selectedCategoryId' => $categoryId,
                        ])
                    </div>

                    <div class="lg:col-span-6">
                        <label for="keyword" class="block text-sm font-black text-slate-700">
                            キーワード
                        </label>

                        <input
                            id="keyword"
                            type="text"
                            name="keyword"
                            value="{{ $keyword }}"
                            placeholder="管理ID・タイトル・コメントで検索"
                            class="mt-2 w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <input type="hidden" name="status" value="{{ $status }}">

                    <div class="flex items-end gap-3 lg:col-span-3">
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow hover:bg-blue-800 transition"
                        >
                            検索する
                        </button>

                        <a
                            href="{{ route('auction-items.index') }}"
                            class="w-full rounded-xl bg-slate-200 px-5 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-300 transition"
                        >
                            解除
                        </a>
                    </div>
                </form>
            </div>

            @if($auctionItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($auctionItems as $item)
                        @php
                            $displayImagePath = $item->status === 'sold' && $item->sold_image_path
                                ? $item->sold_image_path
                                : $item->image_path;

                            $platformName = $item->platform ?? 'その他';

                            $platformLabelStyle = match($platformName) {
                                'ヤフオク' => 'background:#facc15;color:#111827;',
                                'メルカリ' => 'background:#dc2626;color:#ffffff;',
                                'ラクマ' => 'background:#ec4899;color:#ffffff;',
                                'PayPayフリマ' => 'background:#2563eb;color:#ffffff;',
                                default => 'background:#475569;color:#ffffff;',
                            };

                            $cardStyle = match($platformName) {
                                'ヤフオク' => 'background:#fef9c3;border-color:#facc15;',
                                'メルカリ' => 'background:#fee2e2;border-color:#dc2626;',
                                'ラクマ' => 'background:#fce7f3;border-color:#ec4899;',
                                'PayPayフリマ' => 'background:#dbeafe;border-color:#2563eb;',
                                default => 'background:#f1f5f9;border-color:#64748b;',
                            };

                            $purchasePrice = (int) ($item->purchase_price ?? 0);
                            $soldPrice = (int) ($item->sold_price ?? 0);
                            $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
                            $salesFee = (int) ($item->sales_fee ?? round($soldPrice * ($salesFeeRate / 100)));
                            $shippingFee = (int) ($item->shipping_fee ?? 0);
                            $profit = (int) ($item->profit ?? ($soldPrice - $purchasePrice - $salesFee - $shippingFee));
                            $categoryLabel = $item->category
                                ? (($item->category->parent?->name ? $item->category->parent->name.' / ' : '').$item->category->name)
                                : '未設定';
                        @endphp

                        <article
                            class="rounded-3xl shadow border-4 overflow-hidden"
                            style="{{ $cardStyle }}"
                        >
                            <div class="relative bg-slate-200">
                                @if($displayImagePath)
                                    <img
                                        src="{{ asset('storage/' . $displayImagePath) }}"
                                        alt="{{ $item->title }}"
                                        class="w-full h-64 object-cover"
                                    >
                                @else
                                    <div class="w-full h-64 flex items-center justify-center bg-slate-200 text-slate-700 font-bold">
                                        NO IMAGE
                                    </div>
                                @endif

                                @if($item->status === 'sold' && !$item->sold_image_path)
                                    <div class="absolute inset-0 flex items-center justify-center bg-red-600/30">
                                        <span class="-rotate-12 rounded-2xl border-4 border-white px-8 py-3 text-5xl font-black tracking-widest text-white shadow">
                                            SOLD
                                        </span>
                                    </div>
                                @endif

                                <div class="absolute left-4 right-4 top-4 flex flex-wrap gap-2">
                                    @if($item->status === 'sold')
                                        <span class="rounded-full bg-red-600 px-4 py-2 text-xs font-black text-white shadow">
                                            SOLD
                                        </span>
                                    @else
                                        <span class="rounded-full bg-blue-700 px-4 py-2 text-xs font-black text-white shadow">
                                            出品中
                                        </span>
                                    @endif

                                    <span
                                        class="rounded-full px-4 py-2 text-xs font-black shadow"
                                        style="{{ $platformLabelStyle }}"
                                    >
                                        {{ $platformName }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-black tracking-widest text-slate-800">
                                        {{ $item->management_id }}
                                    </p>

                                    <p class="text-sm font-bold text-slate-700">
                                        仕入れ ¥{{ number_format($purchasePrice) }}
                                    </p>
                                </div>

 <h3 style="font-size:20px;font-weight:900;color:#000000 !important;">
    {{ $item->title }}
</h3>

                                <p class="mt-2 text-xs font-black text-slate-700">
                                    ジャンル: {{ $categoryLabel }}
                                </p>

                                <p class="mt-3 text-sm leading-6 text-slate-700 line-clamp-3">
                                    {{ $item->comment ?: 'コメントはありません。' }}
                                </p>

                                <div class="mt-5 rounded-2xl bg-white/85 border border-white p-4">
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <p class="text-xs font-black text-slate-600">売値</p>
                                            <p class="mt-1 font-black text-slate-800">
                                                ¥{{ number_format($soldPrice) }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-black text-slate-600">販売手数料</p>
                                            <p class="mt-1 font-black text-slate-800">
                                                ¥{{ number_format($salesFee) }}
                                                <span class="text-xs text-slate-600">
                                                    / {{ rtrim(rtrim(number_format($salesFeeRate, 2), '0'), '.') }}%
                                                </span>
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-black text-slate-600">送料</p>
                                            <p class="mt-1 font-black text-slate-800">
                                                ¥{{ number_format($shippingFee) }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-black text-slate-600">実利益</p>
                                            <p class="mt-1 font-black {{ $profit < 0 ? 'text-red-700' : 'text-green-700' }}">
                                                ¥{{ number_format($profit) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <a
                                        href="{{ route('auction-items.show', $item) }}"
                                        class="rounded-xl bg-white px-4 py-3 text-center text-sm font-bold text-slate-700 shadow hover:bg-slate-50 transition"
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
                                @else
                                    <form
                                        action="{{ route('auction-items.selling', $item) }}"
                                        method="POST"
                                        class="mt-3"
                                        onsubmit="return confirm('この商品を出品中に戻しますか？')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="w-full rounded-xl bg-blue-700 px-4 py-3 text-center text-sm font-bold text-white hover:bg-blue-800 transition"
                                        >
                                            出品中に戻す
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

                    <p class="mt-3 text-cyan-200 font-semibold">
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
