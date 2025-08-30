# MedQue Project Setup


## Installation

### 1. Clone the repository
```bash
git clone https://github.com/CODERELY07/Queuing_System_v2.git
cd Queuing_System_v2
2. Install PHP dependencies


composer install
3. Install JavaScript dependencies


npm install && npm run build
4. Configure Environment
Copy .env_example file:

create .env file
paste the .env_example in .env file
Update .env with your:
Database credentials

Reverb configuration
php artisan reverb:install


5. Generate application key
php artisan key:generate

6. Run migrations & seed database


php artisan migrate --seed
7. Start Reverb server


php artisan reverb:start
8. Run the application


php artisan serve


npm run dev