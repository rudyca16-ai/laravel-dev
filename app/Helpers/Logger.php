<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Logger
{
    /**
     * Log para info
     */
    public static function info(string $message, array $context = []): void
    {
        Log::info($message,$context);
    }

    /**
     * Log para warning
     */
    public static function warning(string $message, array $context = []): void
    {
        Log::warning($message,$context);
    }

    /**
     * Log para error
     */
    public static function error(string $message, array $context = []): void
    {
        Log::error($message,$context);
    }
}
