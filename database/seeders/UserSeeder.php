<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Creates (or refreshes) the demo accounts listed in config/demo.php —
     * the same list the login page offers one-click sign-in for.
     */
    public function run(): void
    {
        foreach (config('demo.accounts') as $account) {
            $service = Service::where('name', $account['service'])->first();

            // A staff account needs its department to exist; skip rather
            // than fail the whole seed if an admin has since renamed it.
            if (!$service) {
                continue;
            }

            // Plain password: the model's 'hashed' cast hashes it.
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'user_type' => $account['user_type'],
                    'service_id' => $service->id,
                    'password' => $account['password'],
                ]
            );
        }
    }
}
