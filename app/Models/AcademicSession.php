<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model // Changed
{
    use HasFactory;

    protected $table = 'academic_sessions';

    protected $fillable = [
        'name', 
        'is_active'
    ];

    public static function activate($id)
    {
        self::where('is_active', true)->update(['is_active' => false]);
        self::where('id', $id)->update(['is_active' => true]);
    }
}