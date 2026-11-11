<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $foods = [

            // =====================
            // PANES Y CEREALES
            // =====================
            ['Pan blanco',265,9,49,3],
            ['Pan integral',240,10,41,4],
            ['Pan de centeno',259,8,48,3],
            ['Pan de molde',270,9,50,4],
            ['Avena',389,17,66,7],
            ['Copos de maíz',365,8,84,1],
            ['Muesli',360,10,64,6],
            ['Arroz blanco cocido',130,3,28,0.3],
            ['Arroz integral cocido',123,2.7,25,1],
            ['Arroz basmati',121,3,25,0.4],
            ['Pasta cocida',158,6,31,1],
            ['Pasta integral cocida',150,6,30,1.5],
            ['Quinoa cocida',120,4.4,21,1.9],
            ['Cuscús cocido',112,3.8,23,0.2],
            ['Tortilla de trigo',305,8,55,7],
            ['Tortilla de maíz',218,6,45,3],

            // =====================
            // LEGUMBRES
            // =====================
            ['Lentejas cocidas',116,9,20,0.4],
            ['Garbanzos cocidos',164,9,27,2.6],
            ['Judías blancas',139,9.7,25,0.6],
            ['Judías pintas',143,9,26,0.7],
            ['Soja cocida',173,16.6,9.9,9],
            ['Hummus',166,8,14,9],

            // =====================
            // CARNES
            // =====================
            ['Pechuga de pollo',165,31,0,3.6],
            ['Muslo de pollo',177,24,0,8],
            ['Pavo',135,30,0,1],
            ['Ternera magra',176,26,0,6],
            ['Ternera entrecot',250,25,0,20],
            ['Cerdo magro',242,27,0,14],
            ['Lomo de cerdo',195,27,0,9],
            ['Costillas de cerdo',290,24,0,24],
            ['Hamburguesa de ternera',254,17,0,20],
            ['Carne picada mixta',280,18,0,22],

            // =====================
            // PESCADOS Y MARISCOS
            // =====================
            ['Atún al natural',116,26,0,1],
            ['Atún en aceite',198,25,0,8],
            ['Salmón',208,20,0,13],
            ['Merluza',90,18,0,1],
            ['Bacalao',82,18,0,0.7],
            ['Sardinas',208,25,0,11],
            ['Caballa',205,19,0,14],
            ['Gambas',99,24,0,0.3],
            ['Mejillones',172,24,7,4.5],
            ['Pulpo',82,15,2,1],

            // =====================
            // HUEVOS Y LÁCTEOS
            // =====================
            ['Huevo entero',143,12.6,0.7,9.5],
            ['Clara de huevo',52,11,0.7,0.2],
            ['Leche entera',60,3.2,4.8,3.3],
            ['Leche semidesnatada',47,3.3,4.8,1.6],
            ['Leche desnatada',34,3.4,5,0.1],
            ['Yogur natural',61,3.5,4.7,3.3],
            ['Yogur griego',120,6,4,10],
            ['Queso fresco',98,11,3,4],
            ['Queso mozzarella',280,28,3,17],
            ['Queso curado',400,25,1,33],

            // =====================
            // FRUTAS
            // =====================
            ['Plátano',89,1.1,23,0.3],
            ['Manzana',52,0.3,14,0.2],
            ['Pera',57,0.4,15,0.1],
            ['Naranja',47,0.9,12,0.1],
            ['Mandarina',53,0.8,13,0.3],
            ['Fresa',32,0.7,8,0.3],
            ['Uvas',69,0.6,18,0.2],
            ['Melón',34,0.8,8,0.2],
            ['Sandía',30,0.6,8,0.2],
            ['Kiwi',61,1.1,15,0.5],

            // =====================
            // VERDURAS
            // =====================
            ['Lechuga',15,1.4,3,0.2],
            ['Tomate',18,0.9,3.9,0.2],
            ['Cebolla',40,1.1,9,0.1],
            ['Zanahoria',41,0.9,10,0.2],
            ['Calabacín',17,1.2,3,0.3],
            ['Brócoli',34,2.8,7,0.4],
            ['Espinacas',23,2.9,4,0.4],
            ['Pimiento rojo',31,1,6,0.3],
            ['Berenjena',25,1,6,0.2],
            ['Champiñones',22,3.1,3.3,0.3],

            // =====================
            // GRASAS Y EXTRAS
            // =====================
            ['Aceite de oliva',884,0,0,100],
            ['Aceite de girasol',884,0,0,100],
            ['Mantequilla',717,0.9,0.1,81],
            ['Margarina',717,0.2,0,80],
            ['Mayonesa',680,1,1,75],
            ['Azúcar',387,0,100,0],
            ['Miel',304,0.3,82,0],
            ['Chocolate negro',546,4.9,61,31],
            ['Chocolate con leche',535,7.7,59,30],
            ['Cacao puro',228,20,50,14],
        ];

        $rows = array_map(fn($f) => [
            'name' => $f[0],
            'kcal_100' => $f[1],
            'protein_100' => $f[2],
            'carbs_100' => $f[3],
            'fat_100' => $f[4],
            'created_at' => $now,
            'updated_at' => $now,
        ], $foods);

        DB::table('foods')->insert($rows);
    }
}