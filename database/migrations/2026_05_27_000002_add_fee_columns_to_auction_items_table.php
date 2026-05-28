<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_items', function (Blueprint $table) {
            $table->decimal('sales_fee_rate', 5, 2)->default(10.00)->after('sold_price');
            $table->unsignedInteger('sales_fee')->default(0)->after('sales_fee_rate');
            $table->unsignedInteger('shipping_fee')->default(0)->after('sales_fee');
        });
    }

    public function down(): void
    {
        Schema::table('auction_items', function (Blueprint $table) {
            $table->dropColumn([
                'sales_fee_rate',
                'sales_fee',
                'shipping_fee',
            ]);
        });
    }
};
