<?php

namespace App\Http\Controllers\V1\Homepage;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\HomePage\CreateContactRequest;
use App\Models\Contact;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ResponseTrait;

    public function create(CreateContactRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'phone', 'content']);

        Contact::query()->create($data);

        return $this->response(ResponseCodes::S1000);
    }
}
