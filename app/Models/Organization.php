<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'type'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'organization_student');
    }



}
