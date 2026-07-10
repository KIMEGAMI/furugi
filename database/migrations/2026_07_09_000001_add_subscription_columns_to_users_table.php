<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('subscription_plan')->default(User::PLAN_FREE)->after('google_id');
            $table->timestamp('subscribed_at')->nullable()->after('subscription_plan');
            $table->timestamp('subscription_cancelled_at')->nullable()->after('subscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan',
                'subscribed_at',
                'subscription_cancelled_at',
            ]);
        });
    }
};
