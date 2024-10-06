<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'is_active',
        'profile_id',
        'plan_id',
        'client_mikrowisp_id'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
