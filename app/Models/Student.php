<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

// class Student extends Model
class Student extends Authenticatable
{
    protected $fillable = [
        'first_name',
        'last_name',
        'id_number',
        'year_level',
        'organization',
        "section",
        "payment_status",
        "contact_number",
    ];

    public function getAuthIdentifierName()
    {
        return 'id_number'; // userid as username
    }

    public function getAuthPassword()
    {
        return $this->last_name; // password is the last name
    }
    public function semesters()
{
    return $this->belongsToMany(Semester::class, 'semester_student', 'student_id', 'semester_id')
                ->withPivot('payment_status')
                ->withTimestamps();
}

    
public function organization()
{
    return $this->belongsTo(Organization::class);
}



    

}
