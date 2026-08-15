<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $indexes = collect(DB::select('SHOW INDEX FROM auction_items'));

        $singleManagementIndexes = $indexes
            ->where('Column_name', 'management_id')
            ->groupBy('Key_name')
            ->filter(fn ($items, $key) => $key !== 'auction_items_user_management_unique' && $items->count() === 1);

        foreach ($singleManagementIndexes as $keyName => $items) {
            Schema::table('auction_items', function (Blueprint $table) use ($keyName): void {
                $table->dropIndex((string) $keyName);
            });
        }

        $indexes = collect(DB::select('SHOW INDEX FROM auction_items'));

        if (! $indexes->where('Key_name', 'auction_items_user_management_unique')->count()) {
            Schema::table('auction_items', function (Blueprint $table): void {
                $table->unique(['user_id', 'management_id'], 'auction_items_user_management_unique');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $indexes = collect(DB::select('SHOW INDEX FROM auction_items'));

        if ($indexes->where('Key_name', 'auction_items_user_management_unique')->count()) {
            Schema::table('auction_items', function (Blueprint $table): void {
                $table->dropUnique('auction_items_user_management_unique');
            });
        }
    }
};
