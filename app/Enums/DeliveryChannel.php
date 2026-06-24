<?php

namespace App\Enums;

enum DeliveryChannel: string
{
    case SMS = 'sms';
    case EMAIL = 'email';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function default(): string
    {
        return self::EMAIL->value;
    }
}
