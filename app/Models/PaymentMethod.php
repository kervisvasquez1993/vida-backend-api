<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    // TODO: AGREGAR EL SOFTDELETE AL FINAL DE LAS PRUEBAS
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'provider',
        'description',
        'is_active'
    ];
}
