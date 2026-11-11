<?php

return [

    // =========================================================
    // RUTINA 1
    // UPPER / LOWER · 4 DÍAS
    // =========================================================

    'upper-lower-4' => [

        'number' => 1,

        'title' => 'Upper / Lower',

        'days_count' => 4,

        'description' =>
            'Rutina de 4 días dividida en tren superior e inferior. '
            . 'Ideal para trabajar cada grupo muscular dos veces por semana.',

        'days' => [

            // -------------------------
            // DÍA 1 - UPPER A
            // -------------------------

            [
                'name' => 'Upper A',

                'exercises' => [
                    'pecho-pbancab',
                    'espalda-jalonc',
                    'espalda-remouni',
                    'hombro-laterales',
                    'biceps-predicador',
                    'triceps-tricepsp',
                ],
            ],


            // -------------------------
            // DÍA 2 - LOWER A
            // -------------------------

            [
                'name' => 'Lower A',

                'exercises' => [
                    'cuadriceps-sentadilla',
                    'cuadriceps-ext',
                    'femoral-femorals',
                    'aductor-aductor',
                    'gemelo-gemelom',
                    'abdomen-cabs',
                ],
            ],


            // -------------------------
            // DÍA 3 - UPPER B
            // -------------------------

            [
                'name' => 'Upper B',

                'exercises' => [
                    'pecho-pbancaincm',
                    'espalda-dominadas',
                    'espalda-remot',
                    'hombro-militar',
                    'hombro-facepull',
                    'biceps-biceps',
                    'triceps-rompec',
                ],
            ],


            // -------------------------
            // DÍA 4 - LOWER B
            // -------------------------

            [
                'name' => 'Lower B',

                'exercises' => [
                    'cuadriceps-prensa',
                    'cuadriceps-ext',
                    'femoral-femoralt',
                    'aductor-aductor',
                    'gemelo-gemelo',
                    'abdomen-abs',
                ],
            ],
        ],
    ],



    // =========================================================
    // RUTINA 2
    // PPL HÍBRIDA · 5 DÍAS
    // =========================================================

    'ppl-5' => [

        'number' => 2,

        'title' => 'Push / Pull / Legs',

        'days_count' => 5,

        'description' =>
            'Rutina de 5 días basada en Push, Pull y Legs, '
            . 'completada con un segundo día de torso y pierna.',

        'days' => [

            // -------------------------
            // DÍA 1 - PUSH
            // -------------------------

            [
                'name' => 'Push',

                'exercises' => [
                    'pecho-pbancab',
                    'pecho-pbancaincm',
                    'pecho-aperturas',
                    'hombro-militar',
                    'hombro-laterales',
                    'triceps-tricepsp',
                ],
            ],


            // -------------------------
            // DÍA 2 - PULL
            // -------------------------

            [
                'name' => 'Pull',

                'exercises' => [
                    'espalda-dominadas',
                    'espalda-jalonc',
                    'espalda-remot',
                    'hombro-facepull',
                    'biceps-predicador',
                    'biceps-biceps',
                ],
            ],


            // -------------------------
            // DÍA 3 - LEGS
            // -------------------------

            [
                'name' => 'Legs',

                'exercises' => [
                    'cuadriceps-sentadilla',
                    'cuadriceps-prensa',
                    'cuadriceps-ext',
                    'femoral-femorals',
                    'aductor-aductor',
                    'gemelo-gemelom',
                    'abdomen-cabs',
                ],
            ],


            // -------------------------
            // DÍA 4 - UPPER
            // -------------------------

            [
                'name' => 'Upper',

                'exercises' => [
                    'pecho-pbancaincb',
                    'espalda-remouni',
                    'espalda-jalonc',
                    'hombro-lateralesm',
                    'biceps-biceps',
                    'triceps-rompec',
                ],
            ],


            // -------------------------
            // DÍA 5 - LOWER
            // -------------------------

            [
                'name' => 'Lower',

                'exercises' => [
                    'cuadriceps-jaka',
                    'cuadriceps-ext',
                    'femoral-femoralt',
                    'aductor-aductor',
                    'gemelo-gemelo',
                    'abdomen-abs',
                ],
            ],
        ],
    ],



    // =========================================================
    // RUTINA 3
    // FULL BODY · 4 DÍAS
    // =========================================================

    'fullbody-4' => [

        'number' => 3,

        'title' => 'Full Body',

        'days_count' => 4,

        'description' =>
            'Rutina de cuerpo completo de 4 días. '
            . 'Cada sesión combina ejercicios de torso y pierna.',

        'days' => [

            // -------------------------
            // DÍA 1 - FULL BODY A
            // -------------------------

            [
                'name' => 'Full Body A',

                'exercises' => [
                    'cuadriceps-sentadilla',
                    'pecho-pbancab',
                    'espalda-jalonc',
                    'femoral-femorals',
                    'hombro-laterales',
                    'biceps-predicador',
                    'triceps-tricepsp',
                ],
            ],


            // -------------------------
            // DÍA 2 - FULL BODY B
            // -------------------------

            [
                'name' => 'Full Body B',

                'exercises' => [
                    'cuadriceps-prensa',
                    'pecho-pbancaincm',
                    'espalda-remouni',
                    'femoral-femoralt',
                    'hombro-militar',
                    'gemelo-gemelo',
                    'abdomen-cabs',
                ],
            ],


            // -------------------------
            // DÍA 3 - FULL BODY C
            // -------------------------

            [
                'name' => 'Full Body C',

                'exercises' => [
                    'cuadriceps-jaka',
                    'pecho-aperturas',
                    'espalda-dominadas',
                    'femoral-femorals',
                    'hombro-facepull',
                    'biceps-biceps',
                    'triceps-rompec',
                ],
            ],


            // -------------------------
            // DÍA 4 - FULL BODY D
            // -------------------------

            [
                'name' => 'Full Body D',

                'exercises' => [
                    'cuadriceps-prensa',
                    'pecho-pbancam',
                    'espalda-remot',
                    'femoral-femoralt',
                    'hombro-lateralesp',
                    'gemelo-gemelom',
                    'abdomen-abs',
                ],
            ],
        ],
    ],

];