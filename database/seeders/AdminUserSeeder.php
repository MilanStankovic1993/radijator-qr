<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'milan.stankovic@radijator.rs'],
            [
                'name' => 'Milan Stankovic',
                'password' => Hash::make('28januar'),
                'email_verified_at' => now(),
            ]
        );
    }
}