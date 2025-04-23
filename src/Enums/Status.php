<?php

namespace App\Enums;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public static function exists(string $value): bool
    {
        return null !== self::tryFrom($value);
    }
}
