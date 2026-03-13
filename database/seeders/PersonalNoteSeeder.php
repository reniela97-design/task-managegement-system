<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersonalNoteSeeder extends Seeder
{
    public function run(): void
    {
        $notes = [
            [
                'user_id' => 2,
                'note_date' => Carbon::now(),
                'note_text' => 'Remember to review the authentication module today',
            ],
            [
                'user_id' => 3,
                'note_date' => Carbon::now(),
                'note_text' => 'Schedule meeting with the design team',
            ],
            [
                'user_id' => 4,
                'note_date' => Carbon::now()->addDay(),
                'note_text' => 'Complete the payment gateway integration',
            ],
            [
                'user_id' => 5,
                'note_date' => Carbon::now(),
                'note_text' => 'Update the project documentation',
            ],
            [
                'user_id' => 6,
                'note_date' => Carbon::now()->addDays(2),
                'note_text' => 'Prepare for the security audit',
            ],
            [
                'user_id' => 7,
                'note_date' => Carbon::now(),
                'note_text' => 'Review test cases for the new features',
            ],
            [
                'user_id' => 2,
                'note_date' => Carbon::now()->addDays(3),
                'note_text' => 'Client meeting at 2 PM',
            ],
        ];

        foreach ($notes as $note) {
            DB::table('personal_notes')->insert([
                'user_id' => $note['user_id'],
                'note_date' => $note['note_date'],
                'note_text' => $note['note_text'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}