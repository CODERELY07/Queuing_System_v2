<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = Service::where('name', 'Admin')->first();
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'user_type' => 'admin',
            'service_id' => $service->id,
            'password' => Hash::make('admin12345'),
        ]);
    }
}
