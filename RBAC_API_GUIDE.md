# Laravel Starter Kit - RBAC & Sanctum API

This Laravel starter kit includes a complete Role-Based Access Control (RBAC) system with UI management and Laravel Sanctum API authentication.

## Features

### 1. RBAC - Roles & Permissions
- Complete CRUD interface for managing Roles
- Complete CRUD interface for managing Permissions
- Assign multiple permissions to roles
- Clean, LaravelDaily-style Blade UI

**Access:**
- Roles: `/roles`
- Permissions: `/permissions`

### 2. User Management
- User CRUD with role assignment
- Assign multiple roles to users
- Password management (optional update)
- View users with their assigned roles

**Access:**
- Users: `/users`

### 3. Sanctum API
Complete REST API with authentication and authorization.

**Endpoints:**

#### Authentication
```bash
POST /api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}

Response:
{
    "token": "1|xxxxx...",
    "user": { ... }
}
```

#### Get Users (Requires admin role)
```bash
GET /api/users
Authorization: Bearer {token}

Response:
{
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": [
                {
                    "id": 1,
                    "name": "admin",
                    "permissions": [...]
                }
            ]
        }
    ]
}
```

#### Get Current User
```bash
GET /api/me
Authorization: Bearer {token}
```

#### Logout
```bash
POST /api/logout
Authorization: Bearer {token}
```

## Demo Users

The seeder creates these demo users:

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password | admin |
| editor@example.com | password | editor |
| user@example.com | password | user |

## Middleware Usage

### Web Routes
```php
// Require specific role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', ...);
});

// Require specific permission
Route::middleware(['auth', 'permission:create-posts'])->group(function () {
    Route::post('/posts', ...);
});
```

### API Routes
```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/api/users', [UserController::class, 'index']);
});
```

## User Model Methods

```php
// Check if user has role
$user->hasRole('admin'); // returns bool

// Check if user has permission
$user->hasPermission('create-posts'); // returns bool

// Assign role
$user->assignRole('editor');
$user->assignRole($roleModel);

// Remove role
$user->removeRole('editor');

// Get user's roles
$user->roles; // Collection of Role models

// Access through relationships
$user->roles()->with('permissions')->get();
```

## Database Structure

### Tables
- `roles` - Role definitions
- `permissions` - Permission definitions
- `role_user` - Many-to-many pivot between roles and users
- `permission_role` - Many-to-many pivot between permissions and roles
- `personal_access_tokens` - Sanctum API tokens

### Relationships
- Users → Many-to-Many → Roles
- Roles → Many-to-Many → Permissions
- Users have Permissions through Roles

## Testing API with cURL

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Get Users (with auth)
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Permissions Created by Seeder

- view-users
- create-users
- edit-users
- delete-users
- view-roles
- create-roles
- edit-roles
- delete-roles
- view-permissions
- create-permissions
- edit-permissions
- delete-permissions

## UI Navigation

The sidebar includes a "User Management" section with:
- Users
- Roles
- Permissions

All pages follow the LaravelDaily design style with:
- Breadcrumb navigation
- Clean card-based layouts
- Responsive tables
- Dark mode support
- Success message notifications

## Customization

### Add New Permissions
1. Go to `/permissions/create`
2. Enter permission name (e.g., `publish-posts`)
3. Assign to roles via `/roles/{role}/edit`

### Create New Roles
1. Go to `/roles/create`
2. Enter role name
3. Select permissions to assign
4. Users with this role will inherit all selected permissions

### Assign Roles to Users
1. Go to `/users/{user}/edit`
2. Check the roles you want to assign
3. Save - user immediately has access to all permissions from those roles

## Security Notes

- All API routes require authentication via Sanctum
- Role and permission checks happen via middleware
- Passwords are hashed using Laravel's default hasher
- CSRF protection enabled for web routes
- API uses token-based authentication (no cookies required)
