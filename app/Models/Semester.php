<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Semester.php

class Semester extends Model
{
    protected $fillable = [
        'semester',
        'academic_year',
        'admin_id', // important!
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function students()
{
    return $this->belongsToMany(Student::class, 'semester_student', 'semester_id', 'student_id')
                ->withPivot('payment_status')
                ->withTimestamps();
}

    
}
