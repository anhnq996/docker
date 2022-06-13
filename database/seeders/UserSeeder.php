<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'anhnq',
                'email' => 'anhnq1@yopmail.com',
                'phone' => '0968844776',
                'password' => Hash::make('Abc!123'),
                'plan_id' => 1,
                'start_date' => '2022-06-01',
                'end_date' => '2022-08-30'
            ],
            [
                'id' => 2,
                'name' => 'anhnq2',
                'email' => 'anhnq2@yopmail.com',
                'phone' => '0968854776',
                'password' => Hash::make('Abc!123'),
                'plan_id' => 2,
                'start_date' => '2022-06-01',
                'end_date' => '2022-08-30'
            ],
        ];

        foreach ($users as $user) {
            $user = User::query()->firstOrCreate(['id' => $user['id']], $user);
            $user->assignRole('administrator');
        }
    }
}
