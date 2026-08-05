<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {

        $userId = (int) Auth::id();
        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');
        $baseMonth = $this->baseMonth($request->query('month'));
        $periodStart = $baseMonth->copy()->subMonths(11)->startOfMonth();
        $periodEnd = $baseMonth->copy()->endOfMonth();

        $soldItems = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', $userId)
            ->where('status', AuctionItem::STATUS_SOLD)
            ->get();

        $graphSoldItems = $soldItems->filter(function ($item) use ($periodStart, $periodEnd, $hasSoldAt) {
            return $this->itemDate($item, $hasSoldAt)->betweenIncluded($periodStart, $periodEnd);
        });

        $monthlySales = collect();

        for ($month = $periodStart->copy(); $month->lte($periodEnd); $month->addMonth()) {
            $monthKey = $month->format('Y-m');
            $items = $graphSoldItems->filter(fn ($item) => $this->itemDate($item, $hasSoldAt)->format('Y-m') === $monthKey);

            $monthlySales->push($this->salesRow($monthKey, $items));
        }

        $platformNames = collect(AuctionItem::PLATFORMS)
            ->merge($soldItems->map(fn ($item) => $this->platformLabel($item)))
            ->unique()
            ->values();
        $platformSales = $platformNames->map(function ($platform) use ($soldItems) {
            $items = $soldItems->filter(fn ($item) => $this->platformLabel($item) === $platform);

            return $this->salesRow($platform, $items, 'platform');
        });
        $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $selectedMonthRow = $monthlySales->firstWhere('month', $baseMonth->format('Y-m')) ?? $this->salesRow($baseMonth->format('Y-m'), collect());
        $previousMonthRow = $monthlySales->firstWhere('month', $baseMonth->copy()->subMonth()->format('Y-m')) ?? $this->salesRow($baseMonth->copy()->subMonth()->format('Y-m'), collect());
        $monthlyInsights = $this->monthlyInsights(
            $selectedMonthRow,
            $previousMonthRow,
            $monthlySales->sortByDesc('profit')->first(),
            $monthlySales->sortBy('profit')->first()
        );
        $monthlyReport = $this->monthlyReport($selectedMonthRow, $previousMonthRow, $platformSales, $baseMonth);

        return view('sales.index', [
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'baseMonth' => $baseMonth,
            'previousMonth' => $baseMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $baseMonth->copy()->addMonth()->format('Y-m'),
            'totalSales' => $totalSales,
            'totalPurchase' => $soldItems->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
            'totalProfit' => $soldItems->sum(fn ($item) => $this->calculateItemProfit($item)),
            'totalSalesFee' => $soldItems->sum(fn ($item) => $this->calculateSalesFee($item)),
            'totalShippingFee' => $soldItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
            'soldCount' => $soldItems->count(),
            'sellingCount' => AuctionItem::query()->where('user_id', $userId)->where('status', AuctionItem::STATUS_SELLING)->count(),
            'platformSales' => $platformSales,
            'monthlySales' => $monthlySales,
            'monthlyChartLabels' => $monthlySales->pluck('month')->values(),
            'monthlyChartSales' => $monthlySales->pluck('sales')->values(),
            'monthlyChartProfit' => $monthlySales->pluck('profit')->values(),
            'platformChartLabels' => $platformSales->pluck('platform')->values(),
            'platformChartSales' => $platformSales->pluck('sales')->values(),
            'platformChartBreakdown' => $platformSales->map(fn ($row) => [
                'platform' => $row['platform'],
                'sales' => $row['sales'],
                'share' => $totalSales > 0 ? round(((int) $row['sales'] / $totalSales) * 100, 1) : 0.0,
            ])->values(),
            'platformRanking' => $this->addRankingAndShare(
                $platformSales->map(fn ($row) => [
                    'label' => $row['platform'],
                    'count' => $row['count'],
                    'sales' => $row['sales'],
                    'purchase' => $row['purchase'],
                    'sales_fee' => $row['sales_fee'],
                    'shipping_fee' => $row['shipping_fee'],
                    'profit' => $row['profit'],
                ]),
                $totalSales
            ),
            'selectedMonthRow' => $selectedMonthRow,
            'previousMonthRow' => $previousMonthRow,
            'monthlyInsights' => $monthlyInsights,
            'monthlyReport' => $monthlyReport,
        ]);
    }

    public function downloadCsv(): Response
    {

        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');
        $items = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', Auth::id())
            ->where('status', AuctionItem::STATUS_SOLD)
            ->orderByDesc($hasSoldAt ? 'sold_at' : 'updated_at')
            ->get();

        $csv = "\xEF\xBB\xBF";
        $csv .= "管理ID,タイトル,大ジャンル,小ジャンル,出品先,仕入れ値,売値,販売手数料率,販売手数料,送料,実利益,ステータス,SOLD日\n";

        foreach ($items as $item) {
            $salesFee = $this->calculateSalesFee($item);
            $soldDate = $this->itemDate($item, $hasSoldAt)->format('Y-m-d');
            $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
            $row = [
                $item->management_id ?? '',
                $item->title ?? '',
                $item->category?->parent?->name ?? '',
                $item->category?->name ?? '',
                $this->platformLabel($item),
                (int) ($item->purchase_price ?? 0),
                (int) ($item->sold_price ?? 0),
                rtrim(rtrim(number_format($salesFeeRate, 2), '0'), '.').'%',
                $salesFee,
                (int) ($item->shipping_fee ?? 0),
                $this->calculateItemProfit($item),
                'SOLD',
                $soldDate,
            ];

            $csv .= collect($row)->map(fn ($value) => $this->escapeCsvCell($value))->implode(',')."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="furugi_sales_'.now()->format('Ymd_His').'.csv"',
        ]);
    }

    public function downloadBackupCsv(): Response
    {

        $items = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->get();

        $csv = "\xEF\xBB\xBF";
        $csv .= "管理ID,商品タイトル,大ジャンル,小ジャンル,出品先,ステータス,仕入れ値,販売価格,販売手数料率,販売手数料,送料,実利益,SOLD日,商品画像URL,SOLD画像URL,コメント,作成日,更新日\n";

        foreach ($items as $item) {
            $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
            $row = [
                $item->management_id ?? '',
                $item->title ?? '',
                $item->category?->parent?->name ?? '',
                $item->category?->name ?? '',
                $this->platformLabel($item),
                $this->statusLabel((string) $item->status),
                (int) ($item->purchase_price ?? 0),
                (int) ($item->sold_price ?? 0),
                rtrim(rtrim(number_format($salesFeeRate, 2), '0'), '.').'%',
                $this->calculateSalesFee($item),
                (int) ($item->shipping_fee ?? 0),
                $this->calculateItemProfit($item),
                $item->sold_at ? Carbon::parse($item->sold_at)->format('Y-m-d') : '',
                $item->image_path ? asset('storage/'.$item->image_path) : '',
                $item->sold_image_path ? asset('storage/'.$item->sold_image_path) : '',
                $item->comment ?? '',
                $item->created_at ? Carbon::parse($item->created_at)->format('Y-m-d H:i:s') : '',
                $item->updated_at ? Carbon::parse($item->updated_at)->format('Y-m-d H:i:s') : '',
            ];

            $csv .= collect($row)->map(fn ($value) => $this->escapeCsvCell($value))->implode(',')."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="furugi_backup_'.now()->format('Ymd_His').'.csv"',
        ]);
    }

    public function downloadRestoreCsv(): Response
    {

        $items = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', Auth::id())
            ->orderBy('management_id')
            ->get();

        $csv = "\xEF\xBB\xBF";
        $csv .= "management_id,title,comment,platform,parent_category,category,purchase_price,sold_price,sales_fee_rate,shipping_fee,status,sold_at\n";

        foreach ($items as $item) {
            $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
            $row = [
                $item->management_id ?? '',
                $item->title ?? '',
                $item->comment ?? '',
                $this->platformLabel($item),
                $item->category?->parent?->name ?? '',
                $item->category?->name ?? '',
                (int) ($item->purchase_price ?? 0),
                (int) ($item->sold_price ?? 0),
                rtrim(rtrim(number_format($salesFeeRate, 2), '0'), '.'),
                (int) ($item->shipping_fee ?? 0),
                (string) ($item->status ?? AuctionItem::STATUS_SELLING),
                $item->sold_at ? Carbon::parse($item->sold_at)->format('Y-m-d') : '',
            ];

            $csv .= collect($row)->map(fn ($value) => $this->escapeCsvCell($value))->implode(',')."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="furugi_restore_import_'.now()->format('Ymd_His').'.csv"',
        ]);
    }

    public function downloadSellingCsv(): Response
    {
        $items = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', Auth::id())
            ->where('status', AuctionItem::STATUS_SELLING)
            ->orderBy('management_id')
            ->get();

        $csv = "\xEF\xBB\xBF";
        $csv .= "management_id,title,comment,platform,parent_category,category,purchase_price,sold_price,sales_fee_rate,shipping_fee,status,sold_at\n";

        foreach ($items as $item) {
            $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
            $row = [
                $item->management_id ?? '',
                $item->title ?? '',
                $item->comment ?? '',
                $this->platformLabel($item),
                $item->category?->parent?->name ?? '',
                $item->category?->name ?? '',
                (int) ($item->purchase_price ?? 0),
                (int) ($item->sold_price ?? 0),
                rtrim(rtrim(number_format($salesFeeRate, 2), '0'), '.'),
                (int) ($item->shipping_fee ?? 0),
                AuctionItem::STATUS_SELLING,
                '',
            ];

            $csv .= collect($row)->map(fn ($value) => $this->escapeCsvCell($value))->implode(',')."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="furugi_selling_import_'.now()->format('Ymd_His').'.csv"',
        ]);
    }

    private function baseMonth(mixed $selectedMonth): Carbon
    {
        if (is_string($selectedMonth) && $selectedMonth !== '') {
            try {
                return Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            } catch (\Throwable) {
                return Carbon::now()->startOfMonth();
            }
        }

        return Carbon::now()->startOfMonth();
    }

    private function salesRow(string $label, Collection $items, string $labelKey = 'month'): array
    {
        $count = $items->count();
        $sales = $items->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $profit = $items->sum(fn ($item) => $this->calculateItemProfit($item));

        return [
            $labelKey => $label,
            'count' => $count,
            'sales' => $sales,
            'purchase' => $items->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
            'sales_fee' => $items->sum(fn ($item) => $this->calculateSalesFee($item)),
            'shipping_fee' => $items->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
            'profit' => $profit,
            'profit_rate' => $sales > 0 ? round(($profit / $sales) * 100, 1) : 0.0,
            'average_profit' => $count > 0 ? (int) round($profit / $count) : 0,
        ];
    }

    private function itemDate(AuctionItem $item, bool $hasSoldAt): Carbon
    {
        return $hasSoldAt && $item->sold_at
            ? Carbon::parse($item->sold_at)
            : Carbon::parse($item->updated_at);
    }

    private function escapeCsvCell(mixed $value): string
    {
        $value = (string) $value;

        if ($value !== '' && preg_match('/^[=+\-@\t\r]/', $value) === 1) {
            $value = "'".$value;
        }

        return '"'.str_replace('"', '""', $value).'"';
    }

    private function calculateItemProfit(AuctionItem $item): int
    {
        return (int) ($item->sold_price ?? 0)
            - (int) ($item->purchase_price ?? 0)
            - $this->calculateSalesFee($item)
            - (int) ($item->shipping_fee ?? 0);
    }

    private function calculateSalesFee(AuctionItem $item): int
    {
        $storedSalesFee = (int) ($item->sales_fee ?? 0);

        if ($storedSalesFee > 0) {
            return $storedSalesFee;
        }

        return (int) round((int) ($item->sold_price ?? 0) * ((float) ($item->sales_fee_rate ?? 0) / 100));
    }

    private function addRankingAndShare(Collection $rows, int $totalSales): Collection
    {
        return $rows
            ->sortByDesc('sales')
            ->values()
            ->map(function (array $row, int $index) use ($totalSales) {
                $row['rank'] = $index + 1;
                $row['share'] = $totalSales > 0 ? round(($row['sales'] / $totalSales) * 100, 1) : 0.0;

                return $row;
            });
    }

    private function monthlyInsights(array $selectedMonthRow, array $previousMonthRow, ?array $bestProfitMonth, ?array $worstProfitMonth): array
    {
        $profitDiff = (int) $selectedMonthRow['profit'] - (int) $previousMonthRow['profit'];
        $salesDiff = (int) $selectedMonthRow['sales'] - (int) $previousMonthRow['sales'];
        $countDiff = (int) $selectedMonthRow['count'] - (int) $previousMonthRow['count'];

        return [
            'profit_diff' => $profitDiff,
            'sales_diff' => $salesDiff,
            'count_diff' => $countDiff,
            'best_profit_month' => $bestProfitMonth,
            'worst_profit_month' => $worstProfitMonth,
            'message' => $this->monthlyInsightMessage($profitDiff, (float) $selectedMonthRow['profit_rate']),
        ];
    }

    private function monthlyInsightMessage(int $profitDiff, float $profitRate): string
    {
        if ($profitDiff > 0 && $profitRate >= 25) {
            return '前月より利益が伸びています。利益率も高いため、同じジャンルや出品先の商品を強化する価値があります。';
        }

        if ($profitDiff > 0) {
            return '前月より利益が伸びています。手数料と送料を見直すと、さらに利益率を上げられます。';
        }

        if ($profitRate < 15) {
            return '利益率が低めです。仕入れ上限、送料、値下げ幅を見直してください。';
        }

        return '大きな悪化はありません。SOLD件数と在庫の滞留を見ながら、次の仕入れ量を調整してください。';
    }

    private function monthlyReport(array $selectedMonthRow, array $previousMonthRow, Collection $platformSales, Carbon $baseMonth): array
    {
        $bestPlatform = $platformSales
            ->filter(fn ($row) => (int) $row['sales'] > 0)
            ->sortByDesc('profit')
            ->first();
        $profitDiff = (int) $selectedMonthRow['profit'] - (int) $previousMonthRow['profit'];
        $salesDiff = (int) $selectedMonthRow['sales'] - (int) $previousMonthRow['sales'];
        $countDiff = (int) $selectedMonthRow['count'] - (int) $previousMonthRow['count'];
        $actions = [];

        if ((float) $selectedMonthRow['profit_rate'] < 20) {
            $actions[] = '利益率が20%未満です。仕入れ上限、送料、販売手数料を見直してください。';
        }

        if ($profitDiff < 0) {
            $actions[] = '前月より利益が落ちています。売れ残り在庫の値下げ候補と高利益ジャンルを確認してください。';
        }

        if ($countDiff <= 0 && $salesDiff <= 0) {
            $actions[] = '販売件数と売上が伸びていません。写真差し替え、再出品、説明文改善を優先してください。';
        }

        if ($actions === []) {
            $actions[] = '利益は安定しています。利益率の高い出品先とジャンルへ仕入れを寄せると、次月も伸ばしやすくなります。';
        }

        return [
            'title' => $baseMonth->format('Y年n月').' 月次利益レポート',
            'best_platform' => $bestPlatform,
            'profit_diff' => $profitDiff,
            'sales_diff' => $salesDiff,
            'count_diff' => $countDiff,
            'actions' => $actions,
        ];
    }

    private function platformLabel(AuctionItem $item): string
    {
        $platform = AuctionItem::normalizePlatformName($item->platform);

        return $platform !== '' ? $platform : '未設定';
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            AuctionItem::STATUS_SOLD => 'SOLD',
            AuctionItem::STATUS_DRAFT => '下書き',
            default => '出品中',
        };
    }
}
