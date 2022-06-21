<?php

namespace App\Http\Controllers\V1\Homepage;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\HomePage\PlanRequest;
use App\Models\Plan;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    use ResponseTrait;

    public function index(PlanRequest $request): JsonResponse
    {
        $plans = new Plan();
        $plans = $plans->list($request, false);

        return $this->response(ResponseCodes::S1000, $plans);
    }
}
