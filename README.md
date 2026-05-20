# Boneh Star Backend

A robust backend application for the Boneh Star bus management and booking platform, built with Laravel 13.

## 🚀 Tech Stack

- **Framework:** [Laravel 13](https://laravel.com)
- **Language:** PHP 8.4
- **Authentication:** [Laravel Sanctum](https://laravel.com/docs/sanctum)
- **Permissions:** [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission)
- **Testing:** [Pest PHP](https://pestphp.com)
- **Tools:** Laravel Boost, Laravel Pail, Laravel Pint

## 🛠️ Key Features

- **Bus Management:** Manage bus fleets, categories, facilities, and images.
- **Booking System:** Comprehensive booking workflow for customers.
- **Transaction & Payment:** Handle payments and transaction history.
- **Role-Based Access Control (RBAC):** Granular permissions using Spatie Permission.
- **Exporting:** Export bookings and income data (Excel/PDF).
- **Notifications:** System for handling user notifications.

## 📋 Prerequisites

- PHP >= 8.4
- Composer
- Node.js & NPM (for frontend assets if needed)
- Database (MySQL/PostgreSQL/SQLite)

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd boneh-star-backend
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your database and other environment variables in `.env`.*

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Run the Application:**
   ```bash
   composer dev
   ```
   *This uses `concurrently` to run the server, queue listener, and Vite.*

## 🧪 Testing

Run the test suite using Pest:
```bash
composer test
```

## 🏗️ Project Structure

```text
boneh-star-backend/
├── app/
│   ├── Exports/            # Excel/PDF export logic
│   ├── Http/
│   │   ├── Controllers/    # API & Web controllers
│   │   ├── Middleware/     # Request middleware
│   │   ├── Requests/       # Form request validation
│   │   └── Resources/      # Eloquent API resources
│   ├── Models/             # Eloquent database models
│   ├── Repositories/       # Data access layer (Repository pattern)
│   ├── Services/           # Business logic layer
│   └── Providers/          # Application service providers
├── config/                 # Application configuration files
├── database/
│   ├── migrations/         # Database schema definitions
│   └── seeders/            # Database seeders
├── routes/
│   ├── api.php             # API route definitions
│   └── web.php             # Web route definitions
├── tests/
│   ├── Feature/            # Feature/Integration tests
│   └── Unit/               # Unit tests
└── vendor/                 # Composer dependencies
```

## 💅 Code Standards

This project uses **Laravel Pint** for code formatting. Before committing, run:
```bash
vendor/bin/pint --format agent
```

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
