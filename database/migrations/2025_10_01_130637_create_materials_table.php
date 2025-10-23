<?php
// database/migrations/2025_10_01_000002_create_materials_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // minimal
            $table->unsignedInteger('stock')->default(0);
            $table->string('unit')->nullable();    // ex: pcs, kg, m
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('materials');
    }
};
