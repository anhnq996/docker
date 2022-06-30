<?php

namespace App\Http\Requests\Game;

use App\Enums\GameStatus;
use App\Enums\RewardForm;
use App\Enums\RewardType;
use App\Rules\FileExtension;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateGameRequest extends FormRequest
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
            'id'                 => 'required|exists:games,id',
            'name'               => 'nullable',
            'description'        => 'nullable',
            'banner'             => 'nullable',
            'frame'              => 'nullable',
            'background'         => 'nullable',
            'email_template'     => 'nullable',
            'rule'               => 'nullable',
            'redirect_url'       => 'required',
            'status'             => ['nullable', new Enum(GameStatus::class)],
            'start_at'           => 'nullable|date_format:Y-m-d H:i',
            'end_at'             => 'nullable|date_format:Y-m-d H:i|after_or_equal:start_at',
            'redirect_url'       => 'nullable',
            'font_size'          => 'nullable|numeric',
            'color'              => 'nullable',
            'free_turns'         => 'nullable|numeric',
            'title_game'         => 'nullable',
            'code_prefix'        => 'nullable',
            'image_share'        => [new FileExtension([
                'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp']),
                'mimetypes:image/jpg,image/jpeg,image/png,image/bmp,image/gif,image/svg,image/webp',
                'nullable',
                'max:5120',
            ],
            'content_share'      => 'nullable',
            'hashtag'            => 'nullable',
            'create_winner'      => 'boolean|nullable',
            'is_publish'         => 'boolean|nullable',
            'reward_form'        => ['nullable', new Enum(RewardForm::class)],
            'show_suffix'        => 'boolean',
            'reward'             => 'nullable|array',
            'reward.*.name'      => 'required',
            'reward.*.image'     => 'required',
            'reward.*.quantity'  => 'required',
            'reward.*.percent'   => 'required',
            'reward.*.type'      => ['required', new Enum(RewardType::class)],
            'reward.*.add_turn'  => 'nullable|numeric',
        ];
    }

    public function withValidator($validator)
    {
        $rewards = $this->reward;

        if ($rewards) {
            $validator->after(function ($validator) use ($rewards) {
                $precent = 0;
                foreach ($rewards as $reward) {
                    $precent += ($reward['percent'] ?? 0);
                }
                if ($precent > 100 || $precent < 100) {
                    $validator->errors()->add('percent', __('validation.percent'));
                }
            });
        }
    }
}
