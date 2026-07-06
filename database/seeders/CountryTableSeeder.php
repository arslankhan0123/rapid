<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = [
            [
                'name' => 'India',
            ],
            [
                'name' => 'Canada',
            ],
            [
                'name' => 'USA',
            ],
            [
                'name' => 'Germany',
            ],
            [
                'name' => 'Russia',
            ],
            [
                'name' => 'England',
            ],
            [
                'name' => 'UAE',
            ],
            [
                'name' => 'China',
            ],
            [
                'name' => 'Turkey',
            ],
        ];

        foreach ($input as $data) {
            Country::create($data);
        }
    }
}
