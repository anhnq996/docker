<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                  => 'required',
            'email'                 => 'required|unique:users,email,except,id|email_custom',
            'phone'                 => 'required|phone_format',
            'password'              => 'required',
            'password_confirmation' => 'required|same:password',
            'plan_id'               => 'required|numeric',
            'start_date'            => 'required|date_format:Y-m-d',
            'end_date'              => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ];
    }
}
