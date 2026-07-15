<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'subscription_plan')) {
                $table->string('subscription_plan')->default('free')->after('google_id');
            }

            if (! Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->nullable()->after('subscription_plan');
            }

            if (! Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->index()->after('subscription_status');
            }

            if (! Schema::hasColumn('users', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable()->index()->after('stripe_customer_id');
            }

            if (! Schema::hasColumn('users', 'premium_started_at')) {
                $table->timestamp('premium_started_at')->nullable()->after('stripe_subscription_id');
            }

            if (! Schema::hasColumn('users', 'premium_ends_at')) {
                $table->timestamp('premium_ends_at')->nullable()->after('premium_started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['stripe_customer_id']);
            $table->dropIndex(['stripe_subscription_id']);
            $table->dropColumn([
                'subscription_plan',
                'subscription_status',
                'stripe_customer_id',
                'stripe_subscription_id',
                'premium_started_at',
                'premium_ends_at',
            ]);
        });
    }
};
