<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Course;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get a course to link to
        $course = Course::first();

        if (!$course) {
            $this->command->error("No courses found. Run CourseSeeder first.");
            return;
        }

        // 2. Define all subjects in an array
        $subjects = [
            ['subject_code' => 'BCS3143', 'subject_name' => 'Software Quality Assurance'],
            ['subject_code' => 'BCS3113', 'subject_name' => 'Software Engineering'],
            ['subject_code' => 'BCS3213', 'subject_name' => 'Software Project Management'],
            ['subject_code' => 'BCS3313', 'subject_name' => 'Software Analysis and Design'],
            ['subject_code' => 'BCS3413', 'subject_name' => 'Software Testing'],
            ['subject_code' => 'BCS3233', 'subject_name' => 'Software Evolution and Maintenance'],
            ['subject_code' => 'BCS3433', 'subject_name' => 'Software Engineering Practices'],
        ];

        // 3. Loop through and create/update each
        foreach ($subjects as $subjectData) {
            Subject::updateOrCreate(
                ['subject_code' => $subjectData['subject_code']],
                [
                    'subject_name' => $subjectData['subject_name'],
                    'course_id'    => $course->id,
                    // Lecturer assignments removed
                ]
            );
        }

        $this->command->info("All " . count($subjects) . " subjects have been successfully seeded.");
    }
}