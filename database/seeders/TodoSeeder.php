<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where(['email' => 'haky@haky.com']);

        $posts = \App\Models\Todo::factory()
            ->count(5)
            ->for($user)
            ->create();
    }
}
