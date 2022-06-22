<?php

namespace App\Enums;

enum RewardType: int
{
    case GIFT  = 1;
    case TURNS = 2;

    public function label(): string
    {
        return match ($this) {
            self::GIFT  => 'Phần quà',
            self::TURNS => 'Lượt quay',
        };
    }
}


