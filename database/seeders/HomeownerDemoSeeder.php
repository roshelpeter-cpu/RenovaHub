<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HomeownerDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'homeowner@test.com'],
            [
                'name' => 'Roshel Peter',
                'password' => Hash::make('homeowner@123'),
                'role' => 'homeowner',
                'phone' => '0771234567',
                'address' => '42 Flower Road, Colombo 07',
                'email_verified_at' => now(),
            ],
        );
    }
}
