<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','role')) {
                $table->string('role')->default('user')->after('password');
            }
            if (!Schema::hasColumn('users','is_banned')) {
                $table->boolean('is_banned')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users','ban_reason')) {
                $table->string('ban_reason',255)->nullable()->after('is_banned');
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','ban_reason')) $table->dropColumn('ban_reason');
            if (Schema::hasColumn('users','is_banned')) $table->dropColumn('is_banned');
            if (Schema::hasColumn('users','role')) $table->dropColumn('role');
        });
    }
};
