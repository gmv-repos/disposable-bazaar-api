<?php

namespace App\Enums;


/**
 * Available statuses:
 * - PENDING (1)
 * - PROCESSING (2)
 * - ON_THE_WAY (3)
 * - CANCELED (4)
 * - COMPLETED (5)
 */
class OrderStatus
{
    const PENDING = 1;
    const PROCESSING = 2;
    const ON_THE_WAY = 3;
    const CANCELED = 4;
    const COMPLETED = 5;

    public static function badge($status): string
    {
        return match ($status) {
            self::PENDING => '<span class="badge bg-success w-100">PENDING</span>',
            self::PROCESSING => '<span class="badge bg-warning w-100">PROCESSING</span>',
            self::ON_THE_WAY => '<span class="badge bg-primary w-100">ON THE WAY</span>',
            self::CANCELED => '<span class="badge bg-danger w-100">CANCELED</span>',
            self::COMPLETED => '<span class="badge bg-success w-100">COMPLETED</span>',
            default => '<span class="badge bg-secondary w-100">UNKNOWN</span>',
        };
    }
}