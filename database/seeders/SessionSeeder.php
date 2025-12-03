<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sessions')->insert([
            [
                'id' => (string) Str::uuid(),
                'user_id' => 1,
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'payload' => serialize(['logged_in' => true]),
                'last_activity' => now()->timestamp,
            ],
        ]);
    }
}


