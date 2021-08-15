<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'first_name' => 'Me',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'phone' => '0211234567',
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'is_admin'=>1
        ]);
        \App\Models\User::factory(30)->create();
    }
}
