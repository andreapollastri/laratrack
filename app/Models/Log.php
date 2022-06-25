<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'code',
        'datetime',
        'error',
        'file',
        'line',
        'method',
        'url',
        'request',
        'header',
        'ip',
        'user_agent',
        'user_id',
        'error_check'
    ];
    public $timestamps = false;
}
