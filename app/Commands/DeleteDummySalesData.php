<?php

namespace App\Console\Commands;

use App\Models\AuctionItem;
use Illuminate\Console\Command;

class DeleteDummySalesData extends Command
{
    protected $signature = 'furugi:delete-dummy-sales';

    protected $description = 'furugi用の月別売上確認ダミーデータを削除します';

    public function handle(): int
    {
        $deletedCount = AuctionItem::query()
            ->where('management_id', 'like', 'DUMMY-%')
            ->delete();

        $this->info($deletedCount.'件のダミー売上データを削除しました。');

        return self::SUCCESS;
    }
}
