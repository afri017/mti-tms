<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['material_desc' => 'SMB', 'uom' => 'KG', 'konversi_ton' => 1000],
            ['material_desc' => 'Kedelai', 'uom' => 'KG', 'konversi_ton' => 1000],
            ['material_desc' => 'Gandum', 'uom' => 'KG', 'konversi_ton' => 1000],
            ['material_desc' => 'Jagung', 'uom' => 'KG', 'konversi_ton' => 1000],
        ];

        foreach ($materials as $m) {
            Material::create([
                'material_desc' => $m['material_desc'],
                'uom' => $m['uom'],
                'konversi_ton' => $m['konversi_ton'],
                'created_by' => 'system',
                'last_update' => now(),
            ]);
        }
    }
}
