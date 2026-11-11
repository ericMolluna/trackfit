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
        Schema::create('foods', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedSmallInteger('kcal_100')->default(0);
    $table->decimal('protein_100', 6, 2)->default(0);
    $table->decimal('carbs_100', 6, 2)->default(0);
    $table->decimal('fat_100', 6, 2)->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
