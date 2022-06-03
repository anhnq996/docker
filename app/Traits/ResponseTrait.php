<?php

namespace App\Traits;

use App\Enums\ResponseCodes;
use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\ArrayShape;

trait ResponseTrait
{
    /**
     * @param ResponseCodes $code
     * @param mixed|null $data
     * @param array|null $errors
     * @param array|null $debug
     * @return JsonResponse
     */
    protected function response(ResponseCodes $code, mixed $data = null, array $errors = null, array $debug = null): JsonResponse
    {
        return response()->json(array_filter([
            'success' => intval($code->value) == ResponseCodes::S1000->value,
            'code'    => $code->value,
            'message' => __('codes.' . $code->name),
            'data'    => $data,
            'errors'  => $errors,
            'debug'   => config('app.debug', false) ? $debug : null,
        ], fn($fieldValue) => !is_null($fieldValue)));
    }
}
