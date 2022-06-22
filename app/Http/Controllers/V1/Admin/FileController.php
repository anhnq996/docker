<?php

namespace App\Http\Controllers\V1\Admin;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetListFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Models\Attributes\PathAttribute;
use App\Models\File;
use App\Traits\CommonTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends Controller
{
    use ResponseTrait, CommonTrait;

    protected File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function uploadFile(UploadFileRequest $request): JsonResponse
    {
        $data         = $request->only('game_id', 'type');
        $image        = $this->uploadImage($request->file('file'), 'file');
        $data['path'] = $image['path'];

        File::query()->create($data);

        return $this->response(ResponseCodes::S1000);
    }

    public function list(GetListFileRequest $request): JsonResponse
    {
        $data = $this->file->list($request->get('game_id'));

        return $this->response(ResponseCodes::S1000, $data);
    }
}
