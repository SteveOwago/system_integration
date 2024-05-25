<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'admin_management']);
        Permission::create(['name' => 'student_management']);


        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleStudent = Role::create(['name' => 'Student']);

        $roleAdmin->givePermissionTo(Permission::all());
        $roleStudent->givePermissionTo(['admin_management','student_management']);
    }
}
