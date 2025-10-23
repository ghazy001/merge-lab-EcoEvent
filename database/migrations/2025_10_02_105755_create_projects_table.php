<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('planned'); // planned|active|completed|archived
            $table->unsignedTinyInteger('progress')->default(0); // 0..100
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status','progress']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
