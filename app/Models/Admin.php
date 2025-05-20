<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;  // This is important
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable // Extend the Authenticatable class
{
    use Notifiable;

    protected $fillable = [
        'username', // admin's username, can be same as organization name
        'password', // admin's password (hashed)
        // admin's plain password (for internal use)
        'name', // treasurer's name 
        'status',
        'role', // admin's role (e.g., 'admin', 'superadmin')
        'plain_password',
    ];

    // Implement getAuthIdentifier() method
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    // Implement getAuthIdentifierName() method
    public function getAuthIdentifierName()
    {
        return 'id';  // or 'username', depending on your setup
    }

    // Implement getAuthPassword() method
    public function getAuthPassword()
    {
        return $this->password;
    }

    
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

        public function students()
    {
        return $this->hasMany(Student::class, 'organization', 'username');
    }

    public function payments()
    {
        return $this->hasMany(SemesterStudent::class); // if using a model for the pivot
    }





}

