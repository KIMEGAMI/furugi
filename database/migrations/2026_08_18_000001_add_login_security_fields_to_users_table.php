<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('trial_used_at');
            }

            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }

            if (! Schema::hasColumn('users', 'last_login_user_agent_hash')) {
                $table->string('last_login_user_agent_hash', 64)->nullable()->after('last_login_ip');
            }

            if (! Schema::hasColumn('users', 'suspicious_login_detected_at')) {
                $table->timestamp('suspicious_login_detected_at')->nullable()->after('last_login_user_agent_hash');
            }
        });
    }

    public function down(): void
    {
        // Keep login security history to avoid weakening suspicious-login protection on rollback.
    }
};
