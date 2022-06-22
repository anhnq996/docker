<?php

namespace App\Enums;

enum RewardForm: int
{
    case EMAIL    = 1;
    case FACEBOOK = 2;
    case BOTH     = 3;

    public function label(): string
    {
        return match ($this) {
            self::EMAIL    => 'Nhập email',
            self::FACEBOOK => 'Chia sẻ facebook',
            self::BOTH     => 'Cả hai',
        };
    }
}


