<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-cyan-200">月別利益レポート</h2>
                <p class="mt-1 text-sm font-bold text-cyan-100">売上、仕入れ、手数料、送料、実利益、利益率を月別に確認できます。</p>
            </div>
            <a href="{{ route('auction-items.index') }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-white/10 px-4 py-3 text-sm font-black text-cyan-100 shadow-sm hover:bg-white/20">
                商品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="py-8 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="mb-6 rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-6 shadow-sm backdrop-blur-md">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="inline-flex rounded-full border border-cyan-300/30 bg-cyan-500/20 px-3 py-1 text-xs font-bold text-cyan-100">EXPORT & BACKUP</p>
                        <h3 class="mt-3 text-xl font-black text-white">CSV出力とバックアップ</h3>
                        <p class="mt-2 text-sm font-bold leading-6 text-cyan-100">
                            売上CSVはSOLD商品の分析用、バックアップCSVは全商品の控え用です。どちらもExcelで開きやすいUTF-8 BOM付きで出力します。
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="button" onclick="document.getElementById('csvExportHelp').showModal()" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-6 py-4 text-base font-black text-cyan-100 shadow-lg hover:bg-white/20">
                            CSV項目説明
                        </button>
                        <a href="{{ route('sales.csv') }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-cyan-500/25 px-6 py-4 text-base font-black text-white shadow-lg hover:bg-cyan-400/30">
                            売上CSV
                        </a>
                        <a href="{{ route('sales.backup-csv') }}" class="inline-flex items-center justify-center rounded-xl border border-emerald-300/30 bg-emerald-500/25 px-6 py-4 text-base font-black text-white shadow-lg hover:bg-emerald-400/30">
                            全商品バックアップ
                        </a>
                        <a href="{{ route('sales.restore-csv') }}" class="inline-flex items-center justify-center rounded-xl border border-amber-300/30 bg-amber-500/25 px-6 py-4 text-base font-black text-white shadow-lg hover:bg-amber-400/30">
                            復元用CSV
                        </a>
                    </div>
                </div>
            </section>

            <section class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-5 shadow-sm backdrop-blur-md">
                <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <h3 class="text-lg font-black text-white">復元しやすいバックアップ</h3>
                        <p class="mt-2 text-sm font-bold leading-6 text-cyan-100">
                            「全商品バックアップ」は確認用で、画像URLや更新日まで含めます。「復元用CSV」はCSV登録に戻しやすい英字ヘッダ形式です。復元時はCSV登録ページで、重複しない管理IDだけ取り込まれます。
                        </p>
                    </div>
                    <a href="{{ route('auction-items.csv-import') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-black text-black shadow hover:bg-cyan-50">
                        CSV登録ページへ
                    </a>
                </div>
            </section>

            <dialog id="csvExportHelp" class="w-11/12 max-w-4xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/70">
                <div class="max-h-[85vh] overflow-y-auto bg-white p-6 text-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-950">CSVの項目説明</h3>
                            <p class="mt-2 text-sm font-bold leading-6 text-slate-700">
                                1行目はヘッダ行です。売上CSVはSOLD商品の集計、全商品バックアップCSVは復旧や確認用の控えとして使えます。
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('csvExportHelp').close()" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-black text-black hover:bg-slate-200">閉じる</button>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full border-collapse text-left text-sm">
                            <thead class="bg-slate-100 text-xs font-black text-slate-900">
                                <tr>
                                    <th class="border border-slate-200 px-3 py-2">項目</th>
                                    <th class="border border-slate-200 px-3 py-2">意味</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    '管理ID' => '商品ごとの管理番号です。',
                                    '商品タイトル' => '登録した商品名です。',
                                    '大ジャンル / 小ジャンル' => '商品に設定したカテゴリです。',
                                    '出品先' => 'ヤフオク、メルカリ、ラクマなどの販売先です。',
                                    '仕入れ値' => '商品登録時に入力した仕入れ原価です。',
                                    '販売価格 / 売値' => '販売金額または予定販売価格です。',
                                    '販売手数料率 / 販売手数料' => '販売先ごとの手数料率と計算後の手数料です。',
                                    '送料' => '商品ごとに登録した送料です。',
                                    '実利益' => '販売価格から仕入れ値、手数料、送料を引いた金額です。',
                                    'SOLD日' => 'SOLDにした日付です。',
                                    '商品画像URL / SOLD画像URL' => '全商品バックアップCSVに含まれる画像のURLです。',
                                    '復元用CSV' => 'CSV登録へ戻しやすい英字ヘッダ形式です。画像ファイル自体は含まれません。',
                                ] as $heading => $body)
                                    <tr>
                                        <td class="border border-slate-200 px-3 py-2 font-bold text-slate-950">{{ $heading }}</td>
                                        <td class="border border-slate-200 px-3 py-2 text-slate-800">{{ $body }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </dialog>

            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-5"><p class="text-sm font-bold text-cyan-100">累計売上</p><p class="mt-2 text-2xl font-black text-white">¥{{ number_format($totalSales) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-5"><p class="text-sm font-bold text-cyan-100">累計仕入れ</p><p class="mt-2 text-2xl font-black text-white">¥{{ number_format($totalPurchase) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-5"><p class="text-sm font-bold text-cyan-100">手数料・送料</p><p class="mt-2 text-2xl font-black text-white">¥{{ number_format($totalSalesFee + $totalShippingFee) }}</p></div>
                <div class="rounded-2xl border border-lime-300/30 bg-slate-950/55 p-5"><p class="text-sm font-bold text-cyan-100">累計実利益</p><p class="mt-2 text-2xl font-black {{ $totalProfit < 0 ? 'text-red-300' : 'text-lime-300' }}">¥{{ number_format($totalProfit) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-5"><p class="text-sm font-bold text-cyan-100">SOLD / 出品中</p><p class="mt-2 text-2xl font-black text-white">{{ number_format($soldCount) }} / {{ number_format($sellingCount) }}</p></div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-6 shadow-sm backdrop-blur-md">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('sales.index', ['month' => $previousMonth]) }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-cyan-500/20 px-4 py-2 text-sm font-black text-white hover:bg-cyan-400/25">前の月</a>
                        <div class="text-center">
                            <h3 class="text-lg font-black text-white">{{ $baseMonth->format('Y年n月') }}の利益</h3>
                            <p class="mt-1 text-sm font-bold text-cyan-100">{{ $periodStart->format('Y年n月') }}から{{ $periodEnd->format('Y年n月') }}まで表示</p>
                        </div>
                        <a href="{{ route('sales.index', ['month' => $nextMonth]) }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-cyan-500/25 px-4 py-2 text-sm font-black text-white hover:bg-cyan-400/30">次の月</a>
                    </div>
                    <div class="mt-6 h-80"><canvas id="monthlySalesChart"></canvas></div>
                </div>

                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-6 shadow-sm backdrop-blur-md">
                    <h3 class="text-lg font-black text-white">月別サマリー</h3>
                    <div class="mt-5 space-y-3">
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-sm font-bold text-cyan-100">選択月の実利益</p>
                            <p class="mt-1 text-3xl font-black {{ $selectedMonthRow['profit'] < 0 ? 'text-red-300' : 'text-lime-300' }}">¥{{ number_format($selectedMonthRow['profit']) }}</p>
                            <p class="mt-1 text-xs font-bold text-cyan-100">利益率 {{ number_format($selectedMonthRow['profit_rate'], 1) }}% / 平均利益 ¥{{ number_format($selectedMonthRow['average_profit']) }}</p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <p class="text-sm font-bold text-cyan-100">前月との差</p>
                            <p class="mt-1 text-xl font-black {{ $monthlyInsights['profit_diff'] < 0 ? 'text-red-300' : 'text-lime-300' }}">¥{{ number_format($monthlyInsights['profit_diff']) }}</p>
                            <p class="mt-1 text-xs font-bold text-cyan-100">売上差 ¥{{ number_format($monthlyInsights['sales_diff']) }} / SOLD差 {{ number_format($monthlyInsights['count_diff']) }}件</p>
                        </div>
                        <div class="rounded-xl border border-cyan-300/20 bg-cyan-400/10 p-4 text-sm font-bold leading-6 text-cyan-50">
                            {{ $monthlyInsights['message'] }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-6 shadow-sm backdrop-blur-md">
                    <h3 class="text-lg font-black text-white">月別利益一覧</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-cyan-300/20 text-sm">
                            <thead class="bg-cyan-400/10">
                                <tr>
                                    <th class="px-4 py-3 text-left font-black text-cyan-100">月</th>
                                    <th class="px-4 py-3 text-right font-black text-cyan-100">SOLD</th>
                                    <th class="px-4 py-3 text-right font-black text-cyan-100">売上</th>
                                    <th class="px-4 py-3 text-right font-black text-cyan-100">仕入れ</th>
                                    <th class="px-4 py-3 text-right font-black text-cyan-100">実利益</th>
                                    <th class="px-4 py-3 text-right font-black text-cyan-100">利益率</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cyan-300/15 bg-slate-950/20">
                                @foreach ($monthlySales as $row)
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-white">{{ $row['month'] }}</td>
                                        <td class="px-4 py-3 text-right text-cyan-100">{{ number_format($row['count']) }}件</td>
                                        <td class="px-4 py-3 text-right text-cyan-100">¥{{ number_format($row['sales']) }}</td>
                                        <td class="px-4 py-3 text-right text-cyan-100">¥{{ number_format($row['purchase']) }}</td>
                                        <td class="px-4 py-3 text-right font-black {{ $row['profit'] < 0 ? 'text-red-300' : 'text-lime-300' }}">¥{{ number_format($row['profit']) }}</td>
                                        <td class="px-4 py-3 text-right text-cyan-100">{{ number_format($row['profit_rate'], 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/55 p-6 shadow-sm backdrop-blur-md">
                    <h3 class="text-lg font-black text-white">出品先別売上</h3>
                    <div class="mt-6 h-80"><canvas id="platformSalesChart"></canvas></div>
                </div>
            </section>

            <div class="mt-8">
                @include('sales.partials.ranking-table', [
                    'title' => '出品先 売上ランキング',
                    'description' => '売上が高い出品先を確認できます。',
                    'labelHeading' => '出品先',
                    'rows' => $platformRanking,
                ])
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthlyLabels = @json($monthlyChartLabels);
        const monthlySales = @json($monthlyChartSales);
        const monthlyProfit = @json($monthlyChartProfit);
        const platformLabels = @json($platformChartLabels);
        const platformSales = @json($platformChartSales);
        const monthlyCanvas = document.getElementById('monthlySalesChart');
        const platformCanvas = document.getElementById('platformSalesChart');
        const chartTextColor = '#f8fafc';
        const chartGridColor = 'rgba(148, 163, 184, 0.22)';
        const yen = function(value) { return '¥' + Number(value).toLocaleString(); };
        const percentage = function(value, data) {
            const total = data.reduce((sum, current) => sum + Number(current), 0);
            return total <= 0 ? '0.0' : ((Number(value) / total) * 100).toFixed(1);
        };

        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        { label: '売上', data: monthlySales, borderWidth: 1 },
                        { label: '実利益', data: monthlyProfit, borderWidth: 1 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: chartTextColor } }, tooltip: { callbacks: { label: (context) => context.dataset.label + ': ' + yen(context.raw) } } },
                    scales: {
                        x: { ticks: { color: chartTextColor }, grid: { color: chartGridColor } },
                        y: { beginAtZero: true, ticks: { color: chartTextColor, callback: yen }, grid: { color: chartGridColor } }
                    }
                }
            });
        }

        if (platformCanvas) {
            new Chart(platformCanvas, {
                type: 'doughnut',
                data: { labels: platformLabels, datasets: [{ label: '売上', data: platformSales, borderWidth: 1 }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: chartTextColor } },
                        tooltip: { callbacks: { label: (context) => context.label + ': ' + yen(context.raw) + ' (' + percentage(context.raw, context.dataset.data) + '%)' } }
                    }
                }
            });
        }
    </script>
</x-app-layout>
