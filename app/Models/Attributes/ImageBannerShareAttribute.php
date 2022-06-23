<?php

namespace App\Models\Attributes;

use Illuminate\Support\Facades\Storage;

/**
 * @property string avatar
 */
trait ImageBannerShareAttribute
{
    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getBannerImageShareAttribute(): string
    {
        return $this->attributes['banner_image_share'] ? Storage::url($this->attributes['banner_image_share']) : '';
    }
}
