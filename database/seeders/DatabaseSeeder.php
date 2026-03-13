<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First, seed roles WITHOUT user_id references
        $this->call(RoleSeeder::class);
        
        // Then seed users with role_id references
        $this->call(UserSeeder::class);
        
        // Then update roles with user_id references after users exist
        $this->call(RoleUserUpdateSeeder::class);
        
        // Then seed the rest of the tables
        $this->call([
            PrioritySeeder::class,
            StatusSeeder::class,
            SystemSeeder::class,
            CategorySeeder::class,
            TypeSeeder::class,
            ClientSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            ActivitySeeder::class,
            PersonalNoteSeeder::class,
        ]);
    }
}