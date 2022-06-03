<?php

namespace App\Http\Controllers\V1\Admin;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::query()
            ->select(['id', 'name', 'price', 'properties'])
            ->get();

        $this->response(ResponseCodes::S1000, $plans);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only(['name', 'price', 'properties']);
        $plan = new Plan();
        $plan->fill($data);
        $plan->save();
        return $this->response(ResponseCodes::S1000);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::query()
            ->where('id', $id)
            ->select(['id', 'name', 'price', 'properties'])
            ->first();
        $this->response(ResponseCodes::S1000, $plan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'price', 'properties']);
        $plan = new Plan();
        $plan->fill($data);
        $plan->save();
        return $this->response(ResponseCodes::S1000);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Plan::query()->find($id)->delete();
        return $this->response(ResponseCodes::S1000);
    }
}
