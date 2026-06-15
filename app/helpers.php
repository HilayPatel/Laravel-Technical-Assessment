<?php

use App\Models\Log;

if (!function_exists('add_log')) {
    /**
     * Add a log entry.
     */
    function add_log(int $uploadId, string $level, string $message)
    {
        if ($uploadId) {
            Log::create([
                'upload_id' => $uploadId,
                'level' => $level,
                'message' => $message,
            ]);
        }
    }
}
