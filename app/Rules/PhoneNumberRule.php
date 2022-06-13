<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumberRule implements Rule
{
    protected $alias = 'phone_format';
    private $type;
    public const REGEX = [
        'd' => "/^[0][\d]{9}$/",
    ];

    /**
     * Create a new rule instance.
     *
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
        return preg_match(self::REGEX[$this->type], $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.phone_format');
    }
}
