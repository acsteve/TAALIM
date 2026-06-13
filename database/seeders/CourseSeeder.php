<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'course_code' => 'BCS', 
                'course_name' => 'Software Engineering'
            ],
            [
                'course_code' => 'BCN', 
                'course_name' => 'Computer Systems & Networking'
            ],
            [
                'course_code' => 'BCC', 
                'course_name' => 'Cyber Security'
            ],
        ];

        foreach ($courses as $course) {
            // We use course_code as the unique identifier to check if it already exists
            Course::updateOrCreate(
                ['course_code' => $course['course_code']], 
                $course
            );
        }
    }
}