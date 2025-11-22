# Security Audit

## Summary
Key findings center on insufficient authorization and information disclosure. The API currently allows any authenticated principal to perform administrative actions and exposes deployment configuration without authentication. Some write paths also bypass validation, increasing the risk of data tampering.

## Findings

### 1) No role- or permission-based authorization on administrative resources
- Evidence: All resource routes (users, staff, appointments, inventory, invoicing, etc.) only require `auth:sanctum` middleware; no gates/policies are checked in the controllers. For example, the `users` resource is fully exposed to any authenticated caller, with no authorization checks before creating, updating, or deleting users. 【F:routes/api.php†L20-L44】【F:app/Domain/Users/Http/Controllers/UserController.php†L13-L71】
- Risk: Any authenticated user can escalate privileges by creating privileged accounts, editing roles, or deleting other users and clinical data. This is a horizontal privilege-escalation vulnerability across the API surface.
- Recommendation: Introduce Laravel authorization (policies or gates) for each controller action, enforce role-based access aligned to the Employee Access plugin rules, and add tests to prevent regressions. Consider middleware that denies access when a feature is enabled but the caller lacks the mapped role permission.

### 2) Deployment configuration endpoint exposed without authentication
- Evidence: `/config` is publicly reachable and returns the entire `deployment-config.yml`, including plugin settings and feature flags. 【F:routes/api.php†L18-L24】【F:app/Http/Controllers/ConfigController.php†L10-L26】
- Risk: Discloses operational details (feature toggles, plugin choices, message templates) and could leak secrets if added to the configuration file. Attackers can enumerate enabled capabilities and tailor attacks accordingly.
- Recommendation: Require authentication (and ideally an admin-only permission) for configuration introspection. Filter or redact any sensitive values before returning them.

### 3) Inventory updates accept unvalidated, unguarded payloads
- Evidence: `InventoryController@update` applies no validation and writes the raw request body to the model. 【F:app/Domain/Inventory/Http/Controllers/InventoryController.php†L41-L45】
- Risk: Clients can persist unexpected types or malformed values (e.g., negative reorder levels), increasing data integrity issues. Coupled with the missing authorization above, any authenticated user can tamper with inventory records.
- Recommendation: Validate update payloads with explicit rules (e.g., required types, bounds) and restrict assignable attributes via form requests or DTOs. Add authorization checks for inventory modifications.
