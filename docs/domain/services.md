# Services Domain

## Overview
Defines billable clinic services with pricing and descriptive metadata, enabling association to visits.

## Models
- **Service** (`App\\Domain\\Services\\Models\\Service`): fields include `name`, `description`, `duration`, and `price`.

## HTTP Controllers
- **ServiceController**: CRUD for services with validation on name and price.
  - `index`: paginated list of services.
  - `store`: creates a service after validating required name and price plus optional description/duration.
  - `show`: returns a single service.
  - `update`: partial updates with validation safeguards.
  - `destroy`: deletes a service.

## Integration Points
- Services can be attached to visits (via `Visit::services()` relationship) and are leveraged when generating invoice line items from visits.
