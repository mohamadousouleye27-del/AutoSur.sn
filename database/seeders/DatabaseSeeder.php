<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = new User();
        $admin->forceFill([
            'name'              => 'Administrateur',
            'phone'             => '+221770000000',
            'email'             => 'admin@autosur.sn',
            'password'          => Hash::make('Admin@12345'),
            'role'              => 'admin',
            'phone_verified_at' => now(),
        ])->save();
    }
}