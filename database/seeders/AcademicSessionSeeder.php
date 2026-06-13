<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicSession;

class AcademicSessionSeeder extends Seeder
{
    public function run(): void
    {
        AcademicSession::create([
            'name' => '2025/2026 - Semester 1',
            'is_active' => true,
        ]);

        AcademicSession::create([
            'name' => '2025/2026 - Semester 2',
            'is_active' => false,
        ]);
    }
}