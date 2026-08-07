# 🌟 Boneh Star Backend

A robust, enterprise-grade RESTful API backend for the **Boneh Star** bus management and booking platform. Built with **Laravel 13** and powered by **PHP 8.4**, this system is designed for high reliability, scalability, and strict security standards.

---

## 🚀 Tech Stack & Core Infrastructure

- **Framework:** [Laravel 13](https://laravel.com) (Modern routing, security, queueing, and mail services)
- **Language:** PHP 8.4+ (Strict typing, property promotion, and performance optimizations)
- **Authentication:** [Laravel Sanctum](https://laravel.com/docs/sanctum) (Secure token-based SPA/Mobile API authentication)
- **Authorization & RBAC:** [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission) (Role and permission management)
- **Database Engine:** MySQL or PostgreSQL (Relational schema with strict foreign keys and transactional safety)
- **Testing Engine:** [Pest PHP](https://pestphp.com) (Modern, expressive unit and integration testing)
- **Export Engine:** [Laravel Excel (Maatwebsite)](https://laravel-excel.com) & [DomPDF](https://github.com/barryvdh/laravel-dompdf) (Generation of spreadsheets and invoices)
- **Formatting Tools:** Laravel Pint (Strict code styling and linting compliance)

---

## 🛠️ Key Features

- **Decoupled Architecture:** Utilizes the **Repository & Service Pattern** to decouple database queries from business rules and controllers, ensuring clean, unit-testable code.
- **Dynamic Fleet Management:** Manage bus fleets, seating configurations, driver allocations, categories, facilities, and upload galleries.
- **End-to-End Booking Workflow:** Automated booking requests, quotation management (price updates, validity limits, customer discount tracking), payment validation, and reservation locking.
- **Quotation & Negotiation Engine:** Multi-stage negotiations allowing customers to propose revisions and counter-offers, with status synchronization.
- **Reporting & Financial Analytics:** Generation of detailed reports on monthly revenue, booking volumes, fleet usage statistics, and exports in PDF and Excel.
- **Secure File Storage:** Local and cloud storage adapters for driver licenses, transaction slips, and bus images with media URL resolving.
- **Security & Validation:** Strict request validation using Laravel `FormRequest` classes and sanitized API resource responses mapping model fields safely.

---

## 📋 Prerequisites

- **PHP** >= 8.4
- **Composer** (PHP Package Manager)
- **Database Server** (MySQL, PostgreSQL, or SQLite)
- **Redis** (Optional: For caching and queue driver)

---

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd boneh-star-backend
   ```

2. **Install composer dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment Variables:**
   Create a local configuration file:
   ```bash
   cp .env.example .env
   ```
   Generate the application encryption key:
   ```bash
   php artisan key:generate
   ```
   *Open `.env` and fill in your database credentials and connection type.*

4. **Run Migrations & Seeders:**
   Run the migration tool to build database schemas and seed it with roles, permissions, and demo bus categories:
   ```bash
   php artisan migrate --seed
   ```

5. **Generate Link for Public Storage:**
   Create a symbolic link from `public/storage` to `storage/app/public` so media uploads are publicly accessible:
   ```bash
   php artisan storage:link
   ```

6. **Start local servers:**
   To run the backend development server concurrently along with Vite and queue listeners:
   ```bash
   composer dev
   ```
   *Your server will be running at `http://localhost:8000`.*

---

## 🧪 Testing

The test suite is built using **Pest PHP** for clean, readable testing. To run both unit and feature integration tests:
```bash
composer test
```
To run tests with code coverage:
```bash
composer test -- --coverage
```

---

## 🏗️ Project Architecture & Directory Structure

We adhere to standard clean-code principles, decoupling layers using repositories for data access and services for business orchestrations.

```text
boneh-star-backend/
├── app/
│   ├── Exports/            # Excel exporting classes (using Laravel Excel)
│   ├── Http/
│   │   ├── Controllers/    # Thin controllers mapping HTTP requests to Service responses
│   │   ├── Middleware/     # Auth, roles, CORS, and security middleware
│   │   ├── Requests/       # FormRequest validation rules
│   │   └── Resources/      # API Resource wrappers to format JSON responses
│   ├── Models/             # Eloquent database models and relationships
│   ├── Repositories/       # Data Access Layer (queries and database mutations)
│   ├── Services/           # Business Logic Layer (core calculations, third-party integrations)
│   └── Providers/          # App service providers mapping bindings
├── config/                 # Framework config files (app, auth, database, sanctum)
├── database/
│   ├── migrations/         # Database schema files
│   └── seeders/            # System seeders (roles, permissions, admin credentials)
├── routes/
│   ├── api.php             # API routing (Sanctum guarded and public endpoints)
│   └── web.php             # Web-level routing and assets
├── tests/
│   ├── Feature/            # Integration and HTTP endpoint test suites
│   └── Unit/               # Component-level unit tests
├── .env                    # Local configuration variables
├── composer.json           # Composer dependencies and package definition
└── pint.json               # Code style formatting configurations
```

---

## 💅 Code Standards & Linting

We enforce clean formatting rules using **Laravel Pint**. Run Pint to clean and format all PHP classes automatically before pushing to version control:
```bash
composer format
```
*(Or run `vendor/bin/pint --format agent`)*

---

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). This backend API is custom-built for the Boneh Star Bus Platform.
