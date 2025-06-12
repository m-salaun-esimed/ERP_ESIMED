<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::table('quote_statuses')->insert([
            ['name' => 'refusé'],
            ['name' => 'en cours'],
            ['name' => 'accepté'],
        ]);
    }
}
