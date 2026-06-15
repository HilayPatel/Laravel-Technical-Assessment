<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Log extends Model
{
    protected $fillable = [
        'upload_id',
        'level',
        'message',
    ];
}
