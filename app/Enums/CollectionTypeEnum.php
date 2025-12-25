<?php

namespace App\Enums;

enum CollectionTypeEnum: string
{
    case CUSTOM = 'CUSTOM';
    case SMART = 'SMART';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
