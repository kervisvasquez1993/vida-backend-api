<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryActivation extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'client_id',
        'change_status_data',
        'status'
    ];
}
