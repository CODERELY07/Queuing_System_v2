
# MedQue

MedQue is a real-time queuing system built for clinics and service counters. Clients pull a ticket from a kiosk, staff call patients in from a live dashboard, admins manage queues and staff accounts, and a public display screen shows who's being served — all synced live over WebSockets.

## Features

-   **Kiosk ticketing** — clients select a service and receive a queue number, no login required.
    
-   **Staff dashboard** — call the next patient, recall the previous one, or jump to a specific ticket.
    
-   **Admin panel** — manage queues (including clearing old entries) and staff accounts (CRUD).
    
-   **Public display** — a waiting-room screen showing patients currently being served.
    
-   **Live updates** — queue state broadcasts to every screen in real time via Laravel Reverb.
    
-   **Role-based dashboards** — a single `/dashboard` route routes users by their `user_type` (e.g. admin, staff).
    

## Tech Stack

**Layer**

**Technology**

Backend

Laravel 12 (PHP 8.2+)

Real-time

Laravel Reverb + Laravel Echo

Auth

Laravel Breeze

Frontend

Blade, Alpine.js, Tailwind CSS

Build tool

Vite

Database

MySQL

Testing

Pest

## Requirements

-   PHP 8.2+
    
-   Composer
    
-   Node.js & npm
    
-   MySQL
    

## Installation

### 1. Clone the repository

Bash

```
git clone https://github.com/CODERELY07/Queuing_System_v2.git
cd Queuing_System_v2

```

### 2. Install PHP dependencies

Bash

```
composer install

```

### 3. Install JavaScript dependencies

Bash

```
npm install

```

### 4. Configure environment

Bash

```
cp .env.example .env

```

Then update `.env` with your:

-   Database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
    
-   Reverb configuration — run `php artisan reverb:install` if you haven't set one up yet
    

### 5. Generate the application key

Bash

```
php artisan key:generate

```

### 6. Run migrations and seed the database

Bash

```
php artisan migrate --seed

```

### 7. Start the app

Run each in its own terminal:

Bash

```
php artisan serve       # Laravel app
php artisan reverb:start # WebSocket server
npm run dev              # Vite dev server

```

The app will be available at `http://localhost:8000`.

## Key Routes

**Route**

**Purpose**

`/`

Home / landing page

`/kiosk`

Client-facing kiosk to pull a queue ticket

`/display`

Public waiting-room display

`/display/registration`

Public display for registration

`/display/doctor-consultation`

Public display for doctor consultation

`/display/pharmacy`

Public display for pharmacy

`/display/emergency`

Public display for emergency

`/staff/*`

Staff endpoints for calling patients

`/admin/queues`

Admin queue management

`/admin/staff`

Admin staff management

`/dashboard`

Role-based dashboard redirect

## Testing

Bash

```
composer test

```

## License  

This project is proprietary. All rights reserved.
