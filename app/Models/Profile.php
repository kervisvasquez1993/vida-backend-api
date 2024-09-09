<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'img_url',
        'address',
        'user_id',
        'date_of_birth',
        'gender'
    ];
}

// NUEVO CLIENTE 
 