<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-900">
                    出品詳細
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    登録したヤフオク出品データの詳細を確認します。
                </p>
            </div>

            <a
                href="{{ route('auction-items.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-700 border border-slate-200 shadow hover:bg-slate-50 transition"
            >
                一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-6 py-5">
                    <p class="font-bold text-blue-700">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <div class="relative bg-slate-200">
                        @if($auctionItem->image_path)
                            <img
                                src="{{ asset('storage/' . $auctionItem->image_path) }}"
                                alt="{{ $auctionItem->title }}"
                                class="w-full h-full min-h-[520px] object-cover"
                            >
                        @else
                            <div class="w-full min-h-[520px] flex items-center justify-center bg-slate-200 text-slate-500 font-bold">
                                NO IMAGE
                            </div>
                        @endif

                        @if($auctionItem->status === 'sold')
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                <span class="-rotate-12 rounded-3xl border-8 border-red-600 px-10 py-5 text-6xl font-black text-red-600 bg-white/80 shadow-2xl">
                                    SOLD
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-8 lg:p-10">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-black tracking-widest text-blue-700">
                                {{ $auctionItem->management_id }}
                            </p>

                            @if($auctionItem->status === 'sold')
                                <span class="rounded-full bg-red-600 px-4 py-2 text-xs font-black text-white shadow">
                                    SOLD
                                </span>
                            @elseif($auctionItem->status === 'draft')
                                <span class="rounded-full bg-slate-600 px-4 py-2 text-xs font-black text-white shadow">
                                    下書き
                                </span>
                            @else
                                <span class="rounded-full bg-blue-700 px-4 py-2 text-xs font-black text-white shadow">
                                    出品中
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-5 text-3xl font-black leading-tight text-slate-900">
                            {{ $auctionItem->title }}
                        </h3>

                        @if($auctionItem->status === 'sold' && $auctionItem->sold_at)
                            <p class="mt-4 text-sm font-bold text-red-600">
                                SOLD日時：{{ $auctionItem->sold_at->format('Y年m月d日 H:i') }}
                            </p>
                        @endif

                        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                                <p class="text-xs font-bold text-slate-500">
                                    仕入れ値
                                </p>
                                <p class="mt-2 text-2xl font-black text-blue-800">
                                    ¥{{ number_format($auctionItem->purchase_price) }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                                <p class="text-xs font-bold text-slate-500">
                                    売値
                                </p>
                                <p class="mt-2 text-2xl font-black text-slate-800">
                                    @if($auctionItem->sold_price > 0)
                                        ¥{{ number_format($auctionItem->sold_price) }}
                                    @else
                                        未入力
                                    @endif
                                </p>
                            </div>

                            @php
                                $profitClass = $auctionItem->profit >= 0 ? 'text-emerald-700' : 'text-red-700';
                                $profitBgClass = $auctionItem->profit >= 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100';
                            @endphp

                            <div class="rounded-2xl border p-5 {{ $profitBgClass }}">
                                <p class="text-xs font-bold text-slate-500">
                                    利益
                                </p>
                                <p class="mt-2 text-2xl font-black {{ $profitClass }}">
                                    @if($auctionItem->status === 'sold')
                                        ¥{{ number_format($auctionItem->profit) }}
                                    @else
                                        未確定
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="text-sm font-black text-slate-500">
                                コメント
                            </h4>

                            <div class="mt-3 rounded-2xl bg-slate-50 border border-slate-200 p-5">
                                <p class="whitespace-pre-wrap leading-8 text-slate-700">
                                    {{ $auctionItem->comment ?: 'コメントはありません。' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a
                                href="{{ route('auction-items.edit', $auctionItem) }}"
                                class="rounded-2xl bg-blue-700 px-6 py-4 text-center text-sm font-black text-white shadow hover:bg-blue-800 transition"
                            >
                                編集する
                            </a>

                            <a
                                href="{{ route('auction-items.index') }}"
                                class="rounded-2xl bg-white px-6 py-4 text-center text-sm font-black text-slate-700 border border-slate-200 hover:bg-slate-50 transition"
                            >
                                一覧へ戻る
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>