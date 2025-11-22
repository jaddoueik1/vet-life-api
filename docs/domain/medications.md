# Medications Domain

## Overview
Maintains medication catalog data, pricing, stock metadata, and vendor associations, enabling linkage to visits and inventory tracking.

## Models
- **Medication** (`App\\Domain\\Medications\\Models\\Medication`): fields include `name`, `sku`, `description`, `price`, `current_stock`, and `reorder_level`. Relationships:
  - `visits()` -> many-to-many with **Visit** including `quantity` per visit.
  - `vendors()` -> many-to-many with **Vendor** including `is_primary` flag.

## HTTP Controllers
- **MedicationController**: CRUD with vendor management.
  - `index`: paginated medications with vendors.
  - `store`: validates medication attributes and optional vendors; enforces unique SKU; syncs vendors while enforcing a single primary vendor.
  - `show`: retrieves medication with vendors.
  - `update`: partial updates with validation/unique SKU handling and optional vendor resync (single-primary enforcement).
  - `destroy`: deletes a medication.

## Integration Points
- Vendor associations mirror inventory item handling for consistent supplier management.
- Visit attachments via pivot allow medication usage tracking for billing and potential stock deduction.
