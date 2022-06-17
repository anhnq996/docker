<?php

namespace App\Models\Attributes;

use Illuminate\Support\Facades\Storage;

/**
 * @property string avatar
 */
trait PathAttribute
{
    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getPathAttribute(): string
    {
        return $this->attributes['path'] ? Storage::url($this->attributes['path']) : '';
    }
}
