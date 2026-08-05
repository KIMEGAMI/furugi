<?php

namespace App\Console\Commands;

use App\Models\AuctionItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateDummySalesData extends Command
{
    protected $signature = 'furugi:dummy-sales';

    protected $description = 'furugi用の月別売上確認ダミーデータを作成します';

    public function handle(): int
    {
        $platforms = [
            'ヤフオク',
            'メルカリ',
            'ラクマ',
            'Yahooフリマ',
            'その他',
        ];

        $titles = [
            'ヴィンテージ デニムジャケット',
            '古着 スウェット',
            'チェックシャツ',
            'ミリタリージャケット',
            'レザージャケット',
            'ワークパンツ',
            'ナイロンパーカー',
            '古着 Tシャツ',
            'カーディガン',
            'コーデュロイパンツ',
        ];

        $createdCount = 0;

        for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
            $targetMonth = Carbon::now()->subMonths($monthOffset);

            for ($i = 1; $i <= 5; $i++) {
                $purchasePrice = rand(800, 5000);
                $soldPrice = $purchasePrice + rand(1000, 9000);

                $item = new AuctionItem;

                $this->setColumn($item, 'management_id', 'DUMMY-'.$targetMonth->format('Ym').'-'.Str::upper(Str::random(5)));
                $this->setColumn($item, 'title', $titles[array_rand($titles)]);
                $this->setColumn($item, 'comment', '月別売上グラフ確認用のダミーデータです。');
                $this->setColumn($item, 'purchase_price', $purchasePrice);
                $this->setColumn($item, 'sold_price', $soldPrice);
                $this->setColumn($item, 'status', 'sold');
                $this->setColumn($item, 'platform', $platforms[array_rand($platforms)]);

                if (Schema::hasColumn('auction_items', 'sold_at')) {
                    $item->sold_at = $targetMonth->copy()->day(rand(1, 25))->format('Y-m-d');
                }

                $item->save();

                $createdCount++;
            }
        }

        $this->info($createdCount.'件のダミー売上データを作成しました。');

        return self::SUCCESS;
    }

    private function setColumn(AuctionItem $item, string $column, mixed $value): void
    {
        if (Schema::hasColumn('auction_items', $column)) {
            $item->{$column} = $value;
        }
    }
}
