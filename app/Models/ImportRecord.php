<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRecord extends Model
{
    protected $fillable = [
        'upload_id',
        'sku',
        'title',
        'status',
        'payload_data',
        'error_message'
    ];

    // Cast payload data to an array automatically when accessing
    protected $casts = [
        'payload_data' => 'array',
    ];

    // Link back to the parent upload metadata
    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class);
    }
}
