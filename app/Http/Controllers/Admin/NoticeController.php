<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:'.Notice::TITLE_MAX_LENGTH],
            'body' => ['required', 'string', 'max:'.Notice::BODY_MAX_LENGTH],
        ]);

        Notice::query()->create([
            'user_id' => $request->user()->id,
            'title' => trim((string) $validated['title']),
            'body' => trim((string) $validated['body']),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.maintenance.index')
            ->with('status', 'お知らせを投稿しました。');
    }
}
