<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cause_id')->constrained()->cascadeOnDelete();
            $table->string('donor_name');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('date')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();

            $table->index('cause_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
