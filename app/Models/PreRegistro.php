<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreRegistro extends Model
{
    use HasFactory;
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
