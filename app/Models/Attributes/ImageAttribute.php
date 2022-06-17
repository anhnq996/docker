<?php

namespace App\Models\Attributes;

use Illuminate\Support\Facades\Storage;

/**
 * @property string avatar
 */
trait ImageAttribute
{
    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getImageAttribute(): string
    {
        return $this->attributes['image'] ? Storage::url($this->attributes['image']) : '';
    }
}
