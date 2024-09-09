<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'is_active',
        'profile_id',
        'plan_id',
    ];
    public function clientMikrotikAndDBLocal()
    {
        return $this->hasOne(ClientMikrotikAndDBLocal::class);
    }
}
