<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;

class DeliveryOrderSeeder extends Seeder
{
    public function run(): void
    {
        $do = DeliveryOrder::create([
            'noshipment'    => '1100000001',
            'nodo'          => '2000000001',
            'nopo'          => 'PO-2025-001',
            'delivery_date' => '2025-10-01',
            'source'        => 'P00001',
            'destination'   => 'Q00001',
            'tara_weight'   => 3000,
            'gross_weight'  => 3500,
            'created_by'    => 'system',
            'update_by'     => 'system',
            'last_update'   => now(),
        ]);

        DeliveryOrderItem::create([
            'nodo'          => $do->nodo,
            'doitem'        => 1,
            'material_code' => 'S-001',
            'qty_plan'      => 35000,
            'uom'           => 'KG',
            'created_by'    => 'system',
            'update_by'     => 'system',
            'last_update'   => now(),
        ]);
    }
}
