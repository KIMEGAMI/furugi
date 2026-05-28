<?php

namespace App\Http\Controllers;

use App\Models\AuctionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuctionItemController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $platform = $request->get('platform');
        $keyword = $request->get('keyword');

        $query = AuctionItem::where('user_id', Auth::id());

        if (in_array($status, ['selling', 'sold', 'draft'], true)) {
            $query->where('status', $status);
        }

        if (in_array($platform, ['ヤフオク', 'メルカリ', 'ラクマ', 'PayPayフリマ', 'その他'], true)) {
            $query->where('platform', $platform);
        }

        if (! empty($keyword)) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('management_id', 'like', '%'.$keyword.'%')
                    ->orWhere('title', 'like', '%'.$keyword.'%')
                    ->orWhere('comment', 'like', '%'.$keyword.'%');
            });
        }

        $auctionItems = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('auction_items.index', [
            'auctionItems' => $auctionItems,
            'status' => $status,
            'platform' => $platform,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        return view('auction_items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'management_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('auction_items', 'management_id'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'platform' => ['required', 'string'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
            'sold_price' => ['nullable', 'integer', 'min:0'],
            'shipping_fee' => ['nullable', 'integer', 'min:0'],
            'sales_fee_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'image' => ['nullable', 'image', 'max:10240'],
        ], [
            'management_id.unique' => 'この管理IDは既に登録されています。別の管理IDを入力してください。',
        ]);

        $platform = $validated['platform'];
        $soldPrice = (int) ($validated['sold_price'] ?? 0);
        $purchasePrice = (int) ($validated['purchase_price'] ?? 0);
        $shippingFee = (int) ($validated['shipping_fee'] ?? 0);
        $salesFeeRate = (float) ($validated['sales_fee_rate'] ?? $this->defaultSalesFeeRate($platform));
        $salesFee = $this->calculateSalesFee($soldPrice, $salesFeeRate);
        $profit = 0;

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('auction-items', 'public');
        }

        AuctionItem::create([
            'user_id' => Auth::id(),
            'management_id' => $validated['management_id'],
            'title' => $validated['title'],
            'comment' => $validated['comment'] ?? null,
            'platform' => $platform,
            'image_path' => $imagePath,
            'sold_image_path' => null,
            'purchase_price' => $purchasePrice,
            'sold_price' => $soldPrice,
            'sales_fee_rate' => $salesFeeRate,
            'sales_fee' => $salesFee,
            'shipping_fee' => $shippingFee,
            'profit' => $profit,
            'sold_at' => null,
            'status' => 'selling',
        ]);

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品を登録しました。');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'csv_file.required' => 'CSVファイルを選択してください。',
            'csv_file.mimes' => 'CSVファイルを選択してください。',
        ]);

        $filePath = $request->file('csv_file')->getRealPath();

        if (! $filePath || ! file_exists($filePath)) {
            return redirect()
                ->route('auction-items.index')
                ->with('error', 'CSVファイルを読み込めませんでした。');
        }

        $csvText = file_get_contents($filePath);

        if ($csvText === false || trim($csvText) === '') {
            return redirect()
                ->route('auction-items.index')
                ->with('error', 'CSVファイルが空です。');
        }

        $encoding = mb_detect_encoding($csvText, ['UTF-8', 'SJIS-win', 'SJIS', 'CP932', 'EUC-JP'], true);

        if ($encoding && $encoding !== 'UTF-8') {
            $csvText = mb_convert_encoding($csvText, 'UTF-8', $encoding);
        }

        $csvText = preg_replace('/^\xEF\xBB\xBF/', '', $csvText);

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csvText);
        rewind($handle);

        $headers = fgetcsv($handle);

        if (! $headers) {
            fclose($handle);

            return redirect()
                ->route('auction-items.index')
                ->with('error', 'CSVのヘッダー行を読み込めませんでした。');
        }

        $headers = array_map(function ($header) {
            return trim((string) $header);
        }, $headers);

        $requiredHeaders = ['management_id', 'title'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (! empty($missingHeaders)) {
            fclose($handle);

            return redirect()
                ->route('auction-items.index')
                ->with('error', 'CSVには management_id と title のヘッダーが必要です。');
        }

        $importedCount = 0;
        $skippedCount = 0;
        $lineNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, array_slice($row, 0, count($headers)));

            if (! $data) {
                $skippedCount++;

                continue;
            }

            $managementId = trim((string) ($data['management_id'] ?? ''));
            $title = trim((string) ($data['title'] ?? ''));

            if ($managementId === '' || $title === '') {
                $skippedCount++;

                continue;
            }

            if (AuctionItem::where('management_id', $managementId)->exists()) {
                $skippedCount++;

                continue;
            }

            $platform = trim((string) ($data['platform'] ?? 'その他'));

            if (! in_array($platform, ['ヤフオク', 'メルカリ', 'ラクマ', 'PayPayフリマ', 'その他'], true)) {
                $platform = 'その他';
            }

            $purchasePrice = max(0, (int) ($data['purchase_price'] ?? 0));
            $soldPrice = max(0, (int) ($data['sold_price'] ?? 0));
            $shippingFee = max(0, (int) ($data['shipping_fee'] ?? 0));
            $salesFeeRate = isset($data['sales_fee_rate']) && $data['sales_fee_rate'] !== ''
                ? max(0, min(100, (float) $data['sales_fee_rate']))
                : $this->defaultSalesFeeRate($platform);

            $salesFee = $this->calculateSalesFee($soldPrice, $salesFeeRate);

            $status = trim((string) ($data['status'] ?? 'selling'));

            if (! in_array($status, ['selling', 'sold', 'draft'], true)) {
                $status = 'selling';
            }

            $profit = $status === 'sold'
                ? $this->calculateProfit($soldPrice, $purchasePrice, $salesFee, $shippingFee)
                : 0;

            AuctionItem::create([
                'user_id' => Auth::id(),
                'management_id' => $managementId,
                'title' => $title,
                'comment' => trim((string) ($data['comment'] ?? '')) ?: null,
                'platform' => $platform,
                'image_path' => null,
                'sold_image_path' => null,
                'purchase_price' => $purchasePrice,
                'sold_price' => $soldPrice,
                'sales_fee_rate' => $salesFeeRate,
                'sales_fee' => $salesFee,
                'shipping_fee' => $shippingFee,
                'profit' => $profit,
                'sold_at' => $status === 'sold' ? now() : null,
                'status' => $status,
            ]);

            $importedCount++;
        }

        fclose($handle);

        return redirect()
            ->route('auction-items.index')
            ->with('success', "CSVインポートが完了しました。登録 {$importedCount} 件 / スキップ {$skippedCount} 件");
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
            'management_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('auction_items', 'management_id')->ignore($auctionItem->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'platform' => ['required', 'string'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
            'sold_price' => ['nullable', 'integer', 'min:0'],
            'shipping_fee' => ['nullable', 'integer', 'min:0'],
            'sales_fee_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'image' => ['nullable', 'image', 'max:10240'],
        ], [
            'management_id.unique' => 'この管理IDは既に登録されています。別の管理IDを入力してください。',
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
        $auctionItem->platform = $validated['platform'];
        $auctionItem->purchase_price = (int) ($validated['purchase_price'] ?? 0);
        $auctionItem->sold_price = (int) ($validated['sold_price'] ?? 0);
        $auctionItem->shipping_fee = (int) ($validated['shipping_fee'] ?? 0);
        $auctionItem->sales_fee_rate = (float) ($validated['sales_fee_rate'] ?? $this->defaultSalesFeeRate($auctionItem->platform));
        $auctionItem->sales_fee = $this->calculateSalesFee($auctionItem->sold_price, $auctionItem->sales_fee_rate);

        if ($auctionItem->status === 'sold') {
            $auctionItem->profit = $this->calculateProfit(
                $auctionItem->sold_price,
                $auctionItem->purchase_price,
                $auctionItem->sales_fee,
                $auctionItem->shipping_fee
            );

            if ($auctionItem->image_path) {
                $auctionItem->sold_image_path = $this->createSoldImage(
                    $auctionItem->image_path,
                    $auctionItem->sold_image_path
                );
            }
        }

        $auctionItem->save();

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品を更新しました。');
    }

    public function markAsSold(AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        $auctionItem->status = 'sold';
        $auctionItem->sales_fee_rate = (float) ($auctionItem->sales_fee_rate ?: $this->defaultSalesFeeRate($auctionItem->platform));
        $auctionItem->sales_fee = $this->calculateSalesFee((int) $auctionItem->sold_price, (float) $auctionItem->sales_fee_rate);
        $auctionItem->profit = $this->calculateProfit(
            (int) $auctionItem->sold_price,
            (int) $auctionItem->purchase_price,
            (int) $auctionItem->sales_fee,
            (int) $auctionItem->shipping_fee
        );
        $auctionItem->sold_at = now();

        if ($auctionItem->image_path) {
            $auctionItem->sold_image_path = $this->createSoldImage(
                $auctionItem->image_path,
                $auctionItem->sold_image_path
            );
        }

        $auctionItem->save();

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品をSOLD化しました。');
    }

    public function markAsSelling(AuctionItem $auctionItem)
    {
        $this->authorizeOwner($auctionItem);

        if ($auctionItem->sold_image_path) {
            Storage::disk('public')->delete($auctionItem->sold_image_path);
        }

        $auctionItem->status = 'selling';
        $auctionItem->sold_at = null;
        $auctionItem->profit = 0;
        $auctionItem->sold_image_path = null;

        $auctionItem->save();

        return redirect()
            ->route('auction-items.index')
            ->with('success', '商品を出品中へ戻しました。');
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

    private function defaultSalesFeeRate(string $platform): float
    {
        return match ($platform) {
            'ヤフオク' => 10.0,
            'メルカリ' => 10.0,
            'ラクマ' => 10.0,
            'PayPayフリマ' => 5.0,
            default => 0.0,
        };
    }

    private function calculateSalesFee(int $soldPrice, float $salesFeeRate): int
    {
        return (int) round($soldPrice * ($salesFeeRate / 100));
    }

    private function calculateProfit(int $soldPrice, int $purchasePrice, int $salesFee, int $shippingFee): int
    {
        return $soldPrice - $purchasePrice - $salesFee - $shippingFee;
    }

    private function createSoldImage(string $imagePath, ?string $oldSoldImagePath = null): ?string
    {
        if (
            ! function_exists('imagecreatefromjpeg') ||
            ! function_exists('imagejpeg') ||
            ! function_exists('imagesx') ||
            ! function_exists('imagesy')
        ) {
            return null;
        }

        if ($oldSoldImagePath) {
            Storage::disk('public')->delete($oldSoldImagePath);
        }

        $fullPath = Storage::disk('public')->path($imagePath);

        if (! file_exists($fullPath)) {
            return null;
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'jpg', 'jpeg' => \imagecreatefromjpeg($fullPath),
            'png' => function_exists('imagecreatefrompng') ? \imagecreatefrompng($fullPath) : null,
            'webp' => function_exists('imagecreatefromwebp') ? \imagecreatefromwebp($fullPath) : null,
            'gif' => function_exists('imagecreatefromgif') ? \imagecreatefromgif($fullPath) : null,
            default => null,
        };

        if (! $image) {
            return null;
        }

        $width = \imagesx($image);
        $height = \imagesy($image);

        \imagealphablending($image, true);
        \imagesavealpha($image, true);

        $overlay = \imagecolorallocatealpha($image, 220, 38, 38, 25);
        \imagefilledrectangle($image, 0, (int) ($height * 0.36), $width, (int) ($height * 0.64), $overlay);

        $white = \imagecolorallocate($image, 255, 255, 255);

        $fontPath = public_path('fonts/NotoSansJP-Bold.ttf');

        if (file_exists($fontPath) && function_exists('imagettfbbox') && function_exists('imagettftext')) {
            $fontSize = max(32, (int) ($width / 7));
            $text = 'SOLD';

            $box = \imagettfbbox($fontSize, -12, $fontPath, $text);
            $textWidth = abs($box[4] - $box[0]);
            $textHeight = abs($box[5] - $box[1]);

            $x = (int) (($width - $textWidth) / 2);
            $y = (int) (($height + $textHeight) / 2);

            \imagettftext($image, $fontSize, -12, $x, $y, $white, $fontPath, $text);
        } else {
            $text = 'SOLD';
            $font = 5;
            $textWidth = \imagefontwidth($font) * strlen($text);
            $textHeight = \imagefontheight($font);

            $x = (int) (($width - $textWidth) / 2);
            $y = (int) (($height - $textHeight) / 2);

            \imagestring($image, $font, $x, $y, $text, $white);
        }

        $soldImagePath = 'auction-items/sold_'.uniqid('', true).'.jpg';
        $soldFullPath = Storage::disk('public')->path($soldImagePath);

        \imagejpeg($image, $soldFullPath, 90);
        \imagedestroy($image);

        return $soldImagePath;
    }
}
