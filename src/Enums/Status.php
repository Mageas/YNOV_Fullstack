<?php

namespace App\Enums;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public static function exists(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
