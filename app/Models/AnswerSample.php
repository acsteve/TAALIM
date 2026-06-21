<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnswerSample extends Model
{
    use HasFactory;

    protected $fillable = ['assessment_id', 'category', 'file_path', 'filename'];

    /**
     * Get the assessment that owns the answer sample.
     */
    public function assessment() 
    {
        return $this->belongsTo(Assessment::class);
    }
}