<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'password',
        'role',
        'course_id', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: A User (specifically a KP) belongs to a Course/Program.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'sqa_subject_assignments', 'sqa_id', 'subject_id');
    }
}