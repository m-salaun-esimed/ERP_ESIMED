<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Project statuses
        DB::table('project_statuses')->insert([
            ['name' => 'prospect',      'created_at' => now(), 'updated_at' => now()], // pas encore confirmé
            ['name' => 'terminé',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'annulé',        'created_at' => now(), 'updated_at' => now()],
            ['name' => 'en cours',      'created_at' => now(), 'updated_at' => now()],
        ]);


        // Quote statuses
        DB::table('quote_statuses')->insert([
            ['name' => 'envoyé'],
            ['name' => 'accepté'],
            ['name' => 'refusé'],
            ['name' => 'en attente'],
        ]);

        // Invoice statuses
        DB::table('invoice_statuses')->insert([
            ['name' => 'brouillon'],
            ['name' => 'envoyée'],
            ['name' => 'payée'],
        ]);
    }
}
