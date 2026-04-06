// Laravel Starter Kit - API Usage Examples with JavaScript

const API_BASE_URL = 'http://localhost:8000/api';

// ========================================
// 1. LOGIN
// ========================================
async function login(email, password) {
    try {
        const response = await fetch(`${API_BASE_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password }),
        });

        const data = await response.json();

        if (response.ok) {
            // Save token to localStorage
            localStorage.setItem('api_token', data.token);
            console.log('Login successful:', data.user);
            return data;
        } else {
            console.error('Login failed:', data);
            throw new Error(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Error during login:', error);
        throw error;
    }
}

// ========================================
// 2. GET CURRENT USER
// ========================================
async function getCurrentUser() {
    const token = localStorage.getItem('api_token');
    
    if (!token) {
        throw new Error('No authentication token found');
    }

    try {
        const response = await fetch(`${API_BASE_URL}/me`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        const data = await response.json();

        if (response.ok) {
            console.log('Current user:', data);
            return data;
        } else {
            console.error('Failed to get user:', data);
            throw new Error(data.message || 'Failed to get user');
        }
    } catch (error) {
        console.error('Error getting current user:', error);
        throw error;
    }
}

// ========================================
// 3. GET ALL USERS (Admin only)
// ========================================
async function getAllUsers() {
    const token = localStorage.getItem('api_token');
    
    if (!token) {
        throw new Error('No authentication token found');
    }

    try {
        const response = await fetch(`${API_BASE_URL}/users`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        const data = await response.json();

        if (response.ok) {
            console.log('Users:', data);
            return data;
        } else if (response.status === 403) {
            console.error('Access denied: Admin role required');
            throw new Error('You do not have permission to access this resource');
        } else {
            console.error('Failed to get users:', data);
            throw new Error(data.message || 'Failed to get users');
        }
    } catch (error) {
        console.error('Error getting users:', error);
        throw error;
    }
}

// ========================================
// 4. LOGOUT
// ========================================
async function logout() {
    const token = localStorage.getItem('api_token');
    
    if (!token) {
        throw new Error('No authentication token found');
    }

    try {
        const response = await fetch(`${API_BASE_URL}/logout`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        const data = await response.json();

        if (response.ok) {
            // Remove token from localStorage
            localStorage.removeItem('api_token');
            console.log('Logout successful:', data.message);
            return data;
        } else {
            console.error('Logout failed:', data);
            throw new Error(data.message || 'Logout failed');
        }
    } catch (error) {
        console.error('Error during logout:', error);
        throw error;
    }
}

// ========================================
// USAGE EXAMPLES
// ========================================

// Example 1: Login and get current user
async function example1() {
    try {
        // Login
        await login('admin@example.com', 'password');
        
        // Get current user info
        const user = await getCurrentUser();
        console.log('User roles:', user.roles);
        console.log('User permissions:', user.roles.map(r => r.permissions).flat());
    } catch (error) {
        console.error('Example 1 failed:', error);
    }
}

// Example 2: Get all users (admin only)
async function example2() {
    try {
        // Login as admin
        await login('admin@example.com', 'password');
        
        // Get all users
        const usersData = await getAllUsers();
        console.log('Total users:', usersData.data.length);
        
        // Display each user's roles
        usersData.data.forEach(user => {
            console.log(`${user.name}: ${user.roles.map(r => r.name).join(', ')}`);
        });
    } catch (error) {
        console.error('Example 2 failed:', error);
    }
}

// Example 3: Handle permission denied (non-admin trying to access users)
async function example3() {
    try {
        // Login as regular user
        await login('user@example.com', 'password');
        
        // Try to get all users (will fail with 403)
        await getAllUsers();
    } catch (error) {
        console.error('Expected error - Permission denied:', error.message);
    }
}

// Example 4: Complete authentication flow
async function example4() {
    try {
        // Login
        console.log('Logging in...');
        await login('admin@example.com', 'password');
        
        // Verify authentication
        console.log('Checking authentication...');
        await getCurrentUser();
        
        // Perform authenticated action
        console.log('Fetching users...');
        await getAllUsers();
        
        // Logout
        console.log('Logging out...');
        await logout();
        
        console.log('Authentication flow completed successfully!');
    } catch (error) {
        console.error('Authentication flow failed:', error);
    }
}

// ========================================
// HELPER: Check if user has specific role
// ========================================
async function userHasRole(roleName) {
    try {
        const user = await getCurrentUser();
        return user.roles.some(role => role.name === roleName);
    } catch (error) {
        console.error('Error checking role:', error);
        return false;
    }
}

// ========================================
// HELPER: Check if user has specific permission
// ========================================
async function userHasPermission(permissionName) {
    try {
        const user = await getCurrentUser();
        return user.roles.some(role => 
            role.permissions.some(permission => permission.name === permissionName)
        );
    } catch (error) {
        console.error('Error checking permission:', error);
        return false;
    }
}

// Usage:
// if (await userHasRole('admin')) {
//     console.log('User is an admin');
// }
//
// if (await userHasPermission('create-users')) {
//     console.log('User can create users');
// }

// ========================================
// Run examples (uncomment to test)
// ========================================
// example1();
// example2();
// example3();
// example4();
