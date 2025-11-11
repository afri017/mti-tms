<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\POItem;

class POItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nopo' => 'PO-2025-00001', 'itempo' => 1, 'material_code' => 'S-001', 'qty' => 15000, 'uom' => 'Ton'],
            ['nopo' => 'PO-2025-00001', 'itempo' => 2, 'material_code' => 'S-002', 'qty' => 10000, 'uom' => 'Ton'],
            ['nopo' => 'PO-2025-00001', 'itempo' => 3, 'material_code' => 'S-003', 'qty' => 15000, 'uom' => 'Ton'],
            ['nopo' => 'PO-2025-00001', 'itempo' => 4, 'material_code' => 'S-004', 'qty' => 25000, 'uom' => 'Ton'],
            ['nopo' => 'PO-2025-00002', 'itempo' => 1, 'material_code' => 'S-002', 'qty' => 30000, 'uom' => 'Ton'],
            ['nopo' => 'PO-2025-00002', 'itempo' => 2, 'material_code' => 'S-003', 'qty' => 20000, 'uom' => 'Ton'],
        ];

        foreach ($items as $item) {
            POItem::create($item);
        }
    }
}
