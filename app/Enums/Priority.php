<?php

namespace App\Enums;

enum Priority: string
{
    case TRANSACTIONAL = 'transactional';
    case MARKETING = 'marketing';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function default(): string
    {
        return self::MARKETING->value;
    }
}
