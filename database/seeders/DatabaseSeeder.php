<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Statuts de projets
        DB::table('project_statuses')->insert([
            ['name' => 'prospect', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'terminé',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'annulé',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'demarré',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // Statuts de devis
        DB::table('quote_statuses')->insert([
            ['name' => 'envoyée'],
            ['name' => 'accepté'],
            ['name' => 'refusé'],
            ['name' => 'en attente'],
        ]);

        // Statuts de factures
        DB::table('invoice_statuses')->insert([
            ['name' => 'éditée'],
            ['name' => 'envoyée'],
            ['name' => 'payée'],
        ]);

        // Clients
        DB::table('customers')->insert([
            [
                'name' => 'ESIMED',
                'contact_name' => 'envoyée',
                'address' => '73 marseille',
                'email' => 'hmichelon@esimed.fr',
                'phone_number' => '0658718700',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Projets
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
