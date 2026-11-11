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
        Schema::create('workout_sets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
    $table->string('exercise_id', 120);          // ej: pecho-pbancab
    $table->unsignedTinyInteger('set_no');       // 1,2,3...
    $table->unsignedTinyInteger('reps')->nullable();
    $table->decimal('weight', 6, 2)->nullable(); // kg
    $table->timestamps();

    $table->unique(['workout_id','exercise_id','set_no']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_sets');
    }
};
