<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'staff_id' => 'ADMIN001',
                'name'     => 'System Admin',
                'email'    => 'admin@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'admin',
            ],
            [
                'staff_id' => 'LECT001',
                'name'     => 'Dr. Ahmad',
                'email'    => 'ahmad@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],
            [
                'staff_id' => 'LECT002',
                'name'     => 'Dr. Siti',
                'email'    => 'siti@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],
            [
                'staff_id' => 'LECT003',
                'name'     => 'Dr. Bala',
                'email'    => 'bala@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],
            [
                'staff_id' => 'LECT004',
                'name'     => 'Prof. Yusof',
                'email'    => 'yusof@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],

            [
                'staff_id' => 'LECT005',
                'name'     => 'Prof. Aiman',
                'email'    => 'aiman@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],

                        [
                'staff_id' => 'LECT006',
                'name'     => 'Prof. Hazim',
                'email'    => 'hazim@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],

                        [
                'staff_id' => 'LECT007',
                'name'     => 'Prof. Suresh',
                'email'    => 'suresh@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'lecturer',
            ],
                        [
                'staff_id' => 'sqa001',
                'name'     => 'sqa',
                'email'    => 'sqa@ump.edu.my',
                'password' => Hash::make('123456'),
                'role'     => 'sqa',
            ],
        ];

        foreach ($users as $user) {
            // We use updateOrCreate to avoid duplicate errors if you run the seeder twice
            User::updateOrCreate(
                ['staff_id' => $user['staff_id']], 
                $user
            );
        }
    }
}