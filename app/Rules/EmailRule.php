<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailRule implements Rule
{
    private $type;
    protected $alias = 'email_custom';
    public const REGEX = [
        //Default rfc
        'd' => '/^([a-zA-Z0-9])(([a-zA-Z0-9+\._-]{0,})[a-zA-Z0-9]){0,}@[a-zA-Z0-9]([a-zA-Z0-9+\._-]+)(\.[a-zA-Z0-9]+)+$/',
    ];

    /**
     * Create a new rule instance.
     * @param  string  $type
     */
    public function __construct($type = 'd')
    {
        $this->type = $type;
    }

    public function __toString()
    {
        return $this->alias;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return (!(strpos($value, "..")) && preg_match(self::REGEX[$this->type], $value)) || empty($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.email');
    }
}

