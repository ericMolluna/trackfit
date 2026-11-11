<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodLog;
use App\Models\NutritionGoal;
use App\Models\NutritionProfile;
use App\Models\DailyActivity;
use App\Models\Workout;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    public function today()
    {
        return $this->day(now()->toDateString());
    }

    public function day(string $date)
    {
        $userId = auth()->id();

        $goal = NutritionGoal::firstOrCreate(
            ['user_id' => $userId],
            ['calories'=>2500,'protein_g'=>140,'carbs_g'=>300,'fat_g'=>70]
        );

        $profile = NutritionProfile::where('user_id', $userId)->first();

        if (!$profile) {
            return redirect()->route('nutrition.setup');
        }

        $logs = FoodLog::where('user_id', $userId)
            ->where('date', $date)
            ->with('food')
            ->orderBy('meal')
            ->get();

        $foods = Food::orderBy('name')->get();

        // =========================
        // MACROS CONSUMIDOS
        // =========================

        $tot = [
            'kcal' => 0,
            'p' => 0,
            'c' => 0,
            'f' => 0
        ];

        foreach ($logs as $l) {
            $m = $l->grams / 100.0;

            $tot['kcal'] += $l->food->kcal_100 * $m;
            $tot['p']    += $l->food->protein_100 * $m;
            $tot['c']    += $l->food->carbs_100 * $m;
            $tot['f']    += $l->food->fat_100 * $m;
        }

        // =========================
        // ACTIVIDAD DIARIA
        // =========================

        $activity = DailyActivity::where('user_id', $userId)
    ->whereDate('date', $date)
    ->first();

if (!$activity) {
    $activity = DailyActivity::create([
        'user_id' => $userId,
        'date' => $date,
        'steps' => 0,
        'steps_kcal' => 0,
    ]);
}

        // =========================
        // GASTO POR ENTRENAMIENTO
        // =========================

        $workoutKcal = Workout::where('user_id', $userId)
            ->whereDate('started_at', $date)
            ->whereNotNull('ended_at')
            ->sum('estimated_kcal');

        // =========================
        // OBJETIVO DINÁMICO DEL DÍA
        // =========================

        $baseCalories = (int) $goal->calories;

        $extraCalories =
            (float) $activity->steps_kcal
            + (float) $workoutKcal;

        $todayCalories = (int) round(
            $baseCalories + $extraCalories
        );

        $energy = [
            'base' => $baseCalories,
            'steps' => (int) round($activity->steps_kcal),
            'workout' => (int) round($workoutKcal),
            'extra' => (int) round($extraCalories),
            'today' => $todayCalories,
            'remaining' => (int) round(
                $todayCalories - $tot['kcal']
            ),
        ];

        return view(
            'nutrition-day',
            compact(
                'date',
                'goal',
                'logs',
                'foods',
                'tot',
                'profile',
                'activity',
                'energy'
            )
        );
    }

    public function goals()
    {
        $goal = NutritionGoal::firstOrCreate(
            ['user_id' => auth()->id()],
            ['calories'=>2500,'protein_g'=>140,'carbs_g'=>300,'fat_g'=>70]
        );

        return view('nutrition-goals', compact('goal'));
    }

    public function saveGoals(Request $request)
    {
        $data = $request->validate([
            'calories'   => ['required','integer','min:800','max:6000'],
            'protein_g'  => ['required','integer','min:0','max:400'],
            'carbs_g'    => ['required','integer','min:0','max:800'],
            'fat_g'      => ['required','integer','min:0','max:300'],
        ]);

        NutritionGoal::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()->route('nutrition.today')
            ->with('ok', 'Objetivos guardados ✅');
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'date'      => ['required','date'],
            'meal'      => ['required','in:breakfast,lunch,dinner,snack'],
            'food_name' => ['required','string','max:120'],
            'grams'     => ['required','numeric','min:1','max:5000'],
        ]);

        $name = trim($data['food_name']);

        // Buscar o crear alimento
        $food = Food::firstOrCreate(
            ['name' => $name],
            [
                'kcal_100'=>0,
                'protein_100'=>0,
                'carbs_100'=>0,
                'fat_100'=>0
            ]
        );

        FoodLog::create([
            'user_id' => auth()->id(),
            'date'    => $data['date'],
            'meal'    => $data['meal'],
            'food_id' => $food->id,
            'grams'   => $data['grams'],
        ]);

        return back()->with('ok', 'Comida registrada ✅');
    }

    public function remove(FoodLog $log)
    {
        abort_unless($log->user_id === auth()->id(), 403);

        $log->delete();

        return back()->with('ok', 'Eliminado ✅');
    }

    public function setup()
    {
        $profile = NutritionProfile::where(
            'user_id',
            auth()->id()
        )->first();

        return view('nutrition-setup', compact('profile'));
    }

    public function saveSetup(Request $request)
    {
        $data = $request->validate([
            'sex' => ['required','in:male,female'],
            'age' => ['required','integer','min:10','max:90'],
            'height_cm' => ['required','integer','min:120','max:230'],
            'weight_kg' => ['required','numeric','min:35','max:250'],
            'activity' => ['required','in:sedentary,light,moderate,active,very_active'],
            'goal' => ['required','in:lose,maintain,gain'],
        ]);

        $profile = NutritionProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'user_id' => auth()->id(),
                ...$data
            ]
        );

        // =========================
        // CÁLCULO BMR - MIFFLIN ST JEOR
        // =========================

        $w = (float) $data['weight_kg'];
        $h = (int) $data['height_cm'];
        $a = (int) $data['age'];

        $bmr = ($data['sex'] === 'male')
            ? (10 * $w + 6.25 * $h - 5 * $a + 5)
            : (10 * $w + 6.25 * $h - 5 * $a - 161);

        /*
         * Actividad cotidiana base.
         *
         * Entrenamientos y pasos se añadirán después
         * según la actividad REAL de cada día.
         *
         * Evitamos así contabilizar dos veces
         * el ejercicio.
         */
        $factors = [
            'sedentary'   => 1.20,
            'light'       => 1.25,
            'moderate'    => 1.30,
            'active'      => 1.35,
            'very_active' => 1.40,
        ];

        $baseTdee = $bmr * (
            $factors[$data['activity']] ?? 1.20
        );

        // =========================
        // AJUSTE SEGÚN OBJETIVO
        // =========================

        $kcal = match ($data['goal']) {
            'lose'     => $baseTdee - 400,
            'gain'     => $baseTdee + 250,
            default    => $baseTdee,
        };

        // mínimo de seguridad
        $kcal = (int) round(
            max(1200, $kcal)
        );

        // =========================
        // MACROS
        // =========================

        // Proteína: 1.8 g/kg
        $protein = (int) round(
            $w * 1.8
        );

        // Grasas: 0.9 g/kg
        $fat = (int) round(
            $w * 0.9
        );

        $proteinKcal = $protein * 4;
        $fatKcal = $fat * 9;

        // El resto de calorías se asigna a carbohidratos
        $carbs = (int) round(
            max(
                0,
                ($kcal - $proteinKcal - $fatKcal) / 4
            )
        );

        NutritionGoal::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'user_id' => auth()->id(),
                'calories' => $kcal,
                'protein_g' => $protein,
                'carbs_g' => $carbs,
                'fat_g' => $fat,
            ]
        );

        return redirect()
            ->route('nutrition.today')
            ->with(
                'ok',
                'Objetivos calculados y guardados ✅'
            );
    }

    public function log(string $date)
    {
        $userId = auth()->id();

        $profile = NutritionProfile::where(
            'user_id',
            $userId
        )->first();

        if (!$profile) {
            return redirect()->route('nutrition.setup');
        }

        $goal = NutritionGoal::firstOrCreate(
            ['user_id' => $userId],
            ['calories'=>2500,'protein_g'=>140,'carbs_g'=>300,'fat_g'=>70]
        );

        $logs = FoodLog::where('user_id', $userId)
            ->where('date', $date)
            ->with('food')
            ->orderBy('meal')
            ->get();

        $foods = Food::orderBy('name')->get();

        // Totales
        $tot = [
            'kcal'=>0,
            'p'=>0,
            'c'=>0,
            'f'=>0
        ];

        foreach ($logs as $l) {
            $m = $l->grams / 100.0;

            $tot['kcal'] += $l->food->kcal_100 * $m;
            $tot['p']    += $l->food->protein_100 * $m;
            $tot['c']    += $l->food->carbs_100 * $m;
            $tot['f']    += $l->food->fat_100 * $m;
        }

        return view(
            'nutrition-log',
            compact(
                'date',
                'goal',
                'logs',
                'foods',
                'tot'
            )
        );
    }
    public function saveActivity(Request $request)
{
    $data = $request->validate([
        'date' => ['required', 'date'],
        'steps' => ['required', 'integer', 'min:0', 'max:100000'],
    ]);

    $userId = auth()->id();

    $profile = NutritionProfile::where('user_id', $userId)->first();

    if (!$profile) {
        return redirect()
            ->route('nutrition.setup')
            ->with('error', 'Configura primero tus datos nutricionales.');
    }

    $steps = (int) $data['steps'];
    $weightKg = (float) $profile->weight_kg;

    // Estimación del gasto por pasos
    $minutesWalking = $steps / 100;
    $hoursWalking = $minutesWalking / 60;

    $met = 3.3;

    $stepsKcal = $met * $weightKg * $hoursWalking;
    $stepsKcal = round($stepsKcal, 2);

    $activity = DailyActivity::where('user_id', $userId)
    ->whereDate('date', $data['date'])
    ->first();

if ($activity) {
    $activity->update([
        'steps' => $steps,
        'steps_kcal' => $stepsKcal,
    ]);
} else {
    DailyActivity::create([
        'user_id' => $userId,
        'date' => $data['date'],
        'steps' => $steps,
        'steps_kcal' => $stepsKcal,
    ]);
}

    return redirect()
        ->route('nutrition.day', ['date' => $data['date']])
        ->with('ok', 'Actividad diaria actualizada 👟');
}
}