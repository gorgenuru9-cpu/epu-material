# IT Admin Role Setup Guide

## Overview
A new `it_admin` role has been created for system administrators who can manage users and system settings.

## Installation Steps

### 1. Run the Database Migration

Execute the SQL migration to add the IT admin role to your database:

```bash
# Using MySQL command line
mysql -u root -p property_request_system < database/add_it_admin_role.sql

# Or using phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select 'property_request_system' database
# 3. Go to SQL tab
# 4. Copy and paste the contents of database/add_it_admin_role.sql
# 5. Click 'Go'
```

### 2. Default IT Admin Account

The migration creates a default IT admin account:

- **Username**: `admin`
- **Password**: `admin123`
- **Full Name**: የአይቲ አስተዳዳሪ
- **Department**: it_admin
- **ID Number**: IT-ADMIN-001

**IMPORTANT**: Change this password immediately after first login!

## IT Admin Capabilities

### 1. User Management
- **View all users** - See complete list of system users
- **Create users** - Add new users to the system
- **Edit users** - Modify username, full name, and department
- **Delete users** - Remove users (with safety checks)
- **Reset passwords** - Generate new random passwords for users

### 2. System Monitoring
- **System statistics** - View total users, requests, items, permissions
- **Database information** - Monitor database size and table counts
- **Audit logs** - View recent system activities
- **User activity** - Track user actions across the system

### 3. Access Control
- IT admins have access to:
  - `/it-admin.php` - System administration dashboard
  - `/it-users.php` - User management page
  - All API endpoints for user management
  - Full audit log access
  - System reports

## Security Features

### 1. Role-Based Access Control
- Only users with `it_admin` department can access IT admin pages
- Automatic redirect to dashboard for unauthorized users
- Session-based authentication required

### 2. User Management Safety
- Cannot delete own account
- Cannot delete users with existing requests
- Password reset generates secure random passwords
- All actions are logged in audit trail

### 3. Password Security
- Passwords are hashed using PHP's `password_hash()`
- Random passwords use cryptographically secure `random_bytes()`
- Minimum 8-character passwords for reset

## Files Modified/Created

### Created:
1. `database/add_it_admin_role.sql` - Database migration
2. `public/api/users/update.php` - Update user API
3. `public/api/users/reset-password.php` - Reset password API
4. `public/api/users/delete.php` - Delete user API
5. `IT_ADMIN_ROLE_SETUP.md` - This file

### Modified:
1. `config/constants.php` - Added DEPT_IT_ADMIN constant
2. `views/components/sidebar.php` - Added IT admin menu section
3. `public/it-admin.php` - Added role checking and user management
4. `public/it-users.php` - Added it_admin to department options

## Sidebar Menu for IT Admins

IT admins will see a special menu section:

```
የአይቲ አስተዳደር (IT Administration)
├── 🔧 የስርዓት አስተዳደር (System Administration)
└── 👥 የተጠቃሚ አስተዳደር (User Management)
```

## Department Constants

The following constant has been added:

```php
define('DEPT_IT_ADMIN', 'it_admin');
```

## Database Schema Change

The `users` table `department` ENUM now includes:

```sql
ENUM(
    'requester',
    'requester_main_dept',
    'property_mgmt_main_dept',
    'property_mgmt_dept',
    'registry_office',
    'treasury',
    'it_admin'  -- NEW
)
```

## Usage Examples

### Creating Additional IT Admins

```sql
INSERT INTO users (username, password_hash, full_name, department, identification_number)
VALUES (
    'it_admin2',
    '$2y$10$...',  -- Use password_hash() in PHP
    'IT Admin Name',
    'it_admin',
    'IT-ADMIN-002'
);
```

### Checking IT Admin Access in Code

```php
if (Session::getDepartment() === DEPT_IT_ADMIN) {
    // IT admin specific code
}
```

### Restricting Pages to IT Admins

```php
if ($userDept !== DEPT_IT_ADMIN) {
    Session::setFlash('error', 'Access denied. IT Admin privileges required.');
    header('Location: /dashboard.php');
    exit;
}
```

## Testing

### 1. Login as IT Admin
1. Go to `http://localhost:8000/login.php`
2. Username: `admin`
3. Password: `admin123`

### 2. Verify Access
- Check sidebar shows IT admin menu
- Access `/it-admin.php` - should work
- Access `/it-users.php` - should work

### 3. Test User Management
- Edit a user
- Reset a user's password
- Try to delete a user (with and without requests)
- Create a new user

### 4. Test Security
- Login as non-IT admin user
- Try to access `/it-admin.php` - should redirect
- Try to access `/it-users.php` - should redirect

## Troubleshooting

### Migration Fails
- Check if `it_admin` already exists in ENUM
- Verify database connection
- Check MySQL user permissions

### Cannot Access IT Admin Pages
- Verify user department is exactly `it_admin`
- Check session is active
- Clear browser cache and cookies

### User Management Not Working
- Check API endpoints are accessible
- Verify database permissions
- Check browser console for JavaScript errors

## Best Practices

1. **Change default password immediately**
2. **Create separate IT admin accounts** for each administrator
3. **Regularly review audit logs** for suspicious activity
4. **Backup database** before making bulk user changes
5. **Document password resets** for compliance
6. **Limit number of IT admins** to essential personnel only

## Future Enhancements

Potential additions for IT admin role:

1. System backup and restore
2. Database maintenance tools
3. System configuration management
4. Email notification settings
5. Security settings and policies
6. System health monitoring
7. Performance analytics
8. Bulk user import/export
9. Role-based permissions management
10. System logs viewer

## Support

For issues or questions:
1. Check audit logs for error details
2. Review PHP error logs
3. Verify database schema matches migration
4. Test with default admin account first
