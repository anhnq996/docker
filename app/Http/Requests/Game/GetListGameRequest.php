<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class GetListGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return $this->user()->can('games');
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
            'keyword' => 'nullable',
            'status'  => 'nullable|numeric'
        ];
    }
}
