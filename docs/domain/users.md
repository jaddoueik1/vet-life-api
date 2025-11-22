# Users Domain

## Overview
Provides core application user management, authentication, authorization hooks, and role assignments.

## Models
- **User** (`App\\Domain\\Users\\Models\\User`): standard user model with support for API tokens, contact info, and role relationships.
- **Role**: represents permission groupings, attached to users via pivot.

## Services & Repositories
- **UserService** / **UserRepository**: abstractions available for user-related business logic and persistence patterns.
- **AuthService**: placeholder for authentication-related helper logic.

## Policies
- **UserPolicy**: authorization rules governing user access (can be expanded for granular permissions).

## HTTP Controllers
- **AuthController**: token-based authentication endpoints.
  - `login`: validates credentials, checks hashed password, and issues a Sanctum token with the user and roles.
  - `logout`: deletes the current access token.
  - `me`: returns the authenticated user with role assignments.
- **UserController**: CRUD for users with role synchronization.
  - `index`: paginated users including roles.
  - `store`: validates name/email/password, creates user, hashes password, and syncs roles by slug.
  - `update`: partial updates with email uniqueness, optional password rehashing, and role sync when provided.
  - `destroy`: deletes a user.

## Integration Points
- Users are referenced as veterinarians in appointments and visits (`assigned_vet_id`/`vet_id`) and may map to staff roles for authorization.
