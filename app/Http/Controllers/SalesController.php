<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! (Auth::user()?->isPremium() ?? false)) {
            return redirect()
                ->route('subscriptions.index')
                ->with('error', '売上管理はPremium限定機能です。');
        }

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
        ]);
    }

    public function downloadCsv(): Response
    {
        if (! (Auth::user()?->isPremium() ?? false)) {
            abort(403);
        }

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
        return [
            $labelKey => $label,
            'count' => $items->count(),
            'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
            'purchase' => $items->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
            'sales_fee' => $items->sum(fn ($item) => $this->calculateSalesFee($item)),
            'shipping_fee' => $items->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
            'profit' => $items->sum(fn ($item) => $this->calculateItemProfit($item)),
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

    private function platformLabel(AuctionItem $item): string
    {
        $platform = trim((string) $item->platform);

        return $platform !== '' ? $platform : '未設定';
    }
}
