<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');

        $selectedMonth = $request->query('month');

        if ($selectedMonth) {
            try {
                $baseMonth = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            } catch (\Throwable $e) {
                $baseMonth = Carbon::now()->startOfMonth();
            }
        } else {
            $baseMonth = Carbon::now()->startOfMonth();
        }

        $periodStart = $baseMonth->copy()->subMonths(11)->startOfMonth();
        $periodEnd = $baseMonth->copy()->endOfMonth();

        $previousMonth = $baseMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $baseMonth->copy()->addMonth()->format('Y-m');

        $soldItems = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', $userId)
            ->where('status', 'sold')
            ->get();

        $graphSoldItems = $soldItems->filter(function ($item) use ($periodStart, $periodEnd, $hasSoldAt) {
            $date = $hasSoldAt && $item->sold_at
                ? Carbon::parse($item->sold_at)
                : Carbon::parse($item->updated_at);

            return $date->betweenIncluded($periodStart, $periodEnd);
        });

        $sellingCount = AuctionItem::query()
            ->where('user_id', $userId)
            ->where('status', 'selling')
            ->count();

        $soldCount = $soldItems->count();

        $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));
        $totalPurchase = $soldItems->sum(fn ($item) => (int) ($item->purchase_price ?? 0));

        $totalProfit = $soldItems->sum(function ($item) {
            return $this->calculateItemProfit($item);
        });

        $totalSalesFee = $soldItems->sum(function ($item) {
            return $this->calculateSalesFee(
                (int) ($item->sold_price ?? 0),
                (float) ($item->sales_fee_rate ?? 0),
                (int) ($item->sales_fee ?? 0)
            );
        });

        $totalShippingFee = $soldItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0));

        $monthlySales = collect();

        for ($month = $periodStart->copy(); $month->lte($periodEnd); $month->addMonth()) {
            $monthKey = $month->format('Y-m');

            $items = $graphSoldItems->filter(function ($item) use ($monthKey, $hasSoldAt) {
                $date = $hasSoldAt && $item->sold_at
                    ? Carbon::parse($item->sold_at)
                    : Carbon::parse($item->updated_at);

                return $date->format('Y-m') === $monthKey;
            });

            $monthlySales->push([
                'month' => $monthKey,
                'count' => $items->count(),
                'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
                'purchase' => $items->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
                'sales_fee' => $items->sum(function ($item) {
                    return $this->calculateSalesFee(
                        (int) ($item->sold_price ?? 0),
                        (float) ($item->sales_fee_rate ?? 0),
                        (int) ($item->sales_fee ?? 0)
                    );
                }),
                'shipping_fee' => $items->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
                'profit' => $items->sum(fn ($item) => $this->calculateItemProfit($item)),
            ]);
        }

        $platformNames = ['メルカリ', 'ヤフオク', 'ラクマ', 'PayPayフリマ', 'その他'];

        $platformSales = collect($platformNames)->map(function ($platform) use ($soldItems) {
            $items = $soldItems->filter(fn ($item) => ($item->platform ?: 'その他') === $platform);

            return [
                'platform' => $platform,
                'count' => $items->count(),
                'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
                'purchase' => $items->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
                'sales_fee' => $items->sum(function ($item) {
                    return $this->calculateSalesFee(
                        (int) ($item->sold_price ?? 0),
                        (float) ($item->sales_fee_rate ?? 0),
                        (int) ($item->sales_fee ?? 0)
                    );
                }),
                'shipping_fee' => $items->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
                'profit' => $items->sum(fn ($item) => $this->calculateItemProfit($item)),
            ];
        });

        $monthlyChartLabels = $monthlySales->pluck('month')->values();
        $monthlyChartSales = $monthlySales->pluck('sales')->values();
        $monthlyChartProfit = $monthlySales->pluck('profit')->values();

        $platformChartLabels = $platformSales->pluck('platform')->values();
        $platformChartSales = $platformSales->pluck('sales')->values();
        $parentCategorySales = $this->summarizeSalesGroups(
            $soldItems->groupBy(fn ($item) => $item->category?->parent?->name ?? '未設定')
        );
        $childCategorySales = $this->summarizeSalesGroups(
            $soldItems->groupBy(fn ($item) => $this->categoryLabel($item))
        );
        $selectedMonthItems = $soldItems->filter(function ($item) use ($baseMonth, $hasSoldAt) {
            $date = $hasSoldAt && $item->sold_at
                ? Carbon::parse($item->sold_at)
                : Carbon::parse($item->updated_at);

            return $date->format('Y-m') === $baseMonth->format('Y-m');
        });
        $selectedMonthCategorySales = $this->summarizeSalesGroups(
            $selectedMonthItems->groupBy(fn ($item) => $this->categoryLabel($item))
        );

        return view('sales.index', compact(
            'periodStart',
            'periodEnd',
            'baseMonth',
            'previousMonth',
            'nextMonth',
            'totalSales',
            'totalPurchase',
            'totalProfit',
            'totalSalesFee',
            'totalShippingFee',
            'soldCount',
            'sellingCount',
            'platformSales',
            'monthlySales',
            'monthlyChartLabels',
            'monthlyChartSales',
            'monthlyChartProfit',
            'platformChartLabels',
            'platformChartSales',
            'parentCategorySales',
            'childCategorySales',
            'selectedMonthCategorySales'
        ));
    }

    public function downloadCsv(): Response
    {
        $userId = Auth::id();
        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');

        $items = AuctionItem::query()
            ->with(['category.parent'])
            ->where('user_id', $userId)
            ->where('status', 'sold')
            ->orderByDesc($hasSoldAt ? 'sold_at' : 'updated_at')
            ->get();

        $csv = "\xEF\xBB\xBF";
        $csv .= "管理ID,タイトル,大ジャンル,小ジャンル,出品先,仕入れ値,売値,販売手数料率,販売手数料,送料,実利益,ステータス,SOLD日\n";

        foreach ($items as $item) {
            $purchasePrice = (int) ($item->purchase_price ?? 0);
            $soldPrice = (int) ($item->sold_price ?? 0);
            $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
            $salesFee = $this->calculateSalesFee(
                $soldPrice,
                $salesFeeRate,
                (int) ($item->sales_fee ?? 0)
            );
            $shippingFee = (int) ($item->shipping_fee ?? 0);
            $profit = $soldPrice - $purchasePrice - $salesFee - $shippingFee;

            $soldDate = $hasSoldAt && $item->sold_at
                ? Carbon::parse($item->sold_at)->format('Y-m-d')
                : Carbon::parse($item->updated_at)->format('Y-m-d');

            $row = [
                $item->management_id ?? '',
                $item->title ?? '',
                $item->category?->parent?->name ?? '',
                $item->category?->name ?? '',
                $item->platform ?? '未設定',
                $purchasePrice,
                $soldPrice,
                rtrim(rtrim(number_format($salesFeeRate, 2), '0'), '.').'%',
                $salesFee,
                $shippingFee,
                $profit,
                'SOLD',
                $soldDate,
            ];

            $csv .= collect($row)
                ->map(fn ($value) => $this->escapeCsvCell($value))
                ->implode(',')."\n";
        }

        $filename = 'furugi_sales_'.now()->format('Ymd_His').'.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
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
        $soldPrice = (int) ($item->sold_price ?? 0);
        $purchasePrice = (int) ($item->purchase_price ?? 0);
        $salesFeeRate = (float) ($item->sales_fee_rate ?? 0);
        $salesFee = $this->calculateSalesFee(
            $soldPrice,
            $salesFeeRate,
            (int) ($item->sales_fee ?? 0)
        );
        $shippingFee = (int) ($item->shipping_fee ?? 0);

        return $soldPrice - $purchasePrice - $salesFee - $shippingFee;
    }

    private function calculateSalesFee(int $soldPrice, float $salesFeeRate, int $storedSalesFee): int
    {
        if ($storedSalesFee > 0) {
            return $storedSalesFee;
        }

        return (int) round($soldPrice * ($salesFeeRate / 100));
    }

    private function summarizeSalesGroups($groups)
    {
        return $groups
            ->map(function ($items, $label) {
                return [
                    'label' => $label,
                    'count' => $items->count(),
                    'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
                    'purchase' => $items->sum(fn ($item) => (int) ($item->purchase_price ?? 0)),
                    'sales_fee' => $items->sum(function ($item) {
                        return $this->calculateSalesFee(
                            (int) ($item->sold_price ?? 0),
                            (float) ($item->sales_fee_rate ?? 0),
                            (int) ($item->sales_fee ?? 0)
                        );
                    }),
                    'shipping_fee' => $items->sum(fn ($item) => (int) ($item->shipping_fee ?? 0)),
                    'profit' => $items->sum(fn ($item) => $this->calculateItemProfit($item)),
                ];
            })
            ->sortByDesc('sales')
            ->values();
    }

    private function categoryLabel(AuctionItem $item): string
    {
        if (! $item->category) {
            return '未設定';
        }

        if (! $item->category->parent) {
            return $item->category->name;
        }

        return $item->category->parent->name.' / '.$item->category->name;
    }
}
