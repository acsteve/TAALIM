<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'session',
        'type',
        'section_id',
        'file_path',
        'user_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}