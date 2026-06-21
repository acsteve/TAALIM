<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

protected $fillable = [
        'user_id', 
        'subject_id',
        'title', 
        'type', 
        'session', 
        'status',
        'question_file', 
        'question_filename',
        'schema_file', 
        'schema_filename',

        'sme1_status', 'sme1_comments', 'sme1_verified_at', 'sme1_id',

        'sme2_status', 'sme2_comments', 'sme2_verified_at', 'sme2_id',
        
        'kp_status', 'kp_comments', 'kp_verified_at', 'kp_id',
    ];

    public function answerSamples()
    {
        return $this->hasMany(AnswerSample::class);
    }

    // =========================================================================
    // 📁 ADD THIS RELATIONSHIP HERE
    // =========================================================================
    /**
     * Relationship: An assessment folder tracks multiple checklist compliance reports.
     */
    public function courseReports()
    {
        return $this->hasMany(CourseReport::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship: The Coordinator who uploaded the file.
     */
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: The assigned SME 1.
     */
    public function sme1()
    {
        return $this->belongsTo(User::class, 'sme1_id');
    }

    /**
     * Relationship: The assigned SME 2.
     */
    public function sme2()
    {
        return $this->belongsTo(User::class, 'sme2_id');
    }

    /**
     * Relationship: The assigned Ketua Program (KP).
     */
    public function kp()
    {
        return $this->belongsTo(User::class, 'kp_id');
    }
}