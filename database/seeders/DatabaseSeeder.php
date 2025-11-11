<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CustomerSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            CustomerSeeder::class,
            MaterialSeeder::class,
            SourceSeeder::class,
            TonnageSeeder::class,
            DriverSeeder::class,
            PurchaseOrderSeeder::class,
            POItemSeeder::class,
            RouteSeeder::class,
            VendorSeeder::class,
            DeliveryOrderSeeder::class,
            TruckSeeder::class,
            GateSeeder::class,
            ShipmentCostSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
