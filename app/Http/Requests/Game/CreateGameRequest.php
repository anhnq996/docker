<?php

namespace App\Http\Requests\Game;

use App\Enums\GameStatus;
use App\Rules\FileExtension;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;


class CreateGameRequest extends FormRequest
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
            'name'              => 'required',
            'description'       => 'required',
            'banner'            => [new FileExtension([
                'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp']),
                'mimetypes:image/jpg,image/jpeg,image/png,image/bmp,image/gif,image/svg,image/webp',
                'nullable',
                'max:5120',
            ],
            'background'        => [new FileExtension([
                'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp']),
                'mimetypes:image/jpg,image/jpeg,image/png,image/bmp,image/gif,image/svg,image/webp',
                'nullable',
                'max:5120',
            ],
            'email_template'    => 'nullable',
            'rule'              => 'nullable',
            'reward_use_image'  => 'nullable',
            'redirect_url'      => 'required',
            'status'            => ['required', new Enum(GameStatus::class)] ,
            'start_at'          => 'required|date_format:Y-m-d H:i',
            'end_at'            => 'required|date_format:Y-m-d H:i|after_or_equal:start_at',
            'redirect_url'      => 'nullable',
            'reward'            => 'required|array',
            'reward.*.name'     => 'required',
            'reward.*.image'    => 'required',
            'reward.*.quantity' => 'required',
            'reward.*.percent'  => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $rewards = $this->reward;

        if ($rewards) {
            $validator->after(function ($validator) use ($rewards) {
                $precent = 0;
                foreach ($rewards as $reward) {
                    $precent += $reward['percent'];
                }
                if ($precent > 100) {
                    $validator->errors()->add('percent', __('validation.percent'));
                }
            });
        }
    }
}
