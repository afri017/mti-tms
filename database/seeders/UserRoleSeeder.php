<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ Buat permission
        $permissions = [
            'view customer', 'create customer', 'edit customer', 'delete customer',
            'view product', 'create product', 'edit product', 'delete product',
            'view shipment', 'create shipment', 'edit shipment', 'delete shipment',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2️⃣ Buat role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 3️⃣ Assign permission ke role
        $adminRole->syncPermissions(Permission::all());
        $userRole->syncPermissions(['view customer', 'view product', 'view shipment']);

        // 4️⃣ Buat user dengan updateOrCreate untuk ensure password correct
        // Note: Password akan di-hash otomatis karena User model punya cast 'hashed'
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password123'
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => 'password123'
            ]
        );

        // 5️⃣ Assign role ke user
        $admin->syncRoles([$adminRole]);
        $user->syncRoles([$userRole]);

        $this->command->info('✅ User, Role & Permission berhasil dibuat!');
        $this->command->info('   Admin: admin@example.com / password123');
        $this->command->info('   User: user@example.com / password123');
    }
}
