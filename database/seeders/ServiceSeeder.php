<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        
        $services = [
            ['name' => 'Registration', 'prefix' => 'R'],
            ['name' => 'Doctor Consultation', 'prefix' => 'D'],
            ['name' => 'Pharmacy' , 'prefix' => 'P'],
            ['name' => 'Emergency' , 'prefix' => 'E'],
            ['name' => 'Admin' , 'prefix' => 'A'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                ['prefix' => $service['prefix']]
            );
        }
    }
}
