<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    売上管理
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    SOLD商品の売上・手数料・送料・実利益を確認できます。
                </p>
            </div>

            <a href="{{ route('auction-items.index') }}"
               class="inline-flex items-center justify-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                商品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                            CSV EXPORT
                        </p>

                        <h3 class="mt-3 text-xl font-black text-slate-900">
                            売上データをCSVで出力
                        </h3>

                        <p class="mt-2 text-sm font-medium text-slate-600">
                            SOLD商品の管理ID・タイトル・出品先・売上・手数料・送料・実利益・SOLD日をExcelで確認できます。
                        </p>
                    </div>

                    <a href="{{ route('sales.csv') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-6 py-4 text-base font-black text-white shadow-lg hover:bg-blue-800">
                        CSVダウンロード
                    </a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">累計売上</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        ¥{{ number_format($totalSales) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">累計実利益</p>
                    <p class="mt-2 text-2xl font-bold {{ $totalProfit < 0 ? 'text-red-600' : 'text-green-700' }}">
                        ¥{{ number_format($totalProfit) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">累計販売手数料</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        ¥{{ number_format($totalSalesFee) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">累計送料</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        ¥{{ number_format($totalShippingFee) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">今月の売上</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        ¥{{ number_format($thisMonthSales) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">今月の実利益</p>
                    <p class="mt-2 text-2xl font-bold {{ $thisMonthProfit < 0 ? 'text-red-600' : 'text-green-700' }}">
                        ¥{{ number_format($thisMonthProfit) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">今月の販売手数料</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        ¥{{ number_format($thisMonthSalesFee) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">今月の送料</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        ¥{{ number_format($thisMonthShippingFee) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">SOLD件数</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ number_format($soldCount) }}件
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">出品中件数</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ number_format($sellingCount) }}件
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        月別 売上・実利益グラフ
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        SOLD商品の月別推移です。
                    </p>

                    <div class="mt-6 h-80">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        出品先別 売上グラフ
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        ヤフオク・メルカリなど出品先ごとの売上です。
                    </p>

                    <div class="mt-6 h-80">
                        <canvas id="platformSalesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        出品先別 売上集計
                    </h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">出品先</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">件数</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">売上</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">手数料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">送料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">実利益</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($platformSales as $row)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $row['platform'] }}
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            {{ number_format($row['count']) }}件
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            ¥{{ number_format($row['sales']) }}
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            ¥{{ number_format($row['sales_fee']) }}
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            ¥{{ number_format($row['shipping_fee']) }}
                                        </td>

                                        <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] < 0 ? 'text-red-600' : 'text-green-700' }}">
                                            ¥{{ number_format($row['profit']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            SOLD済みの商品がまだありません。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        月別売上一覧
                    </h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">月</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">SOLD件数</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">売上</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">手数料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">送料</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">実利益</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($monthlySales as $row)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $row['month'] }}
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            {{ number_format($row['count']) }}件
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            ¥{{ number_format($row['sales']) }}
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            ¥{{ number_format($row['sales_fee']) }}
                                        </td>

                                        <td class="px-4 py-3 text-right text-gray-700">
                                            ¥{{ number_format($row['shipping_fee']) }}
                                        </td>

                                        <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] < 0 ? 'text-red-600' : 'text-green-700' }}">
                                            ¥{{ number_format($row['profit']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            月別売上データがまだありません。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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

        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: '売上',
                            data: monthlySales,
                            borderWidth: 1
                        },
                        {
                            label: '実利益',
                            data: monthlyProfit,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ¥' + Number(context.raw).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '¥' + Number(value).toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        if (platformCanvas) {
            new Chart(platformCanvas, {
                type: 'doughnut',
                data: {
                    labels: platformLabels,
                    datasets: [
                        {
                            label: '売上',
                            data: platformSales,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ¥' + Number(context.raw).toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>