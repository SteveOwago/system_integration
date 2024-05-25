<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@moringaschool.com',
                'phone'=>'254713218312',
                'password'=> bcrypt('password@123'),
                'created_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Student Moringa',
                'email' => 'student@moringaschool.com',
                'phone'=>'254708444398',
                'password'=> bcrypt('password@123'),
                'created_at' => \Carbon\Carbon::now(),
            ],

        ];
        User::insert($users);

        $user = User::findOrFail(1);
        $user->assignRole('Admin');

        $user = User::findOrFail(2);
        $user->assignRole('Student');
    }
}
