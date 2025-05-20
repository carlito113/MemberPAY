<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YearSection extends Model
{

    protected $table = 'yearsections'; // Specify the table name if different from the model name
    protected $fillable = [
        'semester',
        'academic_year',
        'admin_id', // important!
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}

