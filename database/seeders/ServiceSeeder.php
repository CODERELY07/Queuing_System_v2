<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        
        $services = [
            'Registration',
            'Doctor Consultation',
            'Pharmacy',
            'Emergency',
            'Admin'
        ];

        foreach ($services as $name) {
            Service::firstOrCreate(['name' => $name]);
        }
    }
}
