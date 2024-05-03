<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
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
