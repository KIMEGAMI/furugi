<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'trial_used_at')) {
                $table->timestamp('trial_used_at')->nullable()->after('premium_ends_at');
            }
        });
    }

    public function down(): void
    {
        // Keep the trial usage marker to avoid accidentally granting repeat trials.
    }
};
