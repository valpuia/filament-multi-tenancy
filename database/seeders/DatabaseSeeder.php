<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'is_admin' => true,
            'is_team_owner' => true,
        ]);

        $team = Team::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'is_super' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('team_user')->insert([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }
}
