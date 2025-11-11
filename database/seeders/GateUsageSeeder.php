<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GateUsage;

class GateUsageSeeder extends Seeder
{
    public function run(): void
    {
        GateUsage::create([
            'gate' => 'G1',
            'noshipment' => '1100000001',
            'delivery_date' => '2025-10-01',
            'timestart' => '06:00:00',
            'timeend' => '06:20:00',
        ]);
    }
}
