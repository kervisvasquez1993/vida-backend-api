<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'payment_id',
        'transaction_id',  // ID de la transacción proporcionada por la pasarela
        'status',  // Estado de la transacción
    ];
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
