<?php

namespace App\Enums;

enum SyncLogStatusEnum: string
{
    case PENDING = 'PENDING';
    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
