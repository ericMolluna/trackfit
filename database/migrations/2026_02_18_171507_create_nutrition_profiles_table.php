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
        Schema::create('nutrition_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

    $table->string('sex'); // male|female
    $table->unsignedTinyInteger('age'); // años
    $table->unsignedSmallInteger('height_cm');
    $table->decimal('weight_kg', 5, 2);

    $table->string('activity'); // sedentary|light|moderate|active|very_active
    $table->string('goal');     // lose|maintain|gain

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_profiles');
    }
};
