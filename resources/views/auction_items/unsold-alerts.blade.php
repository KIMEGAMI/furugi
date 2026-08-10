<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-cyan-200">売れ残りアラート商品</h2>
                <p class="mt-1 text-sm font-bold text-cyan-100">出品中のまま一定期間売れていない商品を確認できます。</p>
            </div>
            <a href="{{ route('auction-items.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-black shadow transition hover:bg-cyan-50">
                商品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 sm:pb-0 lg:px-8">
            <section class="mb-6 rounded-3xl border border-amber-200 bg-white p-5 shadow">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-black tracking-widest text-amber-700">UNSOLD ALERT</p>
                        <h3 class="mt-2 text-xl font-black text-slate-950">売れ残りアラート</h3>
                        <p class="mt-2 text-sm font-bold leading-6 text-slate-700">{{ $inventoryAlerts['message'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('auction-items.unsold-alerts', ['threshold' => 14]) }}" class="rounded-xl px-4 py-3 text-sm font-black shadow {{ $threshold === 14 ? 'bg-amber-600 text-white' : 'bg-white text-slate-950 ring-1 ring-slate-200 hover:bg-slate-50' }}">
                            14日以上 {{ number_format($inventoryAlerts['older_than_14_count']) }}件
                        </a>
                        <a href="{{ route('auction-items.unsold-alerts', ['threshold' => 30]) }}" class="rounded-xl px-4 py-3 text-sm font-black shadow {{ $threshold === 30 ? 'bg-red-600 text-white' : 'bg-white text-slate-950 ring-1 ring-slate-200 hover:bg-slate-50' }}">
                            30日以上 {{ number_format($inventoryAlerts['older_than_30_count']) }}件
                        </a>
                    </div>
                </div>
            </section>

            @if ($alertItems->count() > 0)
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-5 py-4 text-left font-black text-slate-700">商品</th>
                                    <th scope="col" class="px-5 py-4 text-left font-black text-slate-700">出品先</th>
                                    <th scope="col" class="px-5 py-4 text-left font-black text-slate-700">カテゴリ</th>
                                    <th scope="col" class="px-5 py-4 text-right font-black text-slate-700">経過日数</th>
                                    <th scope="col" class="px-5 py-4 text-right font-black text-slate-700">販売価格</th>
                                    <th scope="col" class="px-5 py-4 text-right font-black text-slate-700">仕入れ</th>
                                    <th scope="col" class="px-5 py-4 text-right font-black text-slate-700">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($alertItems as $row)
                                    @php($item = $row['item'])
                                    <tr class="hover:bg-amber-50/50">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-slate-200">
                                                    @if ($item->image_path)
                                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full w-full items-center justify-center text-xs font-black text-slate-600">NO</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black tracking-widest text-slate-500">{{ $item->management_id }}</p>
                                                    <p class="mt-1 max-w-md font-black text-slate-950">{{ $item->title }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 font-bold text-slate-700">{{ $row['platform_label'] }}</td>
                                        <td class="px-5 py-4 font-bold text-slate-700">{{ $row['category_label'] }}</td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="inline-flex rounded-full {{ $row['days_listed'] >= 30 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900' }} px-3 py-1 text-xs font-black">
                                                {{ number_format($row['days_listed']) }}日
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-slate-950">¥{{ number_format((int) ($item->sold_price ?? 0)) }}</td>
                                        <td class="px-5 py-4 text-right font-black text-slate-950">¥{{ number_format((int) ($item->purchase_price ?? 0)) }}</td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('auction-items.show', $item) }}" class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-black text-black hover:bg-slate-200">詳細</a>
                                                <a href="{{ route('auction-items.edit', $item) }}" class="rounded-xl bg-blue-700 px-4 py-2 text-xs font-black text-white hover:bg-blue-800">編集</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8">{{ $alertItems->links('vendor.pagination.furupro') }}</div>
            @else
                <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow">
                    <h3 class="text-2xl font-black text-slate-900">アラート対象の商品はありません</h3>
                    <p class="mt-3 font-semibold text-slate-700">{{ $threshold }}日以上売れていない出品中商品はありません。</p>
                    <a href="{{ route('auction-items.index') }}" class="mt-8 inline-flex items-center justify-center rounded-xl bg-blue-700 px-6 py-4 text-sm font-bold text-white shadow transition hover:bg-blue-800">商品一覧へ戻る</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
