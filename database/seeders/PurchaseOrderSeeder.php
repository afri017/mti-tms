<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            ['idcustomer' => 'cu_00001', 'podate' => '2025-10-01', 'valid_to' => '2025-12-31'],
            ['idcustomer' => 'cu_00001', 'podate' => '2025-10-01', 'valid_to' => '2025-12-31'],
        ];

        foreach ($orders as $order) {
            PurchaseOrder::create([
                'idcustomer' => $order['idcustomer'],
                'podate' => $order['podate'],
                'valid_to' => $order['valid_to'],
                'created_by' => 'system',
                'last_update' => now(),
            ]);
        }
    }
}
