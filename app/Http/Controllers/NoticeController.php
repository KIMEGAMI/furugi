<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        return view('notices.index', [
            'notices' => Notice::query()
                ->published()
                ->latest('published_at')
                ->paginate(Notice::PAGE_SIZE),
        ]);
    }

    public function show(Notice $notice): View
    {
        abort_if($notice->published_at === null || $notice->published_at->isFuture(), 404);

        return view('notices.show', [
            'notice' => $notice,
        ]);
    }
}
