<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->string('intensity')->nullable()->after('duration_sec');

            $table->decimal('estimated_kcal', 8, 2)
                ->default(0)
                ->after('intensity');
        });
    }

    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn([
                'intensity',
                'estimated_kcal',
            ]);
        });
    }
};