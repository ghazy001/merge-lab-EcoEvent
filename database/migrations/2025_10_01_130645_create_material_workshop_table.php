<?php
// database/migrations/2025_10_01_000003_create_material_workshop_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_workshop', function (Blueprint $table) {
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->primary(['material_id','workshop_id']); // unique couple
        });
    }
    public function down(): void {
        Schema::dropIfExists('material_workshop');
    }
};
