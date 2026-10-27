<?php

/*
|--------------------------------------------------------------------------
| Demo accounts
|--------------------------------------------------------------------------
|
| This is a portfolio project: every account here is seeded demo data, not
| a real clinic. The list lives in one place so the seeder (which creates
| the accounts) and the login page (which offers one-click sign-in to each)
| can never drift apart.
|
| Set DEMO_ACCOUNTS=false before pointing this at anything real — it hides
| the login panel. (The seeder still creates the accounts either way; change
| or delete them before going live.)
|
*/

return [

    'enabled' => (bool) env('DEMO_ACCOUNTS', true),

    // Linked from the public footer; leave empty to hide the link.
    'repository' => env('DEMO_REPOSITORY_URL', 'https://github.com/CODERELY07/Queuing_System_v2'),

    // `service` is the name of the seeded department the account belongs to.
    'accounts' => [
        ['role' => 'Admin', 'name' => 'admin', 'user_type' => 'admin', 'service' => 'Admin', 'email' => 'admin@medqueue.test', 'password' => 'admin12345'],
        ['role' => 'Staff · Registration', 'name' => 'Registration Staff', 'user_type' => 'staff', 'service' => 'Registration', 'email' => 'registration@medqueue.test', 'password' => 'staff12345'],
        ['role' => 'Staff · Doctor Consultation', 'name' => 'Doctor Consultation Staff', 'user_type' => 'staff', 'service' => 'Doctor Consultation', 'email' => 'doctor@medqueue.test', 'password' => 'staff12345'],
        ['role' => 'Staff · Pharmacy', 'name' => 'Pharmacy Staff', 'user_type' => 'staff', 'service' => 'Pharmacy', 'email' => 'pharmacy@medqueue.test', 'password' => 'staff12345'],
        ['role' => 'Staff · Emergency', 'name' => 'Emergency Staff', 'user_type' => 'staff', 'service' => 'Emergency', 'email' => 'emergency@medqueue.test', 'password' => 'staff12345'],
    ],

];
