<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->softDeletes(); // crée deleted_at TIMESTAMP NULL
        });
    }

    public function down(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
