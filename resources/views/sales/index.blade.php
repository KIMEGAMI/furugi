<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-white">売上管理</h2>
                <p class="mt-1 text-sm text-slate-300">売上・仕入れ・手数料・送料・実利益を出品先別に確認できます。</p>
            </div>
            <a href="{{ route('auction-items.index') }}" class="inline-flex items-center justify-center rounded-lg border border-cyan-300/30 bg-cyan-500/20 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-400/25">
                商品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="py-8 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="inline-flex rounded-full border border-cyan-300/30 bg-cyan-500/20 px-3 py-1 text-xs font-bold text-cyan-100">CSV EXPORT</p>
                        <h3 class="mt-3 text-xl font-black text-white">売上データをCSVで出力</h3>
                        <p class="mt-2 text-sm font-medium text-slate-300">SOLD商品の管理ID・タイトル・出品先・仕入れ値・売上・手数料・送料・実利益・SOLD日をExcelで確認できます。</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="button" onclick="document.getElementById('csvExportHelp').showModal()" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-6 py-4 text-base font-black text-white shadow-lg hover:bg-white/20">
                            出力CSVの項目説明
                        </button>
                        <a href="{{ route('sales.csv') }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-cyan-500/25 px-6 py-4 text-base font-black text-white shadow-lg hover:bg-cyan-400/30">
                            CSVダウンロード
                        </a>
                    </div>
                </div>
            </div>

            <dialog id="csvExportHelp" class="w-11/12 max-w-4xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/70">
                <div class="max-h-[85vh] overflow-y-auto bg-white p-6 text-slate-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-950">出力CSVの項目説明</h3>
                            <p class="mt-2 text-sm font-bold leading-6 text-slate-600">
                                ダウンロードされるCSVの1行目はヘッダ行です。2行目以降に、SOLDになっている商品の売上データが出力されます。
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('csvExportHelp').close()" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-200">閉じる</button>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full border-collapse text-left text-sm">
                            <thead class="bg-slate-100 text-xs font-black text-slate-700">
                                <tr>
                                    <th class="border border-slate-200 px-3 py-2">CSVヘッダ名</th>
                                    <th class="border border-slate-200 px-3 py-2">対応する画面項目</th>
                                    <th class="border border-slate-200 px-3 py-2">説明</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">管理ID</td><td class="border border-slate-200 px-3 py-2">管理ID</td><td class="border border-slate-200 px-3 py-2">商品ごとの管理番号です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">タイトル</td><td class="border border-slate-200 px-3 py-2">商品タイトル</td><td class="border border-slate-200 px-3 py-2">商品名として登録した内容です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">大ジャンル</td><td class="border border-slate-200 px-3 py-2">大ジャンル</td><td class="border border-slate-200 px-3 py-2">商品に設定されている親ジャンルです。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">小ジャンル</td><td class="border border-slate-200 px-3 py-2">小ジャンル</td><td class="border border-slate-200 px-3 py-2">商品に設定されている子ジャンルです。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">出品先</td><td class="border border-slate-200 px-3 py-2">出品先</td><td class="border border-slate-200 px-3 py-2">ヤフオク、メルカリ、ラクマなどの販売先です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">仕入れ値</td><td class="border border-slate-200 px-3 py-2">仕入れ値</td><td class="border border-slate-200 px-3 py-2">商品登録・編集画面で入力した原価です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">売値</td><td class="border border-slate-200 px-3 py-2">売値</td><td class="border border-slate-200 px-3 py-2">SOLD時点の販売額です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">販売手数料率</td><td class="border border-slate-200 px-3 py-2">販売手数料率</td><td class="border border-slate-200 px-3 py-2">売値に対する販売手数料率です。CSVでは%付きで出力します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">販売手数料</td><td class="border border-slate-200 px-3 py-2">販売手数料</td><td class="border border-slate-200 px-3 py-2">売値と手数料率から計算された金額、または保存済みの手数料です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">送料</td><td class="border border-slate-200 px-3 py-2">送料</td><td class="border border-slate-200 px-3 py-2">商品に登録した送料です。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">実利益</td><td class="border border-slate-200 px-3 py-2">実利益</td><td class="border border-slate-200 px-3 py-2">売値 - 仕入れ値 - 販売手数料 - 送料 で計算します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">ステータス</td><td class="border border-slate-200 px-3 py-2">ステータス</td><td class="border border-slate-200 px-3 py-2">売上CSVでは SOLD の商品のみ出力されます。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">SOLD日</td><td class="border border-slate-200 px-3 py-2">SOLD日</td><td class="border border-slate-200 px-3 py-2">SOLDにした日付です。形式は YYYY-MM-DD です。</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </dialog>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">累計売上</p><p class="mt-2 text-2xl font-bold text-white">¥{{ number_format($totalSales) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">累計仕入れ</p><p class="mt-2 text-2xl font-bold text-white">¥{{ number_format($totalPurchase) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">累計販売手数料</p><p class="mt-2 text-2xl font-bold text-white">¥{{ number_format($totalSalesFee) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">累計送料</p><p class="mt-2 text-2xl font-bold text-white">¥{{ number_format($totalShippingFee) }}</p></div>
                <div class="rounded-2xl border border-lime-300/30 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">累計実利益</p><p class="mt-2 text-2xl font-bold {{ $totalProfit < 0 ? 'text-red-400' : 'text-lime-300' }}">¥{{ number_format($totalProfit) }}</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">SOLD件数</p><p class="mt-2 text-2xl font-bold text-white">{{ number_format($soldCount) }}件</p></div>
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-5"><p class="text-sm text-slate-300">現在の出品中件数</p><p class="mt-2 text-2xl font-bold text-white">{{ number_format($sellingCount) }}件</p></div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('sales.index', ['month' => $previousMonth]) }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-cyan-500/20 px-4 py-2 text-sm font-black text-white hover:bg-cyan-400/25">← 前の月</a>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-white">月別 売上・実利益グラフ</h3>
                            <p class="mt-1 text-sm text-slate-300">{{ $periodStart->format('Y年n月') }}〜{{ $periodEnd->format('Y年n月') }}</p>
                        </div>
                        <a href="{{ route('sales.index', ['month' => $nextMonth]) }}" class="inline-flex items-center justify-center rounded-xl border border-cyan-300/30 bg-cyan-500/25 px-4 py-2 text-sm font-black text-white hover:bg-cyan-400/30">次の月 →</a>
                    </div>
                    <p class="mt-4 text-sm text-slate-300">表示期間内のSOLD商品の月別推移です。データがない月は0円で表示します。</p>
                    <div class="mt-6 h-80"><canvas id="monthlySalesChart"></canvas></div>
                </div>

                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
                    <h3 class="text-lg font-semibold text-white">出品先別 売上グラフ</h3>
                    <p class="mt-1 text-sm text-slate-300">出品先ごとの売上割合を表示します。</p>
                    <div class="mt-6 h-80"><canvas id="platformSalesChart"></canvas></div>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
                    <h3 class="text-lg font-semibold text-white">出品先別 売上集計</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-cyan-300/20 text-sm">
                            <thead class="bg-cyan-400/10">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-200">出品先</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">件数</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">売上</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">仕入れ</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">手数料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">送料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">実利益</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cyan-300/15 bg-slate-950/20">
                                @foreach ($platformSales as $row)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-white">{{ $row['platform'] }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">{{ number_format($row['count']) }}件</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['sales']) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['purchase']) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['sales_fee']) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['shipping_fee']) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] < 0 ? 'text-red-400' : 'text-lime-300' }}">¥{{ number_format($row['profit']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
                    <h3 class="text-lg font-semibold text-white">月別売上一覧</h3>
                    <p class="mt-1 text-sm text-slate-300">{{ $periodStart->format('Y年n月') }}〜{{ $periodEnd->format('Y年n月') }} の月別集計です。</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-cyan-300/20 text-sm">
                            <thead class="bg-cyan-400/10">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-200">月</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">SOLD件数</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">売上</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">仕入れ</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">手数料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">送料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-200">実利益</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cyan-300/15 bg-slate-950/20">
                                @foreach ($monthlySales as $row)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-white">{{ $row['month'] }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">{{ number_format($row['count']) }}件</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['sales']) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['purchase']) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['sales_fee']) }}</td>
                                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['shipping_fee']) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] < 0 ? 'text-red-400' : 'text-lime-300' }}">¥{{ number_format($row['profit']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                @include('sales.partials.ranking-table', [
                    'title' => '出品先 売上ランキング',
                    'description' => '全期間の売上が高い出品先 上位10件です。',
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
                data: { labels: monthlyLabels, datasets: [{ label: '売上', data: monthlySales, borderWidth: 1 }, { label: '実利益', data: monthlyProfit, borderWidth: 1 }] },
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
