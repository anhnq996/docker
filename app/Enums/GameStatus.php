<?php

namespace App\Enums;

enum GameStatus: int
{
    case ACTIVE   = 1;
    case UNACTIVE = 2;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE   => 'Active',
            self::UNACTIVE => 'UnActive',
        };
    }
}


