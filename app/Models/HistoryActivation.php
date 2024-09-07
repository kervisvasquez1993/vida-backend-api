<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryActivation extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'change_status_data',
        'status'
    ];
}
