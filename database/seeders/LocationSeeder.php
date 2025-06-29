<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;


class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        for ($i = 1; $i <= 60; $i++) {
            Location::create(['id' => $i]);
        }
    }
}
