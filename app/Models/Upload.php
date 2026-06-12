<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upload extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'status',
        'total_rows'
    ];

    // One upload has many individual local product records
    public function records(): HasMany
    {
        return $this->hasMany(ImportRecord::class);
    }
}
