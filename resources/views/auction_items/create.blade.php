<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-900">
                    出品登録
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    フリマ・オークション出品情報を登録します。
                </p>
            </div>

            <a
                href="{{ route('auction-items.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-300 transition"
            >
                一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                    <ul class="space-y-1 text-sm font-bold text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>・{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('auction-items.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="rounded-3xl bg-white p-6 shadow-xl"
            >
                @csrf

                <div class="grid grid-cols-1 gap-5">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>
                            <label class="block text-xs font-black tracking-wider text-slate-600">
                                管理ID
                            </label>

                            <input
                                type="text"
                                name="management_id"
                                value="{{ old('management_id') }}"
                                class="mt-2 h-11 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="FRG-0001"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-black tracking-wider text-slate-600">
                                出品先
                            </label>

                            <select
                                name="platform"
                                id="platform"
                                class="mt-2 h-11 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="ヤフオク" @selected(old('platform') === 'ヤフオク')>
                                    ヤフオク
                                </option>

                                <option value="メルカリ" @selected(old('platform') === 'メルカリ')>
                                    メルカリ
                                </option>

                                <option value="ラクマ" @selected(old('platform') === 'ラクマ')>
                                    ラクマ
                                </option>

                                <option value="PayPayフリマ" @selected(old('platform') === 'PayPayフリマ')>
                                    PayPayフリマ
                                </option>

                                <option value="その他" @selected(old('platform') === 'その他')>
                                    その他
                                </option>
                            </select>
                        </div>

                    </div>

                    <div>
                        <label class="block text-xs font-black tracking-wider text-slate-600">
                            商品タイトル
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="mt-2 h-11 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="90s ナイロンジャケット"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-black tracking-wider text-slate-600">
                            コメント
                        </label>

                        <textarea
                            name="comment"
                            rows="4"
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="サイズ感、状態、特徴など"
                        >{{ old('comment') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>
                            <label class="block text-xs font-black tracking-wider text-slate-600">
                                仕入れ値
                            </label>

                            <div class="relative mt-2">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-black text-slate-500">
                                    ¥
                                </span>

                                <input
                                    type="number"
                                    name="purchase_price"
                                    value="{{ old('purchase_price') }}"
                                    class="h-11 w-full rounded-xl border-slate-300 pl-8 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="3000"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black tracking-wider text-slate-600">
                                売値
                            </label>

                            <div class="relative mt-2">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-black text-slate-500">
                                    ¥
                                </span>

                                <input
                                    type="number"
                                    name="sold_price"
                                    value="{{ old('sold_price') }}"
                                    class="h-11 w-full rounded-xl border-slate-300 pl-8 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="8500"
                                >
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

                        <div>
                            <label class="block text-xs font-black tracking-wider text-slate-600">
                                販売手数料(%)
                            </label>

                            <input
                                type="number"
                                step="0.1"
                                name="sales_fee_rate"
                                id="sales_fee_rate"
                                value="{{ old('sales_fee_rate', 10) }}"
                                class="mt-2 h-11 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            <p class="mt-1 text-xs font-semibold text-slate-400">
                                出品先を選ぶと自動で反映されます
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-black tracking-wider text-slate-600">
                                送料
                            </label>

                            <div class="relative mt-2">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-black text-slate-500">
                                    ¥
                                </span>

                                <input
                                    type="number"
                                    name="shipping_fee"
                                    value="{{ old('shipping_fee', 750) }}"
                                    class="h-11 w-full rounded-xl border-slate-300 pl-8 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-100 p-4">
                            <p class="text-xs font-black tracking-wider text-slate-500">
                                利益計算式
                            </p>

                            <p class="mt-2 text-sm font-black text-slate-700">
                                売値 − 仕入 − 手数料 − 送料
                            </p>
                        </div>

                    </div>

                    <div>
                        <label class="block text-xs font-black tracking-wider text-slate-600">
                            商品画像
                        </label>

                        <input
                            type="file"
                            name="image"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-500"
                        >

                        <p class="mt-2 text-xs font-semibold text-slate-400">
                            JPG / PNG / WEBP 対応・最大10MB
                        </p>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-6 py-3 text-sm font-black text-white shadow hover:bg-blue-800 transition"
                        >
                            登録する
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        $(function () {
            const feeRates = {
                'ヤフオク': 10,
                'メルカリ': 10,
                'ラクマ': 10,
                'PayPayフリマ': 5,
                'その他': 0
            };

            $('#platform').on('change', function () {
                const selectedPlatform = $(this).val();

                if (Object.prototype.hasOwnProperty.call(feeRates, selectedPlatform)) {
                    $('#sales_fee_rate').val(feeRates[selectedPlatform]);
                }
            });
        });
    </script>
</x-app-layout>