<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademicSessionSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
            SubjectSeeder::class           
        ]);
    }
}