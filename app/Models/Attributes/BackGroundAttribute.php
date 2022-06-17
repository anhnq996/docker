<?php

namespace App\Models\Attributes;

use Illuminate\Support\Facades\Storage;

/**
 * @property string avatar
 */
trait BackGroundAttribute
{
    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getBackgroundAttribute(): string
    {
        return $this->attributes['background'] ? Storage::url($this->attributes['background']) : '';
    }
}
