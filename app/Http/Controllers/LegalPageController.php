<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function terms(): View
    {
        return view('legal.terms');
    }

    public function privacy(): View
    {
        return view('legal.privacy');
    }

    public function commercial(): View
    {
        return view('legal.commercial');
    }

    public function faq(): View
    {
        return view('legal.faq');
    }

    public function contact(): View
    {
        return view('legal.contact');
    }
}
