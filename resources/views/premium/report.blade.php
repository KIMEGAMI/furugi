<x-app-layout>
    <div class="min-h-screen bg-slate-950 bg-cover bg-center bg-fixed text-white" style="background-image: linear-gradient(rgba(2, 6, 23, 0.52), rgba(2, 6, 23, 0.82)), url('{{ asset('images/bg.png') }}');">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <section class="mb-8 rounded-2xl border border-cyan-300/20 bg-slate-950/65 p-6 shadow-2xl backdrop-blur-md md:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-black tracking-[0.24em] text-cyan-300">PREMIUM REPORT</p>
                        <h1 class="mt-3 text-3xl font-black text-white md:text-5xl">運用診断レポート</h1>
                        <p class="mt-4 max-w-3xl text-sm font-semibold leading-7 text-cyan-100">
                            在庫回転率、売れ残り期間、値下げ候補、仕入れ上限をまとめて確認できます。次に何を直すべきかを判断するためのPremiumレポートです。
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-cyan-100 hover:bg-white/20">ダッシュボード</a>
                        <a href="{{ route('auction-items.index') }}" class="rounded-xl bg-cyan-300 px-5 py-3 text-sm font-black text-slate-950 hover:bg-cyan-200">商品一覧</a>
                    </div>
                </div>
            </section>

            <section class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-cyan-300/20 bg-cyan-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-cyan-200">出品中在庫</p>
                    <p class="mt-3 text-3xl font-black text-white">{{ number_format($inventorySummary['selling_count'] ?? 0) }}件</p>
                    <p class="mt-2 text-xs font-bold text-cyan-100">平均滞留 {{ number_format($inventorySummary['average_days_listed'] ?? 0, 1) }}日</p>
                </div>
                <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-emerald-200">在庫原価</p>
                    <p class="mt-3 text-3xl font-black text-white">¥{{ number_format($inventorySummary['inventory_cost'] ?? 0) }}</p>
                </div>
                <div class="rounded-2xl border border-blue-300/20 bg-blue-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-blue-200">30日販売数</p>
                    <p class="mt-3 text-3xl font-black text-white">{{ number_format($turnoverSummary['sold_30'] ?? 0) }}件</p>
                    <p class="mt-2 text-xs font-bold text-blue-100">90日 {{ number_format($turnoverSummary['sold_90'] ?? 0) }}件</p>
                </div>
                <div class="rounded-2xl border border-amber-300/20 bg-amber-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-amber-200">30日消化率</p>
                    <p class="mt-3 text-3xl font-black text-white">{{ number_format($turnoverSummary['sell_through_30'] ?? 0, 1) }}%</p>
                    <p class="mt-2 text-xs font-bold text-amber-100">在庫に対してどれだけ売れたか</p>
                </div>
                <div class="rounded-2xl border border-rose-300/20 bg-rose-400/10 p-5 shadow-xl backdrop-blur-md">
                    <p class="text-sm font-bold text-rose-200">売れ残り原価率</p>
                    <p class="mt-3 text-3xl font-black text-white">{{ number_format($inventorySummary['stale_cost_rate'] ?? 0, 1) }}%</p>
                    <p class="mt-2 text-xs font-bold text-rose-100">30日以上 {{ number_format($inventorySummary['stale_count'] ?? 0) }}件</p>
                </div>
            </section>

            <section class="mb-8 rounded-2xl border border-cyan-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black tracking-[0.24em] text-cyan-300">THIS MONTH</p>
                        <h2 class="mt-2 text-2xl font-black text-white">今月やること</h2>
                    </div>
                    <p class="text-sm font-bold text-cyan-100">数字を見るだけで終わらせず、今月の作業に落とし込みます。</p>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @foreach ($monthlyActionPlan as $action)
                        <article class="rounded-2xl border border-cyan-300/20 bg-cyan-400/10 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-black">{{ $action['priority'] }}</span>
                                <span class="rounded-full border border-cyan-300/30 px-3 py-1 text-xs font-black text-cyan-100">{{ $action['metric'] }}</span>
                            </div>
                            <h3 class="mt-4 text-lg font-black text-white">{{ $action['title'] }}</h3>
                            <p class="mt-3 text-sm font-bold leading-6 text-cyan-100">{{ $action['reason'] }}</p>
                            <p class="mt-3 rounded-xl bg-slate-950/60 p-3 text-sm font-bold leading-6 text-white">{{ $action['todo'] }}</p>
                            <a href="{{ $action['href'] }}" class="mt-4 inline-flex rounded-xl bg-cyan-300 px-4 py-3 text-sm font-black text-slate-950 hover:bg-cyan-200">
                                {{ $action['cta'] }}
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div id="purchase-ceiling" class="rounded-2xl border border-cyan-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md lg:col-span-2 scroll-mt-6">
                    <h2 class="text-xl font-black text-white">仕入れ上限の自動提案</h2>
                    <p class="mt-2 text-sm font-semibold text-cyan-100">直近90日の販売実績から、利益率30%を残すための目安仕入れ上限を出しています。2件以上の販売実績がある出品先/カテゴリだけ表示します。</p>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="text-xs font-black text-cyan-200">
                                <tr>
                                    <th class="py-3 pr-4">出品先 / カテゴリ</th>
                                    <th class="py-3 pr-4 text-right">実績</th>
                                    <th class="py-3 pr-4 text-right">平均売値</th>
                                    <th class="py-3 pr-4 text-right">手数料率</th>
                                    <th class="py-3 pr-4 text-right">目標利益</th>
                                    <th class="py-3 pr-4 text-right">仕入れ上限</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse ($purchaseCeilingSuggestions as $row)
                                    <tr>
                                        <td class="py-4 pr-4 font-black text-white">{{ $row['label'] }}</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">{{ number_format($row['count']) }}件</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">¥{{ number_format($row['average_sold_price']) }}</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">{{ number_format($row['average_fee_rate'], 1) }}%</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">¥{{ number_format($row['target_profit']) }}</td>
                                        <td class="py-4 pr-4 text-right text-lg font-black text-emerald-200">¥{{ number_format($row['max_purchase_price']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center font-bold text-cyan-100">提案に必要な販売実績がまだ足りません。SOLDデータが増えると表示されます。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black text-white">次のアクション</h2>
                    <div class="mt-5 space-y-3">
                        @foreach ($nextActions as $action)
                            <div class="rounded-xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-sm font-bold leading-6 text-emerald-50">
                                {{ $action }}
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 rounded-xl border border-cyan-300/20 bg-cyan-400/10 p-4 text-sm font-bold leading-6 text-cyan-50">
                        平均販売日数: {{ number_format($turnoverSummary['average_days_to_sell'] ?? 0, 1) }}日<br>
                        月間在庫回転率: {{ number_format($turnoverSummary['monthly_turnover'] ?? 0, 2) }}回
                    </div>
                </div>
            </section>

            <section class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-amber-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black text-white">在庫回転・売れ残り期間</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($agingBuckets as $bucket)
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm font-bold text-cyan-100">
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

                <div id="stale-items" class="rounded-2xl border border-rose-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md scroll-mt-6">
                    <h2 class="text-xl font-black text-white">売れ残り上位</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($staleItems as $row)
                            <a href="{{ route('auction-items.show', $row['item']) }}" class="block rounded-xl border border-white/10 bg-white/10 p-4 hover:bg-white/15">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-black text-white">{{ $row['item']->title }}</p>
                                        <p class="mt-1 text-xs font-bold text-cyan-100">{{ $row['item']->management_id }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-black text-amber-200">{{ $row['days'] }}日</p>
                                        <p class="mt-1 text-xs font-bold text-cyan-100">¥{{ number_format($row['price']) }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-xl bg-white/10 p-5 text-sm font-bold text-cyan-100">出品中の商品がありません。</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="markdown-candidates" class="mb-8 rounded-2xl border border-cyan-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md scroll-mt-6">
                <h2 class="text-xl font-black text-white">値下げ候補</h2>
                <p class="mt-2 text-sm font-semibold text-cyan-100">30日以上動いていない商品を、滞留日数の長い順に表示します。</p>
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-xs font-black text-cyan-200">
                            <tr>
                                <th class="py-3 pr-4">商品</th>
                                <th class="py-3 pr-4 text-right">滞留</th>
                                <th class="py-3 pr-4 text-right">現在価格</th>
                                <th class="py-3 pr-4 text-right">提案価格</th>
                                <th class="py-3 pr-4 text-right">想定利益</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($markdownCandidates as $row)
                                <tr>
                                    <td class="py-4 pr-4">
                                        <a href="{{ route('auction-items.show', $row['item']) }}" class="font-black text-white hover:text-cyan-200">{{ $row['item']->title }}</a>
                                        <div class="mt-1 text-xs font-bold text-cyan-100">{{ $row['item']->management_id }}</div>
                                    </td>
                                    <td class="py-4 pr-4 text-right font-bold text-amber-200">{{ $row['days'] }}日</td>
                                    <td class="py-4 pr-4 text-right font-bold text-cyan-100">¥{{ number_format($row['current_price']) }}</td>
                                    <td class="py-4 pr-4 text-right font-black text-emerald-200">¥{{ number_format($row['suggested_price']) }}<div class="text-xs text-cyan-100">{{ $row['discount_rate'] }}%下げ</div></td>
                                    <td class="py-4 pr-4 text-right font-bold {{ $row['expected_profit'] < 0 ? 'text-rose-200' : 'text-cyan-100' }}">¥{{ number_format($row['expected_profit']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center font-bold text-cyan-100">値下げ候補はありません。</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-rose-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md">
                    <h2 class="text-xl font-black text-white">低利益の見直し候補</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($lowProfitItems as $row)
                            <a href="{{ route('auction-items.show', $row['item']) }}" class="block rounded-xl border border-white/10 bg-white/10 p-4 hover:bg-white/15">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-black text-white">{{ $row['item']->title }}</p>
                                        <p class="mt-1 text-xs font-bold text-cyan-100">{{ $row['item']->management_id }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-black text-rose-200">{{ number_format($row['profit_rate'], 1) }}%</p>
                                        <p class="mt-1 text-xs font-bold text-cyan-100">¥{{ number_format($row['profit']) }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-xl bg-white/10 p-5 text-sm font-bold text-cyan-100">低利益の見直し候補はありません。</div>
                        @endforelse
                    </div>
                </div>

                <div id="platform-performance" class="rounded-2xl border border-cyan-300/20 bg-slate-950/60 p-6 shadow-2xl backdrop-blur-md scroll-mt-6">
                    <h2 class="text-xl font-black text-white">出品先別パフォーマンス</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="text-xs font-black text-cyan-200">
                                <tr>
                                    <th class="py-3 pr-4">出品先</th>
                                    <th class="py-3 pr-4 text-right">販売</th>
                                    <th class="py-3 pr-4 text-right">売上</th>
                                    <th class="py-3 pr-4 text-right">利益</th>
                                    <th class="py-3 pr-4 text-right">利益率</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse ($platformPerformance as $row)
                                    <tr>
                                        <td class="py-4 pr-4 font-black text-white">{{ $row['platform'] }}</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">{{ number_format($row['count']) }}件</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">¥{{ number_format($row['sales']) }}</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">¥{{ number_format($row['profit']) }}</td>
                                        <td class="py-4 pr-4 text-right font-bold text-cyan-100">{{ number_format($row['profit_rate'], 1) }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center font-bold text-cyan-100">直近90日の販売データがありません。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
