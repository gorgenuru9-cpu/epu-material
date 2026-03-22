# የንብረት ጥያቄ አስተዳደር ስርዓት
# Property Request Management System

A multi-stage approval workflow system for managing property withdrawal requests with full Amharic language support.

## Features

- ✅ Multi-stage approval workflow (5 departments)
- ✅ Form 20 modal for request submission
- ✅ Role-based access control
- ✅ Item registration system
- ✅ Inventory management with store assignment
- ✅ One-time item assignment per request
- ✅ **Official release permission documents**
- ✅ **Unique permission number generation**
- ✅ **Print-ready permission documents**
- ✅ Backup record keeping
- ✅ Complete audit trail
- ✅ Notification system
- ✅ Full Amharic language interface (150+ translations)
- ✅ Responsive design with Amharic font support
- ✅ CSRF protection
- ✅ Session management with 30-minute timeout
- ✅ Account lockout after 5 failed login attempts

## System Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)

## Installation

### 1. Database Setup

```bash
# Create database and tables
php database/init.php
```

This will:
- Create the `property_request_system` database
- Create all required tables with UTF-8mb4 encoding
- Create sample users for all departments

### 2. Configuration

Edit `config/database.php` with your database credentials:

```php
return [
    'host' => 'localhost',
    'dbname' => 'property_request_system',
    'username' => 'root',
    'password' => '',
];
```

### 3. Web Server Configuration

#### Apache

Create `.htaccess` in the `public` directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 4. Permissions

```bash
chmod -R 755 public
chmod -R 777 logs
```

## Sample Login Credentials

After running `database/init.php`, you can login with:

| Department | Username | Password |
|------------|----------|----------|
| Requester | requester1 | password123 |
| Requester Main Dept | requester_main1 | password123 |
| Property Mgmt Main Dept | property_main1 | password123 |
| Property Mgmt Dept | property_dept1 | password123 |
| Registry Office | registry1 | password123 |
| Treasury | treasury1 | password123 |

## Workflow

1. **Requester** submits Form 20 request for property withdrawal
2. **Requester Main Department** reviews and approves/rejects
3. **Property Management Main Department** reviews and approves/rejects
4. **Property Management Department** registers item with requester ID and approves/rejects
5. **Registry Office** creates backup record and approves/rejects
6. **Treasury** assigns item from store inventory
7. **Treasury** issues release permission document
8. **Requester** prints permission and collects item

### Treasury Store Assignment and Release

The Treasury department can:
- View available inventory items with stock levels
- Assign items from store to requesters by ID number
- Track item assignments (one item per request)
- Automatically reduce stock when items are assigned
- **Issue official release permission documents**
- **Generate unique permission numbers (RP-YYYYMMDD-XXXX)**

### Release Permission Document

When Treasury releases an item:
- A unique permission number is generated
- Official release permission document is created
- Requester receives notification with permission number
- Requester can view and print the permission document
- Document includes:
  - Permission number
  - Requester information
  - Item details (name, code, quantity)
  - Issue date and issuer
  - Signature sections for Treasury and Requester

## Project Structure

```
property-request-system/
├── config/              # Configuration files
│   ├── constants.php   # Status constants and system settings
│   └── database.php    # Database credentials
├── database/            # Database schema and initialization
│   ├── schema.sql      # Main database schema
│   ├── init.php        # Database initialization script
│   ├── add_inventory.sql  # Inventory tables schema
│   └── add_inventory.php  # Inventory initialization
├── lang/                # Amharic translations
│   └── am.php          # 150+ Amharic translations
├── public/              # Public web files
│   ├── css/            # Stylesheets with Amharic font support
│   ├── js/             # JavaScript files
│   ├── api/            # API endpoints (partial)
│   ├── index.php       # Entry point
│   ├── login.php       # Login page
│   ├── dashboard.php   # Unified dashboard for all departments
│   ├── approve.php     # Approval/rejection page
│   ├── reject.php      # Rejection page
│   ├── treasury-assign.php  # Treasury store assignment
│   ├── request-details.php  # Request details view
│   └── logout.php      # Logout endpoint
├── src/
│   ├── controllers/    # Controllers
│   │   ├── AuthController.php
│   │   ├── RequestController.php
│   │   └── ApprovalController.php
│   ├── models/         # Data models
│   │   ├── User.php
│   │   ├── Request.php
│   │   ├── Approval.php
│   │   ├── ItemRegistration.php
│   │   ├── ItemAssignment.php
│   │   ├── InventoryItem.php
│   │   ├── BackupRecord.php
│   │   └── AuditLog.php
│   ├── services/       # Business logic services
│   │   ├── WorkflowService.php
│   │   ├── LanguageService.php
│   │   └── NotificationService.php
│   └── utils/          # Utility classes
│       ├── Database.php
│       └── Session.php
├── views/              # View components
│   └── components/
│       ├── form20_modal.php
│       └── statistics.php
└── vendor/             # Composer autoloader
```

## Development Status

### ✅ Completed Components
- **Database**: Schema with UTF-8mb4 support, initialization script, inventory tables
- **Core Infrastructure**: Database connection, Session management, Configuration
- **Authentication**: User model, AuthController, login/logout, account lockout
- **Language Support**: Amharic translation system (150+ translations), LanguageService
- **UI**: Login page, unified dashboard, Form 20 modal, CSS with Amharic fonts
- **Request Management**: Request model, RequestController, Form 20 submission
- **Workflow Engine**: WorkflowService with 5-stage approval flow
- **Approval System**: Approval model, ApprovalController, approve/reject functionality
- **Item Registration**: ItemRegistration model, registration at Property Mgmt stage
- **Inventory Management**: InventoryItem model, ItemAssignment model, store assignment
- **Backup System**: BackupRecord model, automatic backup at Registry Office
- **Audit Logging**: AuditLog model, comprehensive action tracking
- **Treasury Operations**: Item retrieval, store assignment, item release
- **Notifications**: NotificationService, completion notifications
- **Security**: CSRF protection, SQL injection prevention, XSS prevention

### 🚧 Optional Enhancements
- API endpoints for programmatic access
- Property-based tests
- Unit tests
- Integration tests
- Separate dashboard views per department (currently using unified dashboard)

## Getting Started

### Quick Start with PHP Built-in Server

```bash
# 1. Initialize database
php database/init.php
php database/add_inventory.php

# 2. Start development server
php -S localhost:8000 -t public

# 3. Open browser to http://localhost:8000
```

### Login and Test

1. Login as requester (username: `requester1`, password: `password123`)
2. Click "አዲስ ጥያቄ ፍጠር" to create a new request
3. Fill Form 20 and submit
4. Logout and login as different departments to approve the request
5. Finally, login as Treasury to assign item from store and release

## Usage Guide

### For Requesters
1. Login to the system
2. Click "አዲስ ጥያቄ ፍጠር" (Create New Request)
3. Fill out Form 20 with item description, quantity, and reason
4. Submit the request
5. Track request status on your dashboard
6. Receive notification when request is completed

### For Approval Departments
1. Login to your department account
2. View pending requests on dashboard
3. Click "ይመልከቱ" (View) to see request details
4. Click "ፍቀድ" (Approve) or "ውድቅ አድርግ" (Reject)
5. For rejection, provide feedback in Amharic

### For Property Management Department
1. View pending requests
2. Click "እቃ መዝግብ" (Register Item)
3. Enter item description and requester ID number
4. Approve the request

### For Treasury Department
1. View requests pending treasury release
2. For new requests, click "እቃ ከስቶር መምረጥ" (Select Item from Store)
3. Choose item from inventory dropdown
4. Enter quantity and optional notes
5. Click "እቃ መድብ እና ውሰድ" (Assign and Retrieve Item)
6. Once assigned, click "እቃ አስረክብ" (Release Item)
7. System automatically generates release permission document
8. Requester receives notification with permission number

### For Requesters - Collecting Items
1. After request is completed, view dashboard
2. Click "📄 የመልቀቅ ፍቃድ" (Release Permission) button
3. View and print the release permission document
4. Take printed document to Treasury
5. Both parties sign the document
6. Collect the item from Treasury

## System Features in Detail

### Multi-Stage Approval Workflow
- Requests flow through 5 departments sequentially
- Each department can approve or reject with feedback
- Rejection feedback is preserved and visible to requester
- Audit trail tracks all actions with timestamps

### Item Registration
- Property Management Department registers items with requester ID
- Registration data is stored and visible to downstream departments
- Required before request can proceed to Registry Office

### Backup System
- Registry Office automatically creates backup records
- Backup includes complete request and registration data
- Stored as JSON for easy retrieval

### Inventory Management
- Treasury maintains store inventory with stock levels
- Items have codes, categories, and storage locations
- Stock automatically reduced when items assigned
- One item assignment per request (enforced)

### Audit Logging
- All actions logged with user ID, timestamp, and details
- Complete audit trail available for each request
- Supports compliance and accountability

### Amharic Language Support
- Full interface in Amharic language
- 150+ translations covering all UI elements
- Proper font support (Noto Sans Ethiopic, Abyssinica SIL)
- UTF-8mb4 database encoding for Amharic text

### Security Features
- CSRF token protection on all forms
- SQL injection prevention (parameterized queries)
- XSS prevention (output escaping)
- Session timeout (30 minutes)
- Account lockout after 5 failed login attempts
- Password hashing with bcrypt

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database exists: `php database/init.php`

### Amharic Text Not Displaying
- Ensure UTF-8mb4 encoding in database
- Check browser supports Ethiopic fonts
- Verify `lang/am.php` is loaded correctly

### Session Issues
- Check PHP session configuration
- Ensure `session.gc_maxlifetime` is set appropriately
- Clear browser cookies if needed

## Next Steps

The system is fully functional for production use. Optional enhancements:

1. **API Endpoints**: Add REST API for mobile/external access
2. **Testing**: Implement property-based and unit tests
3. **Reporting**: Add reports and analytics dashboard
4. **Email Notifications**: Send email alerts for request updates
5. **File Attachments**: Allow attaching documents to requests
6. **Advanced Search**: Add filtering and search capabilities

## License

Proprietary - All rights reserved

## Support

For support, please contact the system administrator.
