<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');                  // minimal
            $table->text('description')->nullable();  // optionnel
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->foreignId('lieu_id')              // lien léger vers Lieux existants
            ->nullable()
                ->constrained('lieux')
                ->nullOnDelete();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status')->default('draft'); // draft|published
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('workshops');
    }
};
