<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\PredefinedRoutineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\RoutineFollowController;
use App\Http\Controllers\RoutineManageController;
use App\Http\Controllers\RoutineWizardController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'trackfit.profile'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ZONA PRIVADA (requiere sesión iniciada y correo verificado)
|--------------------------------------------------------------------------
| Sin el correo verificado, cualquier ruta privada redirige a la pantalla
| «Revisa tu correo», aunque se escriba la URL directamente.
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ===== HOY TOCA =====
    Route::post('/dashboard/empezar', [DashboardController::class, 'start'])
        ->name('dashboard.start');

    // ===== PROGRESO =====
    Route::get('/progress', [ProgressController::class, 'index'])
        ->name('progress.index');

    Route::get('/progress/records', [RecordsController::class, 'index'])
        ->name('progress.records');

    // ===== NUTRICIÓN =====
    // Rutas fijas antes que /nutrition/{date}
    Route::get('/nutrition', [NutritionController::class, 'today'])
        ->name('nutrition.today');

    Route::get('/nutrition/setup', [NutritionController::class, 'setup'])
        ->name('nutrition.setup');

    Route::post('/nutrition/setup', [NutritionController::class, 'saveSetup'])
        ->name('nutrition.setup.save');

    Route::get('/nutrition/goals', [NutritionController::class, 'goals'])
        ->name('nutrition.goals');

    Route::post('/nutrition/goals', [NutritionController::class, 'saveGoals'])
        ->name('nutrition.goals.save');

    Route::post('/nutrition/add', [NutritionController::class, 'add'])
        ->name('nutrition.add');

    Route::post('/nutrition/remove/{log}', [NutritionController::class, 'remove'])
        ->name('nutrition.remove');

    Route::post('/nutrition/activity', [NutritionController::class, 'saveActivity'])
        ->name('nutrition.activity.save');

    Route::get('/nutrition/log/{date}', [NutritionController::class, 'log'])
        ->where('date', '\d{4}-\d{2}-\d{2}')
        ->name('nutrition.log');

    // Dinámica, restringida a formato fecha
    Route::get('/nutrition/{date}', [NutritionController::class, 'day'])
        ->where('date', '\d{4}-\d{2}-\d{2}')
        ->name('nutrition.day');

    // ===== RUTINA: MENÚ Y SEGUIR =====
    Route::get('/rutina', function () {
        return view('rutina');
    })->name('rutina');

    Route::get('/rutina/seguir', [RoutineFollowController::class, 'show'])
        ->name('rutina.follow');

    // ===== RUTINA: ASISTENTE (crear) =====
    Route::get('/rutina/crear', [RoutineWizardController::class, 'step1'])
        ->name('rutina.create.step1');

    Route::post('/rutina/crear', [RoutineWizardController::class, 'postStep1'])
        ->name('rutina.create.postStep1');

    Route::get('/rutina/crear/dia/{day}', [RoutineWizardController::class, 'stepDay'])
        ->name('rutina.create.day');

    Route::post('/rutina/crear/dia/{day}', [RoutineWizardController::class, 'postStepDay'])
        ->name('rutina.create.postDay');

    Route::get('/rutina/crear/prev/{day}', [RoutineWizardController::class, 'prevDay'])
        ->name('rutina.create.prevDay');

    Route::get('/rutina/crear/resumen', [RoutineWizardController::class, 'summary'])
        ->name('rutina.create.summary');

    Route::post('/rutina/crear/finalizar', [RoutineWizardController::class, 'finish'])
        ->name('rutina.create.finish');

    // ===== GESTIONAR RUTINAS =====
    Route::get('/rutinas', [RoutineManageController::class, 'index'])
        ->name('rutinas.index');

    Route::get('/rutinas/{routine}/editar', [RoutineManageController::class, 'edit'])
        ->name('rutinas.edit');

    Route::put('/rutinas/{routine}', [RoutineManageController::class, 'update'])
        ->name('rutinas.update');

    Route::post('/rutinas/{routine}/duplicar', [RoutineManageController::class, 'duplicate'])
        ->name('rutinas.duplicate');

    Route::delete('/rutinas/{routine}', [RoutineManageController::class, 'destroy'])
        ->name('rutinas.destroy');

    // ===== RUTINAS PREDEFINIDAS =====
    Route::get('/rutinas/predefinidas', [PredefinedRoutineController::class, 'index'])
        ->name('routines.predefined.index');

    Route::get('/rutinas/predefinidas/{routineKey}', [PredefinedRoutineController::class, 'show'])
        ->name('routines.predefined.show');

    Route::post('/rutinas/predefinidas/{routineKey}/usar', [PredefinedRoutineController::class, 'use'])
        ->name('routines.predefined.use');

    // ===== ENTRENO =====
    Route::get('/rutina/entreno/{routineDay}', [WorkoutController::class, 'show'])
        ->name('workout.show');

    Route::post('/rutina/entreno/{routineDay}/start', [WorkoutController::class, 'start'])
        ->name('workout.start');

    Route::post('/rutina/entreno/{routineDay}/save', [WorkoutController::class, 'save'])
        ->name('workout.save');

    Route::post('/rutina/entreno/{routineDay}/finish', [WorkoutController::class, 'finish'])
        ->name('workout.finish');

    // ===== PERFIL =====
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| ACCESO CON GOOGLE / GITHUB
|--------------------------------------------------------------------------
*/

// throttle:20,1 = máximo 20 intentos por minuto
Route::middleware(['guest', 'throttle:20,1'])->group(function () {
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'github'])
        ->name('social.redirect');

    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'github'])
        ->name('social.callback');
});
