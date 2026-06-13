<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Section extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'section_name',
        'lecturer_name',
    ];

    /**
     * The "booted" method of the model.
     * Intercepts the deleting event to automatically purge associated physical files and records.
     */
    protected static function booted(): void
    {
        static::deleting(function ($section) {
            // Fetch all reports directly tied to this section
            foreach ($section->courseReports as $report) {
                // Delete the physical PDF file from storage disk if it exists
                if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                    Storage::disk('public')->delete($report->file_path);
                }
                
                // Explicitly delete the database entry row 
                $report->delete();
            }
        });
    }

    /**
     * Get the subject that owns this academic section slot.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the midterm evaluation reports associated with this specific section.
     * * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseReports(): HasMany
    {
        return $this->hasMany(CourseReport::class, 'section_id');
    }
}