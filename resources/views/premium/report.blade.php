<x-app-layout>
    <div class="min-h-screen bg-slate-950 bg-cover bg-center bg-fixed text-white" style="background-image: linear-gradient(rgba(2, 6, 23, 0.50), rgba(2, 6, 23, 0.78)), url('{{ asset('images/bg.png') }}');">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <section class="mb-8 rounded-2xl border border-emerald-300/20 bg-slate-950/65 p-6 shadow-2xl backdrop-blur-md md:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black tracking-[0.24em] text-emerald-300">PREMIUM REPORT</p>
                        <h1 class="mt-3 text-3xl font-black md:text-5xl">運用診断レポート</h1>
                        <p class="mt-4 max-w-3xl text-sm font-semibold leading-7 text-slate-300">
                            在庫の滞留、値下げ候補、低利益商品、出品先別の成果をまとめて確認できます。次に何を直すべきかを判断するためのPremium専用レポートです。
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white hover:bg-white/20">ダッシュボード</a>
                        <a href="{{ route('auction-items.index') }}" class="rounded-xl bg-emerald-400 px-5 py-3 text-sm font-black text-slate-950 hover:bg-emerald-300">商品一覧</a>
                    </div>
                </div>
            </section>

            <section class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-cyan-300/20 bg-cyan-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-cyan-200">販売中在庫</p>
                    <p class="mt-3 text-3xl font-black">{{ number_format($inventorySummary['selling_count'] ?? 0) }}件</p>
                </div>
                <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-emerald-200">在庫原価</p>
                    <p class="mt-3 text-3xl font-black">¥{{ number_format($inventorySummary['inventory_cost'] ?? 0) }}</p>
                </div>
                <div class="rounded-2xl border border-blue-300/20 bg-blue-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-blue-200">販売予定額</p>
                    <p class="mt-3 text-3xl font-black">¥{{ number_format($inventorySummary['expected_sales'] ?? 0) }}</p>
                </div>
                <div class="rounded-2xl border border-amber-300/20 bg-amber-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-amber-200">滞留在庫</p>
                    <p class="mt-3 text-3xl font-black">{{ number_format($inventorySummary['stale_count'] ?? 0) }}件</p>
                </div>
                <div class="rounded-2xl border border-rose-300/20 bg-rose-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-rose-200">滞留原価率</p>
                    <p class="mt-3 text-3xl font-black">{{ number_format($inventorySummary['stale_cost_rate'] ?? 0, 1) }}%</p>
                </div>
            </section>

            <section class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md lg:col-span-2">
                    <h2 class="text-xl font-black">値下げ候補</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-300">30日以上動いていない販売中商品を、滞留日数の長い順に表示します。</p>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="text-xs font-black text-cyan-200">
                                <tr>
                                    <th class="py-3 pr-4">商品</th>
                                    <th class="py-3 pr-4">滞留</th>
                                    <th class="py-3 pr-4">現在価格</th>
                                    <th class="py-3 pr-4">提案価格</th>
                                    <th class="py-3 pr-4">想定利益</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse ($markdownCandidates as $row)
                                    <tr>
                                        <td class="py-4 pr-4">
                                            <a href="{{ route('auction-items.show', $row['item']) }}" class="font-black text-white hover:text-cyan-200">{{ $row['item']->title }}</a>
                                            <div class="mt-1 text-xs font-bold text-slate-400">{{ $row['item']->management_id }}</div>
                                        </td>
                                        <td class="py-4 pr-4 font-bold text-amber-200">{{ $row['days'] }}日</td>
                                        <td class="py-4 pr-4 font-bold">¥{{ number_format($row['current_price']) }}</td>
                                        <td class="py-4 pr-4 font-black text-emerald-200">¥{{ number_format($row['suggested_price']) }}<div class="text-xs text-slate-400">{{ $row['discount_rate'] }}%下げ</div></td>
                                        <td class="py-4 pr-4 font-bold {{ $row['expected_profit'] < 0 ? 'text-rose-200' : 'text-slate-100' }}">¥{{ number_format($row['expected_profit']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center font-bold text-slate-300">値下げ候補はありません。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black">次のアクション</h2>
                    <div class="mt-5 space-y-3">
                        @foreach ($nextActions as $action)
                            <div class="rounded-xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-sm font-bold leading-6 text-emerald-50">
                                {{ $action }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-amber-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black">在庫年齢</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($agingBuckets as $bucket)
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm font-bold">
                                    <span>{{ $bucket['label'] }}</span>
                                    <span>{{ number_format($bucket['count']) }}件 / ¥{{ number_format($bucket['cost']) }}</span>
                                </div>
                                <div class="h-3 overflow-hidden rounded-full bg-slate-800">
                                    <div class="h-full rounded-full bg-amber-300" style="width: {{ min(100, (($inventorySummary['selling_count'] ?? 0) > 0 ? ($bucket['count'] / $inventorySummary['selling_count']) * 100 : 0)) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-rose-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black">低利益の見直し候補</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($lowProfitItems as $row)
                            <a href="{{ route('auction-items.show', $row['item']) }}" class="block rounded-xl border border-white/10 bg-white/10 p-4 hover:bg-white/15">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-black text-white">{{ $row['item']->title }}</p>
                                        <p class="mt-1 text-xs font-bold text-slate-400">{{ $row['item']->management_id }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-black text-rose-200">{{ number_format($row['profit_rate'], 1) }}%</p>
                                        <p class="mt-1 text-xs font-bold text-slate-300">¥{{ number_format($row['profit']) }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-xl bg-white/10 p-5 text-sm font-bold text-slate-300">低利益の見直し候補はありません。</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-cyan-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                <h2 class="text-xl font-black">出品先別パフォーマンス</h2>
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-xs font-black text-cyan-200">
                            <tr>
                                <th class="py-3 pr-4">出品先</th>
                                <th class="py-3 pr-4">販売件数</th>
                                <th class="py-3 pr-4">売上</th>
                                <th class="py-3 pr-4">利益</th>
                                <th class="py-3 pr-4">利益率</th>
                                <th class="py-3 pr-4">平均利益</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($platformPerformance as $row)
                                <tr>
                                    <td class="py-4 pr-4 font-black">{{ $row['platform'] }}</td>
                                    <td class="py-4 pr-4 font-bold">{{ number_format($row['count']) }}件</td>
                                    <td class="py-4 pr-4 font-bold">¥{{ number_format($row['sales']) }}</td>
                                    <td class="py-4 pr-4 font-bold">¥{{ number_format($row['profit']) }}</td>
                                    <td class="py-4 pr-4 font-bold">{{ number_format($row['profit_rate'], 1) }}%</td>
                                    <td class="py-4 pr-4 font-bold">¥{{ number_format($row['average_profit']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center font-bold text-slate-300">直近90日の販売データがありません。</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
