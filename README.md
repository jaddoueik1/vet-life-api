# Vet Life API

A modular Laravel backend for single-clinic veterinary hospital deployments with feature flags and plugins.

## Setup
1. Install PHP 8.2+, Composer, and MySQL/MariaDB.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and configure database and Sanctum settings.
4. Place per-deployment configuration in `deployment-config.yml`.

## Migrations
Run migrations and queues:
```bash
php artisan migrate
php artisan queue:work
```

## Feature Flags
Feature availability is configured in `deployment-config.yml` under `features`. Routes are protected by the `feature` middleware defined in `app/Http/Middleware/EnsureFeatureEnabled.php`.

## Plugins
Plugins are registered through `App\Core\Plugins\PluginRegistry` and can be toggled via `deployment-config.yml` under `plugins`. Example plugins include WhatsApp reminders, chat bot, employee access, and low stock alerts.

## User Roles
Roles are stored in the `roles` table and attached to users via the `role_user` pivot. The `EmployeeAccessPlugin` reads role permissions from configuration and registers Gates accordingly.

## Scheduler & Queues
Background work can be processed with Laravel's queue worker. Use the scheduler (`php artisan schedule:run`) for periodic tasks such as expiry checks used by the LowStockAlert plugin.

## API
See `openapi.yaml` for endpoints and schemas covering auth, users, appointments, patients, visits, inventory, and invoicing.
