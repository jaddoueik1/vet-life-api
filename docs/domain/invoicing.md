# Invoicing Domain

## Overview
Handles invoice creation, retrieval, updates, payments, and integrations with visits for automatic line item generation.

## Models
- **Invoice** (`App\\Domain\\Invoicing\\Models\\Invoice`): represents billing for a patient/owner, linked optionally to a visit, with `status`, generated `number`, and `total`. Relationships include `lineItems` and `payments`.
- **InvoiceLineItem**: child lines holding `description`, `quantity`, and `price` contributing to invoice totals.
- **Payment**: records payments applied to an invoice with `amount`, optional `method`, and `paid_at` timestamp.

## Services
- **InvoiceService**:
  - Builds invoices from provided line items or auto-generates them from visit medications/services when `visit_id` is supplied without line items.
  - Creates invoices and associated line items within a transaction, assigns a generated invoice number, calculates totals, and dispatches `InvoiceCreated`.

## HTTP Controllers
- **InvoiceController**: CRUD and payment handling for invoices.
  - `index`: paginated invoices with line items.
  - `store`: validates patient, owner, optional visit, status, and line item payloads; delegates creation to the service.
  - `show`: loads an invoice with its line items.
  - `update`: mass-updates invoice attributes.
  - `destroy`: deletes an invoice.
  - `recordPayment`: validates payment details, ensures amount does not exceed remaining balance, creates the payment, and adjusts invoice status to `paid` or `partial` based on remaining amount.

## Events
- **InvoiceCreated**: domain event emitted after invoice creation to trigger follow-up workflows (e.g., notifications or accounting hooks).
