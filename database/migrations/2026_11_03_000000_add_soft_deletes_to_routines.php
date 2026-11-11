<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Borrado lógico de rutinas y días.
 *
 * workouts.routine_day_id tiene "on delete cascade": si borráramos
 * de verdad una rutina o un día, se borraría también el historial
 * de entrenos y series. Con deleted_at la fila se conserva y
 * Eloquent la oculta automáticamente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('routine_days', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('routine_days', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
