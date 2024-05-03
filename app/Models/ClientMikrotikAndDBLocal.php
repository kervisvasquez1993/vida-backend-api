<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMikrotikAndDBLocal extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_mikrotik_id',
    ];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
