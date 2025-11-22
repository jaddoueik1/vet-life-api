# Inventory Domain

## Overview
Manages clinic inventory including stocked items, batches, vendor sourcing, purchase orders, and stock movements. Supports low-stock monitoring and vendor associations.

## Models
- **InventoryItem** (`App\\Domain\\Inventory\\Models\\InventoryItem`): core item with `name`, `sku`, and optional `reorder_level`. Relationships:
  - `batches()` -> has many **StockBatch** records to track quantities and expiration.
  - `visits()` -> many-to-many with **Visit** via `inventory_item_visit` pivot (includes `quantity`).
  - `vendors()` -> many-to-many with **Vendor** including `is_primary` flag.
- **StockBatch**: individual stock entries for an inventory item with `quantity` and `expires_at`, belongs to an **InventoryItem**.
- **StockMovement**: audit of quantity changes with a `change` delta and `reason` metadata.
- **PurchaseOrder**: links to a **Supplier** with `status` and `total` fields.
- **Supplier**: contact details for suppliers used in purchase orders.

## Services
- **InventoryService**:
  - `createItem`: persists a new inventory item.
  - `adjustStock`: placeholder for stock adjustments; dispatches `StockLow` event when a negative change dips below reorder threshold.
  - `lowStockItems`: aggregates items with stock sums from batches and returns those at or below reorder level.

## HTTP Controllers
- **InventoryController**: CRUD for inventory items with vendor synchronization.
  - `index`: paginated items with vendors.
  - `store`: validates `name`, `sku`, optional `reorder_level`, and vendor assignments; creates item through service and syncs vendors (enforces single primary vendor).
  - `show`: fetches an item with vendors.
  - `lowStock`: lists items identified by the service as below reorder threshold.
  - `update`: partial updates with optional vendor resync, including primary-vendor constraint.
  - `destroy`: removes an inventory item.

## Events & Listeners
- **StockLow**: emitted on low-stock detection.
- **MedicationUsed**: signals medication consumption (consumed by inventory listeners for stock deduction).
- **StockExpired**: placeholder event for expiring batches.
- **DeductMedicationStock**: listener intended to react to medication usage events and decrement inventory.
