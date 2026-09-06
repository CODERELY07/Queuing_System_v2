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
            // Internal bucket, not a real department — exists only so
            // admin accounts have *some* service_id to satisfy the
            // foreign key. Flagged is_internal so it's never renamed into
            // a public one (see the 2026_09_06_000001 migration).
            ['name' => 'Admin' , 'prefix' => 'A', 'is_internal' => true],
        ];

        foreach ($services as $service) {
            $record = Service::firstOrCreate(
                ['name' => $service['name']],
                ['prefix' => $service['prefix']]
            );

            // is_internal isn't mass-assignable (see Service::$fillable),
            // so it's set directly rather than through firstOrCreate's
            // create-attributes array.
            if (($service['is_internal'] ?? false) && !$record->is_internal) {
                $record->is_internal = true;
                $record->save();
            }
        }
    }
}
