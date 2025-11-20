# Vet Life API

A modular Laravel backend for single-clinic veterinary hospital deployments with feature flags and plugins.

## Setup
1. Install PHP 8.2+, Composer, and MySQL/MariaDB.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and configure database and Sanctum settings.
4. Place per-deployment configuration in `deployment-config.yml`.

## Docker
Build and run the API with Docker and MySQL:

```bash
# Copy your environment file and set DB credentials
cp .env.example .env
# Update DB_HOST to "db" inside .env for container networking
# Generate a secure app key
APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
# Build and start the stack
APP_KEY=$APP_KEY docker compose up --build
```

- The API listens on http://localhost:8000.
- MySQL is exposed on port 3306 with credentials sourced from your `.env` or defaults in `docker-compose.yml`.
- Run additional Artisan commands against the running container, e.g. migrations or seeders:

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed
```

## Migrations
Run migrations and queues:
```bash
php artisan migrate
php artisan queue:work
```

## Seeders
Populate the database with realistic demo data for users, clients, appointments, invoices, and inventory:
```bash
php artisan db:seed
# or with Docker
docker compose exec app php artisan db:seed
```

Default login examples seeded for testing:
- Administrator/Vet: `ana.silva@example.com` / `password`
- Veterinarian: `rafael.costa@example.com` / `password`
- Receptionist: `camila.ribeiro@example.com` / `password`
- Inventory Manager: `lucas.mendes@example.com` / `password`

## Feature Flags
Feature availability is configured in `deployment-config.yml` under `features`. Routes are protected by the `feature` middleware
 defined in `app/Http/Middleware/EnsureFeatureEnabled.php`.

## Plugins
Plugins are registered through `App\Core\Plugins\PluginRegistry` and can be toggled via `deployment-config.yml` under `plugins`.
 Example plugins include WhatsApp reminders, chat bot, employee access, and low stock alerts.

## User Roles
Roles are stored in the `roles` table and attached to users via the `role_user` pivot. The `EmployeeAccessPlugin` reads role per
missions from configuration and registers Gates accordingly.

## Scheduler & Queues
Background work can be processed with Laravel's queue worker. Use the scheduler (`php artisan schedule:run`) for periodic tasks
such as expiry checks used by the LowStockAlert plugin.

## API
See `openapi.yaml` for endpoints and schemas covering auth, users, appointments, patients, visits, inventory, and invoicing.
