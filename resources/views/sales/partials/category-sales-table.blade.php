<div class="rounded-2xl border border-cyan-300/20 bg-slate-950/45 p-6 shadow-sm backdrop-blur-md">
    <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>

    @isset($description)
        <p class="mt-1 text-sm text-slate-300">{{ $description }}</p>
    @endisset

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-cyan-300/20 text-sm">
            <thead class="bg-cyan-400/10">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-200">ジャンル</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-200">SOLD件数</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-200">売上</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-200">仕入れ</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-200">手数料</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-200">送料</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-200">実利益</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-cyan-300/15 bg-slate-950/20">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium text-white">{{ $row['label'] }}</td>
                        <td class="px-4 py-3 text-right text-slate-100">{{ number_format($row['count']) }}件</td>
                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['sales']) }}</td>
                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['purchase']) }}</td>
                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['sales_fee']) }}</td>
                        <td class="px-4 py-3 text-right text-slate-100">¥{{ number_format($row['shipping_fee']) }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] < 0 ? 'text-red-400' : 'text-lime-300' }}">¥{{ number_format($row['profit']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center font-semibold text-slate-300">集計できるSOLD商品がありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
