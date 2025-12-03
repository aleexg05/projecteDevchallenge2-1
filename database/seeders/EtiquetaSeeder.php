<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Etiqueta;

class EtiquetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etiquetas = [
            'Urgent',
            'Opcional',
            'Important',
            'Per comprar aviat',
            'Ja en tinc',
        ];

        foreach ($etiquetas as $nom) {
            Etiqueta::create([
                'etiqueta_producte' => $nom,
            ]);
        }
    }
}
