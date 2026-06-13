<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    // Added kp_id to allow mass assignment from your controller
    protected $fillable = [
        'course_code', 
        'course_name', 
        'kp_id'
    ];

    /**
     * Get the Ketua Program (KP) assigned to this course.
     * This links the course to the specific User who validates assessments.
     */
    public function kp()
    {
        return $this->belongsTo(User::class, 'kp_id');
    }

    /**
     * Get all users (Subject Coordinators, SMEs) assigned to this program.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'course_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'course_id');
    }
}