<?php

return [

    'groups' => [
        'abdomen' => 'Abdomen',
        'pecho' => 'Pecho',
        'espalda' => 'Espalda',
        'biceps' => 'Bíceps',
        'triceps' => 'Tríceps',
        'cuadriceps' => 'Cuádriceps',
        'hombro' => 'Hombro',
        'femoral' => 'Femoral',
        'gemelo' => 'Gemelo',
        'aductor' => 'Aductor',
        'antebrazo' => 'Antebrazo',
    ],

    'names' => [
  'abdomen-abs' => 'Elevaciones de piernas',
  'abdomen-cabs'        => 'Crunch abdominal',
  'aductor-aductor'     => 'Máquina de aductores',
  'antebrazo-cmuneca' => 'Curl de muñeca en banco unilateral',

        'antebrazo-cprono'   => 'Curl prono (antebrazo)',
        'antebrazo-munecas'  => 'Curl de muñecas en banco',

        // BÍCEPS
        'biceps-biceps'      => 'Curl martillo',
        'biceps-predicador'  => 'Curl predicador',

        // CUÁDRICEPS
        'cuadriceps-ext'        => 'Extensión de cuádriceps',
        'cuadriceps-jaka'       => 'Hack squat (máquina)',      // si “jaka” es otra cosa, lo cambiamos
        'cuadriceps-prensa'     => 'Prensa de piernas',
        'cuadriceps-prensah'    => 'Prensa horizontal',
        'cuadriceps-sentadilla' => 'Sentadilla',

        // ESPALDA
        'espalda-remouni'   => 'Remo unilateral (máquina/cable)',
        'espalda-dominadas' => 'Dominadas',
        'espalda-jalonc'    => 'Jalón al pecho',
        'espalda-remot'     => 'Remo en T',

        // FEMORAL
        'femoral-femorals'  => 'Curl femoral sentado',
        'femoral-femoralt'  => 'Curl femoral tumbado',

        // GEMELO
        'gemelo-gemelo'     => 'Elevación de gemelos',
        'gemelo-gemelom'    => 'Máquina de gemelo',

        // HOMBRO
        'hombro-facepull'   => 'Face pull',
        'hombro-fondos'     => 'Fondos',   // si esto es tríceps realmente, lo movemos luego
        'hombro-laterales'  => 'Elevaciones laterales',
'hombro-lateralesm'   => 'Elevaciones laterales en máquina',
'hombro-lateralesp'   => 'Elevaciones laterales con polea',
'hombro-militar'      => 'Press militar en máquina',

// PECHO
'pecho-aperturas'     => 'Aperturas en máquina',
'pecho-fondos'        => 'Fondos',

'pecho-pbancab'       => 'Press banca con barra',
'pecho-pbancaincb'    => 'Press banca inclinado con barra',
'pecho-pbancaincm'    => 'Press banca inclinado con mancuernas',
'pecho-pbancam'       => 'Press banca con mancuernas',

// TRÍCEPS
'triceps-fondos'      => 'Fondos',
'triceps-rompec'      => 'Rompecráneos',
'triceps-tricepsp'    => 'Extensión de tríceps en polea',

],

'tags' => [

  // ABDOMEN
  'abdomen-abs'   => ['abdomen', 'cuadriceps'], // elevaciones de piernas: abdomen + flexores de cadera (lo más parecido en tus grupos)
  'abdomen-cabs'  => ['abdomen'],

  // ADUCTOR
  'aductor-aductor' => ['aductor', 'cuadriceps'],

  // ANTEBRAZO
  'antebrazo-cprono'  => ['antebrazo', 'biceps'],
  'antebrazo-munecas' => ['antebrazo'],

  // BÍCEPS
  'biceps-biceps'     => ['biceps', 'antebrazo'],   // curl martillo: antebrazo muy implicado
  'biceps-predicador' => ['biceps', 'antebrazo'],

  // CUÁDRICEPS (compuestos suelen tocar femoral también)
  'cuadriceps-ext'        => ['cuadriceps'],
  'cuadriceps-jaka'       => ['cuadriceps', 'femoral'], // hack squat
  'cuadriceps-prensa'     => ['cuadriceps', 'femoral'],
  'cuadriceps-prensah'    => ['cuadriceps', 'femoral'],
  'cuadriceps-sentadilla' => ['cuadriceps', 'femoral'],

  // ESPALDA (tirones = espalda + bíceps + antebrazo)
  'espalda-remouni'   => ['espalda', 'biceps', 'antebrazo'],
  'espalda-dominadas' => ['espalda', 'biceps', 'antebrazo'],
  'espalda-jalonc'    => ['espalda', 'biceps', 'antebrazo'],
  'espalda-remot'     => ['espalda', 'biceps', 'antebrazo'],

  // FEMORAL
  'femoral-femorals' => ['femoral', 'gemelo'],  // gemelo estabiliza, secundario
  'femoral-femoralt' => ['femoral', 'gemelo'],

  // GEMELO
  'gemelo-gemelo'  => ['gemelo'],
  'gemelo-gemelom' => ['gemelo'],

  // HOMBRO
  'hombro-facepull'   => ['hombro', 'espalda'],  // deltoide posterior + espalda alta
  'hombro-laterales'  => ['hombro'],
  'hombro-lateralesm' => ['hombro'],
  'hombro-lateralesp' => ['hombro'],
  'hombro-militar'    => ['hombro', 'triceps'],

  // PECHO
  'pecho-aperturas'   => ['pecho', 'hombro'],   // hombro estabiliza
  'pecho-pbancab'     => ['pecho', 'triceps', 'hombro'],
  'pecho-pbancaincb'  => ['pecho', 'triceps', 'hombro'],
  'pecho-pbancaincm'  => ['pecho', 'triceps', 'hombro'],
  'pecho-pbancam'     => ['pecho', 'triceps', 'hombro'],

  // FONDOS (UNO SOLO, multi-categoría)
  'pecho-fondos'      => ['pecho', 'triceps', 'hombro'],

  // TRÍCEPS
  'triceps-rompec'    => ['triceps'],
  'triceps-tricepsp'  => ['triceps'],
],

'aliases' => [
  // Para que NO se repitan Fondos:
  'triceps-fondos' => 'pecho-fondos',
  'hombro-fondos'  => 'pecho-fondos',
],




];
