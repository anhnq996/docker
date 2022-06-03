<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    private $plans = [
        [
            'id' => 1,
            'name' => 'Basic',
            'price' => 200000,
            'properties' => [
                'prop1' => 'Prop1',
                'prop2' => 'Prop2'
            ]
        ],
        [
            'id' => 2,
            'name' => 'Pro',
            'price' => 400000,
            'properties' => [
                'prop1' => 'Prop1',
                'prop2' => 'Prop2'
            ]
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->plans as $plan) {
            Plan::query()->firstOrCreate(['id' => $plan['id']], $plan);
        }
    }
}
