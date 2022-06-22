<?php

namespace App\Http\Requests;

use App\Enums\FileType;
use App\Rules\FileExtension;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UploadFileRequest extends FormRequest
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
            'file' => [new FileExtension([
                'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp']),
                'mimetypes:image/jpg,image/jpeg,image/png,image/bmp,image/gif,image/svg,image/webp',
                'required',
                'max:5120',
            ],
            'game_id' => 'nullable|exists:games,id',
            'type'    => ['required', new Enum(FileType::class)],
        ];
    }
}
