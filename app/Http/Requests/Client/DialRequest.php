<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class DialRequest extends FormRequest
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
            'game_id' => 'required|exists:games,id',
            'email'   => 'required|email_custom',
            'phone'   => 'required|phone_format',
            'name'    => 'required'
        ];
    }
}