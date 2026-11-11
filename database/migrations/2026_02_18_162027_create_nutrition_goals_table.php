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
        Schema::create('nutrition_goals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
    $table->unsignedSmallInteger('calories')->default(2500);
    $table->unsignedSmallInteger('protein_g')->default(140);
    $table->unsignedSmallInteger('carbs_g')->default(300);
    $table->unsignedSmallInteger('fat_g')->default(70);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_goals');
    }
};
