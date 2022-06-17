<?php

namespace App\Models\Attributes;

use Illuminate\Support\Facades\Storage;

/**
 * @property string avatar
 */
trait BannerAttribute
{
    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getBannerAttribute(): string
    {
        return $this->attributes['banner'] ? Storage::url($this->attributes['banner']) : '';
    }
}
