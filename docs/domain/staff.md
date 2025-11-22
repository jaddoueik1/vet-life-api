# Staff Domain

## Overview
Manages clinic personnel records for both veterinarians and non-veterinarian staff, including contact details and roles.

## Models
- **StaffMember** (`App\\Domain\\Staff\\Models\\StaffMember`): shared model for all staff with `name`, `email`, `phone`, `role`, and optional `specialization` or `position`. Roles are constrained by constants `ROLE_VETERINARIAN` and `ROLE_STAFF`.

## HTTP Controllers
- **VeterinarianController**: CRUD scoped to veterinarian staff members.
  - `index`: paginated veterinarians filtered by role.
  - `store`: validates contact info and specialization, sets role to `veterinarian`.
  - `show` / `update` / `destroy`: restricted to veterinarian records (404 otherwise).
- **StaffController**: CRUD for non-veterinarian staff.
  - `index`: paginated staff filtered by role.
  - `store`: validates contact info and position, sets role to `staff`.
  - `show` / `update` / `destroy`: restricted to staff records (404 otherwise).

## Integration Points
- Staff members (particularly veterinarians) are linked to visits and appointments via `vet_id`/`assigned_vet_id` fields to track responsible clinicians.
