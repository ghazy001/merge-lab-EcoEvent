<?php

// database/migrations/2025_10_18_000001_add_message_to_donations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('message', 1000)->nullable()->after('amount');
        });
    }
    public function down(): void {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
};

