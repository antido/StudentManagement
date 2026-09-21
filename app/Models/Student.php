<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'age',
        'gender',
        'score'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
