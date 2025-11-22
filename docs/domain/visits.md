# Visits Domain

## Overview
Coordinates clinical visit records, capturing diagnostics, treatments, and related resources while triggering downstream invoicing and inventory updates.

## Models
- **Visit** (`App\\Domain\\Visits\\Models\\Visit`): stores visit metadata (`patient_id`, `vet_id`, `summary`, `diagnosis`, `treatment`, `visit_date`). Relationships include patient, vet, services, medications, attachments, and equipment used.
- **Attachment**: file metadata for assets linked to a visit (e.g., labs, images).

## Services
- **VisitService**: orchestrates visit creation and association of medications, services, and equipment used.
  - Syncs pivot tables with default quantity `1` for provided IDs.
  - Dispatches `MedicationUsed` events for each medication and `VisitCreated` after creation.

## HTTP Controllers
- **VisitController**: CRUD facade for visits.
  - `index`: paginated visits with patient, vet, services, medications, and equipment used eager-loaded.
  - `store`: validates patient/vet IDs, visit details, and optional arrays of medication, service, and equipment IDs; delegates creation to the service.
  - `show`: returns a visit with related data.
  - `update`: mass-updates visit attributes.
  - `destroy`: deletes a visit.

## Events & Listeners
- **VisitCreated**: emitted after a visit is saved.
- **VisitCompleted**: placeholder event for completion workflows.
- **MedicationUsed**: emitted per medication association to facilitate stock deduction.
- **CreateInvoiceForVisit**: listener intended to generate invoices in response to visit events.
