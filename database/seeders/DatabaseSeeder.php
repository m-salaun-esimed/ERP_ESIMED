<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insertion des statuts de projets dans project_statuses
        DB::table('project_statuses')->insert([
            [
                'name' => 'prospect',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'terminé',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'annulé',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
                 [
                'name' => 'demarré',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('quote_statuses')->insert([
            ['name' => 'envoyée'],
            ['name' => 'accepté'],
            ['name' => 'refusé'],
            ['name' => 'en attente'],
        ]);

        DB::table('invoice_statuses')->insert([
            ['name' => 'éditée',],
            ['name' => 'envoyée'],
            ['name' => 'payée'],
        ]);
    }
}
