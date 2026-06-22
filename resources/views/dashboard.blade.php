<x-app-layout>
    @php
        $displayTotalItems = ($sellingCount ?? 0) + ($soldCount ?? 0) + ($draftCount ?? 0);

        $monthlyChartLabels = collect($monthlyStats ?? [])->pluck('label')->values();
        $monthlyChartSales = collect($monthlyStats ?? [])->pluck('sales')->values();
        $monthlyChartProfit = collect($monthlyStats ?? [])->pluck('profit')->values();

        $displayYear = $year ?? now()->year;
    @endphp

    <div
        class="min-h-screen bg-slate-950 bg-cover bg-center bg-fixed text-white"
        style="background-image: linear-gradient(rgba(2, 6, 23, 0.28), rgba(2, 6, 23, 0.52)), url('{{ asset('images/bg.png') }}?v={{ time() }}');"
    >
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <div class="mb-8 rounded-3xl border border-cyan-300/20 bg-slate-950/45 p-8 shadow-2xl backdrop-blur-md">
                <p class="text-sm font-bold text-emerald-300">FURUGI MANAGEMENT SYSTEM</p>

                <h1 class="mt-3 text-3xl font-black tracking-tight md:text-5xl">
                    こんにちは、{{ Auth::user()->name }}さん
                </h1>

                <p class="mt-4 text-sm font-semibold text-slate-300">
                    ダッシュボードの数値・グラフは、登録済みの商品データから集計しています。
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('auction-items.create') }}" class="rounded-2xl bg-emerald-400 px-6 py-3 text-sm font-black text-slate-950 hover:bg-emerald-300">
                        商品を登録する
                    </a>

                    <a href="{{ route('auction-items.index') }}" class="rounded-2xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-black text-white hover:bg-white/20">
                        商品一覧を見る
                    </a>

                    <a href="{{ route('sales.index') }}" class="rounded-2xl border border-red-300/30 bg-red-500/20 px-6 py-3 text-sm font-black text-red-100 hover:bg-red-500/30">
                        売上管理を開く
                    </a>
                </div>
            </div>

            <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-5">
                <div class="rounded-3xl border border-cyan-300/20 bg-slate-950/45 p-5 shadow-xl backdrop-blur-md">
                    <div class="text-sm font-bold text-slate-300">総商品数</div>
                    <div class="mt-3 text-3xl font-black">{{ number_format($displayTotalItems) }}</div>
                </div>

                <div class="rounded-3xl border border-cyan-300/20 bg-cyan-400/10 p-5 shadow-xl backdrop-blur-md">
                    <div class="text-sm font-bold text-cyan-200">出品中</div>
                    <div class="mt-3 text-3xl font-black text-cyan-100">{{ number_format($sellingCount ?? 0) }}</div>
                </div>

                <div class="rounded-3xl border border-violet-300/20 bg-violet-400/10 p-5 shadow-xl backdrop-blur-md">
                    <div class="text-sm font-bold text-violet-200">売却済み</div>
                    <div class="mt-3 text-3xl font-black text-violet-100">{{ number_format($soldCount ?? 0) }}</div>
                </div>

                <div class="rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-5 shadow-xl backdrop-blur-md">
                    <div class="text-sm font-bold text-emerald-200">総売上</div>
                    <div class="mt-3 text-2xl font-black text-emerald-100">¥{{ number_format($totalSales ?? 0) }}</div>
                </div>

                <div class="rounded-3xl border border-orange-300/20 bg-orange-400/10 p-5 shadow-xl backdrop-blur-md">
                    <div class="text-sm font-bold text-orange-200">総利益</div>
                    <div class="mt-3 text-2xl font-black text-orange-100">¥{{ number_format($totalProfit ?? 0) }}</div>
                </div>
            </div>

            <div class="mb-8">
                <section class="rounded-3xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-2xl backdrop-blur-md">
                    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-xl font-black">{{ $displayYear }}年 月別売上・利益</h2>
                        <span class="text-xs font-bold text-cyan-300">sold_at / sold_price / profit</span>
                    </div>

                    <div class="h-96">
                        <canvas id="monthlySalesProfitLineChart"></canvas>
                    </div>
                </section>
            </div>

            

            <section class="mt-8 rounded-3xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-2xl backdrop-blur-md">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-black">最近更新した商品</h2>

                    <a href="{{ route('auction-items.index') }}" class="text-sm font-bold text-emerald-300 hover:text-emerald-200">
                        すべて見る →
                    </a>
                </div>

                @if (($recentItems ?? collect())->count() > 0)
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($recentItems as $item)
                            @php
                                $displayImagePath = $item->status === 'sold' && $item->sold_image_path
                                    ? $item->sold_image_path
                                    : $item->image_path;
                            @endphp

                            <a href="{{ route('auction-items.show', $item) }}" class="group overflow-hidden rounded-3xl border border-cyan-300/20 bg-white/10 shadow-xl transition hover:-translate-y-1 hover:bg-white/15">
                                <div class="aspect-[4/3] bg-slate-800">
                                    @if ($displayImagePath)
                                        <img src="{{ asset('storage/' . $displayImagePath) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm font-bold text-cyan-200">
                                            NO IMAGE
                                        </div>
                                    @endif
                                </div>

                                <div class="p-4">
                                    <div class="line-clamp-2 text-base font-black text-white">
                                        {{ $item->title }}
                                    </div>

                                    <div class="mt-2 text-xs font-bold text-cyan-300">
                                        管理ID：{{ $item->management_id }}
                                    </div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-slate-200">
                                            {{ $item->platform }}
                                        </span>

                                        @if ($item->status === 'sold')
                                            <span class="rounded-full bg-red-500/20 px-3 py-1 text-xs font-black text-red-200">
                                                SOLD
                                            </span>
                                        @else
                                            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-black text-emerald-200">
                                                出品中
                                            </span>
                                        @endif
                                    </div>

                                    @if ($item->status === 'sold')
                                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs font-bold text-slate-300">
                                            <div class="rounded-xl bg-slate-950/45 p-2">
                                                売値<br>¥{{ number_format($item->sold_price ?? 0) }}
                                            </div>
                                            <div class="rounded-xl bg-slate-950/45 p-2">
                                                利益<br>¥{{ number_format($item->profit ?? 0) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl bg-white/10 p-8 text-sm font-bold text-cyan-300">
                        最近更新した商品はありません。
                    </div>
                @endif
            </section>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const monthlyChartLabels = @json($monthlyChartLabels);
        const monthlyChartSales = @json($monthlyChartSales);
        const monthlyChartProfit = @json($monthlyChartProfit);

        const monthlySalesProfitLineCanvas = document.getElementById('monthlySalesProfitLineChart');

        const yenTickCallback = function(value) {
            return '¥' + Number(value).toLocaleString();
        };

        const yenTooltipLabel = function(context) {
            return context.dataset.label + ': ¥' + Number(context.raw).toLocaleString();
        };

        if (monthlySalesProfitLineCanvas) {
            new Chart(monthlySalesProfitLineCanvas, {
                type: 'line',
                data: {
                    labels: monthlyChartLabels,
                    datasets: [
                        {
                            label: '月別売上',
                            data: monthlyChartSales,
                            borderWidth: 3,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false
                        },
                        {
                            label: '月別利益',
                            data: monthlyChartProfit,
                            borderWidth: 3,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#e2e8f0',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: yenTooltipLabel
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#cbd5e1',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.08)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#cbd5e1',
                                callback: yenTickCallback,
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.08)'
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>