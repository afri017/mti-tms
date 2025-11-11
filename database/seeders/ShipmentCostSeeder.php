<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShipmentCost;
use Carbon\Carbon;

class ShipmentCostSeeder extends Seeder
{
    public function run(): void
    {
        ShipmentCost::create([
            'idvendor' => 'TR001',
            'route' => 'R00001',
            'type_truck' => 'T1',
            'price_freight' => 1200000,
            'price_driver' => 200000,
            'validity_start' => Carbon::create(2025, 10, 21),
            'validity_end' => Carbon::create(2026, 10, 21),
            'active' => 'Y',
        ]);
    }
}
