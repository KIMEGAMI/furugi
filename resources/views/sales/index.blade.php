<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-900">
                    売上管理
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    SOLD済み商品の売上・利益・月別実績を確認します。
                </p>
            </div>

            <a
                href="{{ route('auction-items.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-700 border border-slate-200 shadow hover:bg-slate-50 transition"
            >
                出品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-3xl bg-white border border-slate-200 shadow p-6">
                    <p class="text-sm font-bold text-slate-500">
                        SOLD件数
                    </p>
                    <p class="mt-3 text-4xl font-black text-blue-900">
                        {{ $soldItems->count() }}件
                    </p>
                </div>

                <div class="rounded-3xl bg-white border border-slate-200 shadow p-6">
                    <p class="text-sm font-bold text-slate-500">
                        累計売上
                    </p>
                    <p class="mt-3 text-4xl font-black text-blue-900">
                        ¥{{ number_format($totalSales) }}
                    </p>
                </div>

                <div class="rounded-3xl bg-white border border-slate-200 shadow p-6">
                    <p class="text-sm font-bold text-slate-500">
                        累計利益
                    </p>
                    <p class="mt-3 text-4xl font-black {{ $totalProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        ¥{{ number_format($totalProfit) }}
                    </p>
                </div>
            </div>

            <div class="mt-8 rounded-3xl bg-white border border-slate-200 shadow overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <h3 class="text-xl font-black text-slate-900">
                        月別売上
                    </h3>
                </div>

                @if($monthlySales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-black tracking-wider text-slate-500">
                                        月
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-black tracking-wider text-slate-500">
                                        SOLD件数
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-black tracking-wider text-slate-500">
                                        売上
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-black tracking-wider text-slate-500">
                                        利益
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach($monthlySales as $month => $summary)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-black text-slate-900">
                                            {{ $month }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-slate-700">
                                            {{ $summary['count'] }}件
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-blue-900">
                                            ¥{{ number_format($summary['sales']) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold {{ $summary['profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                            ¥{{ number_format($summary['profit']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-10 text-center">
                        <p class="font-bold text-slate-500">
                            まだSOLD済みの商品がありません。
                        </p>
                    </div>
                @endif
            </div>

            <div class="mt-8 rounded-3xl bg-white border border-slate-200 shadow overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <h3 class="text-xl font-black text-slate-900">
                        SOLD済み商品一覧
                    </h3>
                </div>

                @if($soldItems->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-black tracking-wider text-slate-500">
                                        SOLD日
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-black tracking-wider text-slate-500">
                                        管理ID
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-black tracking-wider text-slate-500">
                                        タイトル
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-black tracking-wider text-slate-500">
                                        仕入れ値
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-black tracking-wider text-slate-500">
                                        売値
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-black tracking-wider text-slate-500">
                                        利益
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach($soldItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-bold text-slate-700">
                                            {{ optional($item->sold_at)->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-black text-blue-700">
                                            {{ $item->management_id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-slate-900">
                                            <a
                                                href="{{ route('auction-items.show', $item) }}"
                                                class="hover:text-blue-700 hover:underline"
                                            >
                                                {{ $item->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-slate-700">
                                            ¥{{ number_format($item->purchase_price) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-blue-900">
                                            ¥{{ number_format($item->sold_price) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold {{ $item->profit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                            ¥{{ number_format($item->profit) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-10 text-center">
                        <p class="font-bold text-slate-500">
                            SOLD済みの商品が登録されると、ここに表示されます。
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>