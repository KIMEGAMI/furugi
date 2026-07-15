<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MarketingPageController extends Controller
{
    public function features(): View
    {
        return view('marketing.features');
    }

    public function pricing(): View
    {
        return view('marketing.pricing');
    }

    public function useCases(): View
    {
        return view('marketing.use-cases');
    }
}
