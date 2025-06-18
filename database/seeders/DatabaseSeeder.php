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
            ['name' => 'prospect', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'completed',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cancelled',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'started',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // Quote statuses
        DB::table('quote_statuses')->insert([
            ['name' => 'sent'],
            ['name' => 'accepted'],
            ['name' => 'refused'],
            ['name' => 'pending'],
        ]);

        // Invoice statuses
        DB::table('invoice_statuses')->insert([
            ['name' => 'drafted'],
            ['name' => 'sent'],
            ['name' => 'paid'],
        ]);

        // Clients
        DB::table('customers')->insert([
            [
                'name' => 'ESIMED',
                'contact_name' => 'sent',
                'address' => '73 marseille',
                'email' => 'hmichelon@esimed.fr',
                'phone_number' => '0658718700',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Projects
        DB::table('projects')->insert([
            [
                'status_project_id' => 4,
                'customer_id' => 1,
                'name' => 'ERP',
                'date_started' => '2025-06-17',
                'date_end' => '2025-06-17 11:01:45',
                'created_at' => '2025-06-17 11:01:45',
                'updated_at' => '2025-06-17 11:01:45',
            ]
        ]);
    }
}
