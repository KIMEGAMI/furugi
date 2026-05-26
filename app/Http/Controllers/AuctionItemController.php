<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuctionItemController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = AuctionItem::where('user_id', Auth::id());

        if (in_array($status, ['selling', 'sold', 'draft'])) {
            $query->where('status', $status);
        }

        $auctionItems = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('auction_items.index', [
            'auctionItems' => $auctionItems,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('auction_items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'management_id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
            'sold_price' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('auction-items', 'public');
        }

        AuctionItem::create([
            'user_id' => Auth::id(),
            'management_id' => $validated['management_id'],
            'title' => $validated['title'],
            'comment' => $validated['comment'] ?? null,
            'image_path' => $imagePath,
            'sold_image_path' => null,
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'sold_price' => $validated['sold_price'] ?? 0,
            'profit' => 0,
            'sold_at' => null,
            'status' => 'selling',
        ]);

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品を登録しました。');
    }

    public function show(AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        return view('auction_items.show', compact('auctionItem'));
    }

    public function edit(AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        return view('auction_items.edit', compact('auctionItem'));
    }

    public function update(Request $request, AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        $validated = $request->validate([
            'management_id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
            'sold_price' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            if ($auctionItem->image_path) {
                Storage::disk('public')->delete($auctionItem->image_path);
            }

            if ($auctionItem->sold_image_path) {
                Storage::disk('public')->delete($auctionItem->sold_image_path);
                $auctionItem->sold_image_path = null;
            }

            $auctionItem->image_path = $request->file('image')->store('auction-items', 'public');
        }

        $auctionItem->management_id = $validated['management_id'];
        $auctionItem->title = $validated['title'];
        $auctionItem->comment = $validated['comment'] ?? null;
        $auctionItem->purchase_price = $validated['purchase_price'] ?? 0;
        $auctionItem->sold_price = $validated['sold_price'] ?? 0;

        if ($auctionItem->status === 'sold') {
            $auctionItem->profit = $auctionItem->sold_price - $auctionItem->purchase_price;
        }

        $auctionItem->save();

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品を更新しました。');
    }

    public function markAsSold(AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        if (is_null($auctionItem->sold_price) || $auctionItem->sold_price <= 0) {
            return redirect()
                ->route('auction-items.edit', $auctionItem)
                ->with('success', 'SOLDにする前に売値を入力してください。');
        }

        $auctionItem->status = 'sold';
        $auctionItem->profit = $auctionItem->sold_price - $auctionItem->purchase_price;
        $auctionItem->sold_at = now();

        $auctionItem->save();

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品をSOLD化しました。');
    }

    public function destroy(AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        if ($auctionItem->image_path) {
            Storage::disk('public')->delete($auctionItem->image_path);
        }

        if ($auctionItem->sold_image_path) {
            Storage::disk('public')->delete($auctionItem->sold_image_path);
        }

        $auctionItem->delete();

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品を削除しました。');
    }

    private function authorizeOwner(AuctionItem $auctionItem): void
    {
        if ($auctionItem->user_id !== Auth::id()) {
            abort(403);
        }
    }
}