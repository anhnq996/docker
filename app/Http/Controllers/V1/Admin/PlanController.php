<?php

namespace App\Http\Controllers\V1\Admin;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\CreatePlanRequest;
use App\Http\Requests\Plan\DeletePlanRequest;
use App\Http\Requests\Plan\GetListPlanRequest;
use App\Http\Requests\Plan\ListPlanRequest;
use App\Http\Requests\Plan\SelectPlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\Plan\DetailPlanResource;
use App\Http\Resources\Plan\ListPlanCollection;
use App\Models\Plan;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListPlanRequest $request)
    {
        $plans = new Plan();
        $plans = $plans->list($request);

        return $this->response(ResponseCodes::S1000, ListPlanCollection::make($plans));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePlanRequest $request)
    {
        $data = $request->only(['name', 'price', 'properties', 'duration_time', 'is_best_seller']);

        if ($request->get('is_best_seller')) {
            Plan::query()->update([
                'is_best_seller' => false,
            ]);
        }
        $plan = new Plan();
        $plan->fill($data);
        $plan->save();

        //response
        return $this->response(ResponseCodes::S1000);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GetListPlanRequest $request)
    {
        $plan = Plan::query()
            ->select(['id', 'name', 'price', 'properties', 'duration_time', 'is_best_seller'])
            ->where('id', $request->get('id'))
            ->first();

        return $this->response(ResponseCodes::S1000, DetailPlanResource::make($plan));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlanRequest $request)
    {
        $data = $request->only(['name', 'price', 'properties', 'duration_time', 'is_best_seller']);

        if ($request->get('is_best_seller')) {
            Plan::query()->update([
                'is_best_seller' => false,
            ]);
        }

        Plan::query()->find($request->get('id'))
            ->update($data);

        return $this->response(ResponseCodes::S1000);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeletePlanRequest $request)
    {
        Plan::query()->find($request->get('id'))
            ->delete();

        return $this->response(ResponseCodes::S1000);
    }

    /**
     *
     * @param \App\Http\Requests\Plan\SelectPlanRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select(SelectPlanRequest $request): JsonResponse
    {
        $keyword = $request->get('keyword') ?? null;

        return $this->response(
            ResponseCodes::S1000,
            Plan::query()->select(['id', 'name', 'price'])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                })->get()
        );
    }
}
