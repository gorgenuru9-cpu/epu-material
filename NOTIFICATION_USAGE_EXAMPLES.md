# Notification System Usage Examples

## Overview
The notification system allows you to send notifications to users when important events occur in the system.

## Available Methods

### 1. Notify Single User
```php
use PropertyRequestSystem\Services\NotificationService;

// Notify a specific user
NotificationService::notify(
    $userId,           // User ID to notify
    $requestId,        // Related request ID (or null)
    'Your message'     // Notification message
);
```

### 2. Notify All Users Except One
```php
// Notify all users except the current user
$currentUserId = Session::get('user_id');
$notifiedCount = NotificationService::notifyAllExcept(
    $currentUserId,                    // User ID to exclude
    $requestId,                        // Related request ID (or null)
    'New request has been created'     // Message
);

// Notify all users in a specific department except one
$notifiedCount = NotificationService::notifyAllExcept(
    $currentUserId,                    // User ID to exclude
    $requestId,                        // Related request ID
    'Request approved',                // Message
    DEPT_TREASURY                      // Only notify treasury users
);
```

### 3. Notify Department
```php
// Notify all users in a department
$notifiedCount = NotificationService::notifyDepartment(
    DEPT_TREASURY,                     // Department
    $requestId,                        // Related request ID
    'New item ready for release'       // Message
);

// Notify department except one user
$notifiedCount = NotificationService::notifyDepartment(
    DEPT_PROPERTY_MGMT_DEPT,          // Department
    $requestId,                        // Related request ID
    'Item registered',                 // Message
    $currentUserId                     // User to exclude
);
```

### 4. Get Notifications
```php
// Get unread notifications
$unreadNotifications = NotificationService::getUnread($userId);

// Get all notifications (with limit)
$allNotifications = NotificationService::getAll($userId, 50);

// Get unread count
$unreadCount = NotificationService::getUnreadCount($userId);
```

### 5. Mark as Read
```php
// Mark single notification as read
NotificationService::markAsRead($notificationId);

// Mark all notifications as read for user
NotificationService::markAllAsRead($userId);
```

## Real-World Examples

### Example 1: New Request Created
```php
// In RequestController->create()
$requestId = $request->getId();
$currentUserId = Session::get('user_id');

// Notify all users except the requester
NotificationService::notifyAllExcept(
    $currentUserId,
    $requestId,
    'ጥያቄ ቁጥር ' . $requestId . ' አዲስ ጥያቄ ተፈጥሯል'
);
```

### Example 2: Request Approved
```php
// In ApprovalController->approve()
$request = Request::findById($requestId);
$requester = $request->getRequesterId();

// Notify the requester
NotificationService::notify(
    $requester,
    $requestId,
    'ጥያቄዎ በ' . $department . ' ፀድቋል'
);

// Notify next department
$nextDept = WorkflowService::getNextDepartment($request->getStatus());
NotificationService::notifyDepartment(
    $nextDept,
    $requestId,
    'አዲስ ጥያቄ ለፀደቃ ይጠብቃል'
);
```

### Example 3: Item Released
```php
// In TreasuryController->release()
$currentUserId = Session::get('user_id');

// Notify requester
NotificationService::notify(
    $request->getRequesterId(),
    $requestId,
    'እቃዎ ተለቋል። የመልቀቅ ፍቃድ ቁጥር: ' . $permissionNumber
);

// Notify all other users
NotificationService::notifyAllExcept(
    $currentUserId,
    $requestId,
    'ጥያቄ ቁጥር ' . $requestId . ' ተጠናቋል',
    null  // All departments
);
```

### Example 4: Request Rejected
```php
// In ApprovalController->reject()
$currentUserId = Session::get('user_id');

// Notify requester
NotificationService::notify(
    $request->getRequesterId(),
    $requestId,
    'ጥያቄዎ በ' . $department . ' ውድቅ ሆኗል። ምክንያት: ' . $feedback
);

// Notify all users except current user
NotificationService::notifyAllExcept(
    $currentUserId,
    $requestId,
    'ጥያቄ ቁጥር ' . $requestId . ' ውድቅ ሆኗል'
);
```

### Example 5: Item Registered
```php
// In PropertyMgmtController->registerItem()
$currentUserId = Session::get('user_id');

// Notify requester
NotificationService::notify(
    $request->getRequesterId(),
    $requestId,
    'እቃዎ በንብረት አስተዳደር ተመዝግቧል'
);

// Notify registry office
NotificationService::notifyDepartment(
    DEPT_REGISTRY_OFFICE,
    $requestId,
    'አዲስ እቃ ለመዝገብ ይጠብቃል',
    $currentUserId
);
```

## UI Integration

### Sidebar Badge
The sidebar automatically shows unread notification count:
```php
// In sidebar.php
$unreadCount = NotificationService::getUnreadCount($userId);
```

### Notifications Page
Users can view all notifications at `/notifications.php`:
- View all notifications (read and unread)
- Mark individual notifications as read
- Mark all notifications as read
- Click to view related request details

## Best Practices

1. **Always exclude the current user** when notifying "all users" to avoid self-notification
2. **Include request ID** when notification is related to a specific request
3. **Use Amharic messages** for consistency with the system
4. **Be specific** in notification messages (include request ID, item name, etc.)
5. **Notify relevant departments** instead of all users when possible
6. **Clean up old notifications** periodically (consider adding a cleanup job)

## Department Constants
```php
DEPT_REQUESTER                  // ጠያቂ
DEPT_REQUESTER_MAIN_DEPT        // ጠያቂው ዋና ክፍል
DEPT_PROPERTY_MGMT_MAIN_DEPT    // የንብረት አስተዳደር ዋና ክፍል
DEPT_PROPERTY_MGMT_DEPT         // የንብረት አስተዳደር ክፍል
DEPT_REGISTRY_OFFICE            // መዝገብ ቤት
DEPT_TREASURY                   // ግምጃ ቤት
```
