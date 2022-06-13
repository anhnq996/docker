<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class FileExtension implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $parameters;
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value instanceof UploadedFile) {
            return false;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        return $extension !== '' && in_array($extension, $this->parameters);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.file_extension', ['values' => join(',',$this->parameters)]);
    }
}
