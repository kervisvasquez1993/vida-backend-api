<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoMovil extends Model
{
    use HasFactory;
    protected $table = 'pago_movil';
    protected $fillable = [
        'bank',
        'identification',
        'phone'
    ];
}
