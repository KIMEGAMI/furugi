<?php

namespace App\Http\Controllers;

use App\Services\CategorySalesAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategorySalesController extends Controller
{
    public function __construct(
        private readonly CategorySalesAnalysisService $categorySalesAnalysis
    ) {
    }

    public function index(Request $request): View
    {
        $month = $request->query('month');
        $analysis = $this->categorySalesAnalysis->analyze((int) Auth::id(), [
            'month' => $month,
        ]);

        return view('category_sales.index', [
            ...$analysis,
            'month' => $analysis['selectedMonth']?->format('Y-m') ?? '',
        ]);
    }
}
