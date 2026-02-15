<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'admission_date',
        'roll_number',
        'previous_school',
        'transport_required',
        'hostel_required'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->enrollments()->where('is_active', true)->first();
    }

    public function currentGrade()
    {
        return $this->currentEnrollment()->grade ?? null;
    }
}
