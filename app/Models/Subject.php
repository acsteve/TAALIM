<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Including the new assignment foreign keys.
     */
    protected $fillable = [
        'course_id', 
        'subject_code', 
        'subject_name',
        'coordinator_id',
        'sme1_id',
        'sme2_id'
    ];

    /**
     * A subject belongs to one Program/Course (e.g., BCS).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The Lecturer assigned as the Subject Coordinator.
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * The Lecturer assigned as the first Subject Matter Expert (Reviewer).
     */
    public function sme1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sme1_id');
    }

    /**
     * The Lecturer assigned as the second Subject Matter Expert (Reviewer).
     */
    public function sme2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sme2_id');
    }

    /**
     * A subject has many assessment templates (Tests, Quizzes).
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * A subject has many course reports generated across semesters.
     */
    public function courseReports(): HasMany
    {
        return $this->hasMany(CourseReport::class);
    }

    /**
     * Get all class sections registered under this subject.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'subject_id');
    }
    
    public function sqaAssignments(): HasMany
    {
        return $this->hasMany(\App\Models\SqaSubjectAssignment::class, 'subject_id');
    }

    
}