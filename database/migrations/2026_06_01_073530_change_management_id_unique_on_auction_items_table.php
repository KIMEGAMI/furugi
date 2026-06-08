<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM auction_items'));

        $singleManagementIndexes = $indexes
            ->where('Column_name', 'management_id')
            ->groupBy('Key_name')
            ->filter(fn ($items, $key) => $key !== 'auction_items_user_management_unique' && $items->count() === 1);

        foreach ($singleManagementIndexes as $keyName => $items) {
            DB::statement('ALTER TABLE auction_items DROP INDEX `'.$keyName.'`');
        }

        $indexes = collect(DB::select('SHOW INDEX FROM auction_items'));

        if (! $indexes->where('Key_name', 'auction_items_user_management_unique')->count()) {
            DB::statement('ALTER TABLE auction_items ADD UNIQUE `auction_items_user_management_unique` (`user_id`, `management_id`)');
        }
    }

    public function down(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM auction_items'));

        if ($indexes->where('Key_name', 'auction_items_user_management_unique')->count()) {
            DB::statement('ALTER TABLE auction_items DROP INDEX `auction_items_user_management_unique`');
        }
    }
};
