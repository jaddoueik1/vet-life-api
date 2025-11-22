# Patients Domain

## Overview
Captures pet owners and their patients, supporting CRUD operations, detailed retrieval, and linkage to visits and invoicing.

## Models
- **Owner** (`App\\Domain\\Patients\\Models\\Owner`): stores client contact details (`name`, `email`, `phone`, `address`).
- **Patient** (`App\\Domain\\Patients\\Models\\Patient`): represents an animal with `owner_id`, `name`, `species`, `breed`, `age`, and `sex`. Key relationships:
  - `owner()` -> belongs to **Owner**.
  - `visits` -> links to visit history including related vet, attachments, medications.
  - `invoices` -> associated billing records with line items and payments.

## HTTP Controllers
- **OwnerController**: CRUD for owners with validation on contact fields.
- **PatientController**: patient CRUD plus a detailed view.
  - `index`: paginated patients with owner.
  - `store`: validates owner reference and patient demographics.
  - `show`: returns patient with owner.
  - `details`: richer view loading owner, visits (and nested vet/attachments/medications), and invoices with line items and payments.
  - `update` / `destroy`: standard modifications and deletion.
