<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Delete existing users
        User::query()->delete();

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON;');

        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
        ]);
    }
}
