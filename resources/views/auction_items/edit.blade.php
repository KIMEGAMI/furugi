<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-900">
                    出品編集
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    登録済みのヤフオク出品データを編集します。
                </p>
            </div>

            <a
                href="{{ route('auction-items.show', $auctionItem) }}"
                class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-700 border border-slate-200 shadow hover:bg-slate-50 transition"
            >
                詳細へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-6 py-5">
                    <p class="font-bold text-amber-700">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-200 bg-gradient-to-r from-blue-700 to-blue-500">
                    <h3 class="text-2xl font-black text-white">
                        商品情報編集
                    </h3>

                    <p class="mt-2 text-sm text-blue-100">
                        管理ID・画像・タイトル・コメント・仕入れ値・売値を編集します。
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('auction-items.update', $auctionItem) }}"
                    enctype="multipart/form-data"
                    class="p-8 space-y-8"
                >
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-sm font-bold text-slate-700">
                            商品画像
                        </label>

                        <div class="mt-4">
                            <img
                                id="image-preview"
                                src="{{ $auctionItem->image_path ? asset('storage/' . $auctionItem->image_path) : 'https://placehold.co/800x500/e2e8f0/64748b?text=NO+IMAGE' }}"
                                class="w-full max-w-xl h-[320px] object-cover rounded-2xl border border-slate-200"
                            >
                        </div>

                        <input
                            type="file"
                            name="image"
                            id="image"
                            accept="image/*"
                            class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm"
                        >

                        <p class="mt-2 text-xs font-bold text-slate-400">
                            画像を選択した場合のみ差し替えます。
                        </p>

                        @error('image')
                            <p class="mt-2 text-sm font-bold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-bold text-slate-700">
                                管理ID
                            </label>

                            <input
                                type="text"
                                name="management_id"
                                value="{{ old('management_id', $auctionItem->management_id) }}"
                                placeholder="例：NK-001"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('management_id')
                                <p class="mt-2 text-sm font-bold text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">
                                仕入れ値
                            </label>

                            <input
                                type="number"
                                name="purchase_price"
                                value="{{ old('purchase_price', $auctionItem->purchase_price) }}"
                                placeholder="3000"
                                min="0"
                                class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('purchase_price')
                                <p class="mt-2 text-sm font-bold text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6">
                        <label class="block text-sm font-black text-amber-800">
                            売値
                        </label>

                        <input
                            type="number"
                            name="sold_price"
                            value="{{ old('sold_price', $auctionItem->sold_price) }}"
                            placeholder="例：5800"
                            min="0"
                            class="mt-3 block w-full rounded-xl border-amber-300 px-4 py-4 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                        >

                        <p class="mt-3 text-sm font-bold text-amber-700">
                            SOLDにする前に売値を入力してください。利益は「売値 − 仕入れ値」で自動計算します。
                        </p>

                        @error('sold_price')
                            <p class="mt-2 text-sm font-bold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">
                            タイトル
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $auctionItem->title) }}"
                            placeholder="商品タイトル"
                            class="mt-3 block w-full rounded-xl border-slate-300 px-4 py-4 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('title')
                            <p class="mt-2 text-sm font-bold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">
                            コメント
                        </label>

                        <textarea
                            name="comment"
                            rows="8"
                            placeholder="サイズ・カラー・状態など"
                            class="mt-3 block w-full rounded-2xl border-slate-300 px-4 py-4 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('comment', $auctionItem->comment) }}</textarea>

                        @error('comment')
                            <p class="mt-2 text-sm font-bold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a
                            href="{{ route('auction-items.show', $auctionItem) }}"
                            class="rounded-2xl bg-white px-8 py-4 text-center text-sm font-black text-slate-700 border border-slate-200 hover:bg-slate-50 transition"
                        >
                            キャンセル
                        </a>

                        <button
                            type="submit"
                            class="rounded-2xl bg-blue-700 px-8 py-4 text-sm font-black text-white shadow-lg hover:bg-blue-800 transition"
                        >
                            更新する
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(function () {
            $('#image').on('change', function (event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    $('#image-preview').attr('src', e.target.result);
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
</x-app-layout>