<div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
    <h3 class="text-lg font-semibold text-white">ジャンル x 出品先 クロス分析</h3>

    <p class="mt-1 text-sm text-slate-300">
        大ジャンルごとに、各出品先の売上・SOLD件数・実利益を比較します。
    </p>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-max divide-y divide-cyan-300/20 text-sm">
            <thead class="bg-cyan-400/10">
                <tr>
                    <th class="sticky left-0 z-10 min-w-36 bg-slate-900 px-4 py-3 text-left font-semibold text-slate-200">大ジャンル</th>
                    @foreach ($platformNames as $platformName)
                        <th class="min-w-44 px-4 py-3 text-right font-semibold text-slate-200">{{ $platformName }}</th>
                    @endforeach
                    <th class="min-w-44 px-4 py-3 text-right font-semibold text-slate-200">合計</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-cyan-300/15 bg-slate-950/20">
                @forelse ($rows as $row)
                    <tr>
                        <td class="sticky left-0 z-10 bg-slate-950 px-4 py-3 font-black text-white">{{ $row['category'] }}</td>
                        @foreach ($platformNames as $platformName)
                            @php
                                $cell = $row['platforms']->get($platformName, ['count' => 0, 'sales' => 0, 'profit' => 0]);
                            @endphp
                            <td class="px-4 py-3 text-right">
                                <p class="font-semibold text-white">¥{{ number_format($cell['sales']) }}</p>
                                <p class="mt-1 text-xs text-slate-300">{{ number_format($cell['count']) }}件 / 利益 ¥{{ number_format($cell['profit']) }}</p>
                            </td>
                        @endforeach
                        <td class="bg-cyan-400/5 px-4 py-3 text-right">
                            <p class="font-black text-cyan-100">¥{{ number_format($row['sales']) }}</p>
                            <p class="mt-1 text-xs text-slate-200">{{ number_format($row['count']) }}件 / 利益 ¥{{ number_format($row['profit']) }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $platformNames->count() + 2 }}" class="px-4 py-8 text-center font-semibold text-slate-300">集計できるSOLD商品がありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
