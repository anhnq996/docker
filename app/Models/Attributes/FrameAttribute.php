<?php

namespace App\Models\Attributes;

use Illuminate\Support\Facades\Storage;

/**
 * @property string avatar
 */
trait FrameAttribute
{
    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getFrameAttribute(): string
    {
        return $this->attributes['frame'] ? Storage::url($this->attributes['frame']) : '';
    }
}
