<?php

namespace App\Enums;

enum ProductStatusEnum: string
{
    case ACTIVE = 'ACTIVE';
    case DRAFT = 'DRAFT';
    case ARCHIVED = 'ARCHIVED';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}