<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();
        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');

        $soldItems = AuctionItem::query()
            ->where('status', 'sold')
            ->orderByDesc($hasSoldAt ? 'sold_at' : 'updated_at')
            ->get();

        $sellingCount = AuctionItem::query()
            ->where('status', 'selling')
            ->count();

        $soldCount = $soldItems->count();

        $totalSales = $soldItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));

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

        $thisMonthItems = $soldItems->filter(function ($item) use ($now, $hasSoldAt) {
            $date = $hasSoldAt && $item->sold_at
                ? Carbon::parse($item->sold_at)
                : Carbon::parse($item->updated_at);

            return $date->isSameMonth($now);
        });

        $thisMonthSales = $thisMonthItems->sum(fn ($item) => (int) ($item->sold_price ?? 0));

        $thisMonthProfit = $thisMonthItems->sum(function ($item) {
            return $this->calculateItemProfit($item);
        });

        $thisMonthSalesFee = $thisMonthItems->sum(function ($item) {
            return $this->calculateSalesFee(
                (int) ($item->sold_price ?? 0),
                (float) ($item->sales_fee_rate ?? 0),
                (int) ($item->sales_fee ?? 0)
            );
        });

        $thisMonthShippingFee = $thisMonthItems->sum(fn ($item) => (int) ($item->shipping_fee ?? 0));

        $platformSales = $soldItems
            ->groupBy(fn ($item) => $item->platform ?: '未設定')
            ->map(function ($items, $platform) {
                return [
                    'platform' => $platform,
                    'count' => $items->count(),
                    'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
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

        $monthlySales = $soldItems
            ->groupBy(function ($item) use ($hasSoldAt) {
                $date = $hasSoldAt && $item->sold_at
                    ? Carbon::parse($item->sold_at)
                    : Carbon::parse($item->updated_at);

                return $date->format('Y-m');
            })
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'count' => $items->count(),
                    'sales' => $items->sum(fn ($item) => (int) ($item->sold_price ?? 0)),
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
            ->sortByDesc('month')
            ->values();

        $monthlyChartLabels = $monthlySales->sortBy('month')->pluck('month')->values();
        $monthlyChartSales = $monthlySales->sortBy('month')->pluck('sales')->values();
        $monthlyChartProfit = $monthlySales->sortBy('month')->pluck('profit')->values();

        $platformChartLabels = $platformSales->pluck('platform')->values();
        $platformChartSales = $platformSales->pluck('sales')->values();

        return view('sales.index', compact(
            'totalSales',
            'totalProfit',
            'totalSalesFee',
            'totalShippingFee',
            'thisMonthSales',
            'thisMonthProfit',
            'thisMonthSalesFee',
            'thisMonthShippingFee',
            'soldCount',
            'sellingCount',
            'platformSales',
            'monthlySales',
            'monthlyChartLabels',
            'monthlyChartSales',
            'monthlyChartProfit',
            'platformChartLabels',
            'platformChartSales'
        ));
    }

    public function downloadCsv(): Response
    {
        $hasSoldAt = Schema::hasColumn('auction_items', 'sold_at');

        $items = AuctionItem::query()
            ->where('status', 'sold')
            ->orderByDesc($hasSoldAt ? 'sold_at' : 'updated_at')
            ->get();

        $csv = "\xEF\xBB\xBF";
        $csv .= "管理ID,タイトル,出品先,仕入れ値,売値,販売手数料率,販売手数料,送料,実利益,ステータス,SOLD日\n";

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
                ->map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"')
                ->implode(',')."\n";
        }

        $filename = 'furugi_sales_'.now()->format('Ymd_His').'.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
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
}
