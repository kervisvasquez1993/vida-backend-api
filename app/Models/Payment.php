<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'client_id',
        'invoice_id',
        'payment_method_id',
        'amount',
        'status',
    ];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
