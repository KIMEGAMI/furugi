<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            HOME
        </h2>
    </x-slot>

    @php
        $displayTotalItems = ($sellingCount ?? 0) + ($soldCount ?? 0) + ($draftCount ?? 0);
        $displayActiveItems = $sellingCount ?? 0;
        $displaySoldItems = $soldCount ?? 0;
        $displayTotalSales = $totalSales ?? 0;
        $displayTotalProfit = $totalProfit ?? 0;
        $displayTotalSalesFee = $totalSalesFee ?? 0;
        $displayTotalShippingFee = $totalShippingFee ?? 0;
    @endphp

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-bold text-slate-500">総数</div>
                    <div class="mt-2 text-3xl font-black text-slate-900">
                        {{ number_format($displayTotalItems) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-bold text-slate-500">出品</div>
                    <div class="mt-2 text-3xl font-black text-blue-700">
                        {{ number_format($displayActiveItems) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-bold text-slate-500">売却</div>
                    <div class="mt-2 text-3xl font-black text-slate-800">
                        {{ number_format($displaySoldItems) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-bold text-slate-500">売上</div>
                    <div class="mt-2 text-2xl font-black text-emerald-700">
                        ¥{{ number_format($displayTotalSales) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-bold text-slate-500">利益</div>
                    <div class="mt-2 text-2xl font-black text-orange-600">
                        ¥{{ number_format($displayTotalProfit) }}
                    </div>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <a href="{{ route('auction-items.index') }}"
                   class="block rounded-2xl bg-blue-700 p-6 text-white shadow transition hover:bg-blue-800">
                    <div class="text-xl font-black">出品一覧</div>
                    <div class="mt-2 text-sm font-semibold text-blue-100">
                        登録済みの商品を確認
                    </div>
                </a>

                <a href="{{ route('auction-items.create') }}"
                   class="block rounded-2xl bg-emerald-600 p-6 text-white shadow transition hover:bg-emerald-700">
                    <div class="text-xl font-black">新規登録</div>
                    <div class="mt-2 text-sm font-semibold text-emerald-100">
                        古着商品を追加
                    </div>
                </a>

                <a href="{{ route('sales.index') }}"
                   class="block rounded-2xl bg-red-600 p-6 text-white shadow-lg transition hover:bg-red-700">
                    <div class="text-xl font-black">売上管理</div>
                    <div class="mt-2 text-sm font-semibold text-red-100">
                        売上・利益を確認
                    </div>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-black text-slate-900">
                        売上サマリー
                    </h3>

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-5">
                            <div class="text-sm font-bold text-slate-500">販売手数料</div>
                            <div class="mt-2 text-2xl font-black text-slate-900">
                                ¥{{ number_format($displayTotalSalesFee) }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-5">
                            <div class="text-sm font-bold text-slate-500">送料</div>
                            <div class="mt-2 text-2xl font-black text-slate-900">
                                ¥{{ number_format($displayTotalShippingFee) }}
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('sales.index') }}"
                       class="mt-5 inline-flex rounded-xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow transition hover:bg-red-700">
                        売上管理を開く
                    </a>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-black text-slate-900">
                        最近更新した商品
                    </h3>

                    @if (($recentItems ?? collect())->count() > 0)
                        <div class="mt-5 space-y-3">
                            @foreach ($recentItems as $item)
                                <a href="{{ route('auction-items.show', $item) }}"
                                   class="block rounded-xl border border-slate-200 bg-white p-4 transition hover:bg-slate-50">
                                    <div class="font-bold text-slate-800">
                                        {{ $item->title }}
                                    </div>

                                    <div class="mt-1 text-sm font-semibold text-slate-500">
                                        管理ID：{{ $item->management_id }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-5 rounded-xl bg-slate-50 p-6 text-sm font-semibold text-slate-500">
                            最近更新した商品はありません。
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>