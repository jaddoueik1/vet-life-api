# Vendors Domain

## Overview
Maintains supplier records used across medications and inventory items for sourcing and procurement.

## Models
- **Vendor** (`App\\Domain\\Vendors\\Models\\Vendor`): stores vendor name plus primary/secondary contact information fields.

## HTTP Controllers
- **VendorController**: CRUD controller for vendors.
  - `index`: paginated vendor list.
  - `store`: validates core and contact details before creation.
  - `show`: returns a vendor.
  - `update`: partial updates with validation on contact fields.
  - `destroy`: deletes a vendor.

## Integration Points
- Vendors associate with medications and inventory items through pivot tables, optionally marking a primary vendor per item/medication.
