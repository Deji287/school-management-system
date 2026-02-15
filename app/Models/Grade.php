<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StudentEnrollment;
use App\Models\Teacher;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'numeric_value',
        'section',
        'class_teacher_id',
        'capacity',
        'academic_year'
    ];

    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function activeStudents()
    {
        return $this->enrollments()->where('is_active', true)->with('student')->get()->pluck('student');
    }
}
