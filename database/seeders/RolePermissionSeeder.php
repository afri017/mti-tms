<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar permission
        $permissions = [
            'view customer', 'create customer', 'edit customer', 'delete customer',
            'view product', 'create product', 'edit product', 'delete product',
            'view shipment', 'create shipment', 'edit shipment', 'delete shipment',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Buat role
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Assign permission ke role
        $admin->givePermissionTo(Permission::all());
        $user->givePermissionTo(['view customer', 'view product', 'view shipment']);

        // Assign role ke user pertama (optional)
        $user = User::first();
        if ($user) {
            $user->assignRole('admin');
        }

        $this->command->info('✅ Role & Permission berhasil dibuat!');
    }
}
