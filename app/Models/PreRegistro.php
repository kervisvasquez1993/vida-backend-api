<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreRegistro extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'cliente', 
        'cedula', 
        'direccion', 
        'telefono', 
        'movil', 
        'email', 
        'notas', 
        'fecha_instalacion', 
        'tecnico_id'
    ];

}
