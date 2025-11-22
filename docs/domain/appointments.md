# Appointments Domain

## Overview
Handles scheduling lifecycle for patient appointments, including creation, updates, retrieval, and cancellation.

## Models
- **Appointment** (`App\\Domain\\Appointments\\Models\\Appointment`): stores patient appointment details such as patient reference, scheduled time, status, notes, and assigned veterinarian (`assigned_vet_id`). Relationships:
  - `patient()` -> belongs to **Patient**
  - `vet()` -> belongs to **User** (assigned veterinarian)

## Services
- **AppointmentService**: wraps appointment creation and dispatches the `AppointmentCreated` domain event after persisting a record.

## HTTP Controllers
- **AppointmentController**: RESTful controller for appointments.
  - `index`: paginated list with patient and vet relations.
  - `store`: validates required `patient_id`, `scheduled_at`, `status`, optional notes and assigned vet, then delegates to the service for creation.
  - `show`: loads patient and vet for a single appointment.
  - `update`: allows updating schedule, status, and notes.
  - `destroy`: deletes the appointment.

## Events
- **AppointmentCreated**: domain event emitted when a new appointment is created to trigger downstream workflows.
