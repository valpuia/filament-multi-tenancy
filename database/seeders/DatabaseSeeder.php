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
        ]);

        $team = Team::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('team_user')->insert([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }
}
