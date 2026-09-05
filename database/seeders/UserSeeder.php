<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminService = Service::where('name', 'Admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'user_type' => 'admin',
                'service_id' => $adminService?->id,
                'password' => Hash::make('admin12345'),
            ]
        );

        $staffAccounts = [
            'Registration' => 'registration@medqueue.test',
            'Doctor Consultation' => 'doctor@medqueue.test',
            'Pharmacy' => 'pharmacy@medqueue.test',
            'Emergency' => 'emergency@medqueue.test',
        ];

        foreach ($staffAccounts as $serviceName => $email) {
            $service = Service::where('name', $serviceName)->first();

            if (!$service) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "{$serviceName} Staff",
                    'user_type' => 'staff',
                    'service_id' => $service->id,
                    'password' => Hash::make('staff12345'),
                ]
            );
        }
    }
}
