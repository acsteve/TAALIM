<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SqaSubjectAssignment extends Model
{
    use HasFactory;

    protected $table = 'sqa_subject_assignments';

    protected $fillable = [
        'sqa_id', 
        'subject_id'
    ];

    // Relationship to the SQA User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sqa_id');
    }

    // Relationship to the Subject
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}