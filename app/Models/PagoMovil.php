<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoMovil extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pago_movil';
    protected $fillable = [
        'bank',
        'identification',
        'phone'
    ];
}
