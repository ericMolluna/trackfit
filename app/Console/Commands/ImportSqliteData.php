<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copia los datos del archivo database/database.sqlite a la base de datos
 * PostgreSQL del servidor. Solo se hace UNA vez: si la base de datos
 * ya tiene usuarios, no toca nada (así nunca se pisan datos nuevos).
 *
 * Se ejecuta solo al arrancar el servidor (docker/entrypoint.sh).
 * Uso manual: php artisan trackfit:importar-sqlite
 */
class ImportSqliteData extends Command
{
    protected $signature = 'trackfit:importar-sqlite {--file= : Ruta del archivo SQLite}';

    protected $description = 'Importa una vez los datos de database.sqlite a la base de datos actual';

    /**
     * Tablas en orden: primero las que no dependen de ninguna otra
     * (usuarios, alimentos) y después las que dependen de ellas.
     */
    private const TABLES = [
        'users',
        'foods',
        'routines',
        'routine_days',
        'routine_exercises',
        'workouts',
        'workout_sets',
        'nutrition_goals',
        'nutrition_profiles',
        'food_logs',
        'daily_activities',
    ];

    public function handle(): int
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->info('La base de datos ya es SQLite: no hay nada que importar.');

            return self::SUCCESS;
        }

        if (DB::table('users')->exists()) {
            $this->info('La base de datos ya tiene datos: no se importa nada.');

            return self::SUCCESS;
        }

        $file = $this->option('file') ?: database_path('database.sqlite');

        if (! is_file($file)) {
            $this->warn("No existe {$file}: no se importa nada.");

            return self::SUCCESS;
        }

        // Conexión temporal de solo lectura al archivo SQLite.
        Config::set('database.connections.sqlite_import', [
            'driver' => 'sqlite',
            'database' => $file,
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]);

        $source = DB::connection('sqlite_import');

        DB::transaction(function () use ($source) {
            foreach (self::TABLES as $table) {
                if (! $source->getSchemaBuilder()->hasTable($table) || ! Schema::hasTable($table)) {
                    continue;
                }

                // Solo copiamos las columnas que existen en las dos bases de datos.
                $targetColumns = collect(Schema::getColumns($table))->keyBy('name');
                $columns = array_values(array_intersect(
                    $source->getSchemaBuilder()->getColumnListing($table),
                    $targetColumns->keys()->all()
                ));

                $booleans = $targetColumns
                    ->filter(fn ($column) => str_contains(strtolower($column['type_name']), 'bool'))
                    ->keys()
                    ->all();

                $count = 0;

                $source->table($table)->orderBy($columns[0])->chunk(500, function ($rows) use ($table, $columns, $booleans, &$count) {
                    $data = $rows->map(function ($row) use ($columns, $booleans) {
                        $values = [];

                        foreach ($columns as $column) {
                            $value = $row->{$column};
                            // SQLite guarda los booleanos como 0/1; PostgreSQL usa true/false.
                            $values[$column] = in_array($column, $booleans, true) && $value !== null
                                ? (bool) $value
                                : $value;
                        }

                        return $values;
                    })->all();

                    DB::table($table)->insert($data);
                    $count += count($data);
                });

                // Los contadores de "id" de PostgreSQL deben seguir después del último id importado.
                if (in_array('id', $columns, true) && $count > 0) {
                    DB::statement(
                        "SELECT setval(pg_get_serial_sequence('{$table}', 'id'), (SELECT MAX(id) FROM {$table}))"
                    );
                }

                $this->line("{$table}: {$count} filas");
            }
        });

        $this->info('Datos importados correctamente.');

        return self::SUCCESS;
    }
}
