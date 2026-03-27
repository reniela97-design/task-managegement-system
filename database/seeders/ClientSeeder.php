<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'client_name' => 'ABC Corporation',
                'client_contact_person' => 'Robert Chen',
                'client_contact_number' => '+1-555-0101',
                'client_user_id' => 2,
            ],
            [
                'client_name' => 'XYZ Industries',
                'client_contact_person' => 'Maria Garcia',
                'client_contact_number' => '+1-555-0102',
                'client_user_id' => 2,
            ],
            [
                'client_name' => 'Tech Solutions Inc',
                'client_contact_person' => 'James Wilson',
                'client_contact_number' => '+1-555-0103',
                'client_user_id' => 3,
            ],
            [
                'client_name' => 'Global Systems',
                'client_contact_person' => 'Patricia Lee',
                'client_contact_number' => '+1-555-0104',
                'client_user_id' => 3,
            ],
            [
                'client_name' => 'Innovation Labs',
                'client_contact_person' => 'Michael Brown',
                'client_contact_number' => '+1-555-0105',
                'client_user_id' => 4,
            ],
            [
                'client_name' => 'Digital Dynamics',
                'client_contact_person' => 'Emma Davis',
                'client_contact_number' => '+1-555-0106',
                'client_user_id' => 4,
            ],
            [
                'client_name' => 'Future Enterprises',
                'client_contact_person' => 'William Taylor',
                'client_contact_number' => '+1-555-0107',
                'client_user_id' => 5,
            ],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->insert([
                'client_name' => $client['client_name'],
                'client_contact_person' => $client['client_contact_person'],
                'client_contact_number' => $client['client_contact_number'],
                'client_user_id' => $client['client_user_id'],
                'client_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'client_inactive' => 0,
            ]);
        }
    }
}