<?php

namespace App\Enums;

enum FileType: int
{
    case BACKGROUND = 1;
    case BANNER     = 2;
    case FRAME      = 3;

    public function label(): string
    {
        return match ($this) {
            self::BACKGROUND  => 'Background',
            self::BANNER      => 'Banner',
            self::FRAME       => 'Frame',
        };
    }
}


