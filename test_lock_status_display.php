<?php
/**
 * Test Lock Status Display
 * Verifies that lock status is correctly displayed in user management page
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

$db = Database::getConnection();

echo "Testing Lock Status Display Implementation\n";
echo "==========================================\n\n";

// Test 1: Verify account_locked_until field exists in users table
echo "Test 1: Checking if account_locked_until field exists...\n";
try {
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'account_locked_until'")->fetch();
    if ($result) {
        echo "✓ PASS: account_locked_until field exists\n";
    } else {
        echo "✗ FAIL: account_locked_until field does not exist\n";
    }
} catch (Exception $e) {
    echo "✗ FAIL: Error checking field: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Verify failed_login_attempts field exists
echo "Test 2: Checking if failed_login_attempts field exists...\n";
try {
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'failed_login_attempts'")->fetch();
    if ($result) {
        echo "✓ PASS: failed_login_attempts field exists\n";
    } else {
        echo "✗ FAIL: failed_login_attempts field does not exist\n";
    }
} catch (Exception $e) {
    echo "✗ FAIL: Error checking field: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check if query includes lock status fields
echo "Test 3: Testing user query with lock status fields...\n";
try {
    $users = $db->query("
        SELECT 
            u.*,
            COUNT(DISTINCT r.request_id) as total_requests,
            COUNT(DISTINCT CASE WHEN r.status = 'completed' THEN r.request_id END) as completed_requests,
            COUNT(DISTINCT al.log_id) as total_activities,
            MAX(al.created_at) as last_activity
        FROM users u
        LEFT JOIN requests r ON u.user_id = r.requester_id
        LEFT JOIN audit_logs al ON u.user_id = al.user_id
        GROUP BY u.user_id
        LIMIT 1
    ")->fetch();
    
    if ($users && isset($users['account_locked_until']) && isset($users['failed_login_attempts'])) {
        echo "✓ PASS: Query successfully retrieves lock status fields\n";
        echo "  - account_locked_until: " . ($users['account_locked_until'] ?? 'NULL') . "\n";
        echo "  - failed_login_attempts: " . $users['failed_login_attempts'] . "\n";
    } else {
        echo "✗ FAIL: Query does not include lock status fields\n";
    }
} catch (Exception $e) {
    echo "✗ FAIL: Error executing query: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Verify unlock-account.php API endpoint exists
echo "Test 4: Checking if unlock-account.php API endpoint exists...\n";
if (file_exists(__DIR__ . '/public/api/users/unlock-account.php')) {
    echo "✓ PASS: unlock-account.php API endpoint exists\n";
} else {
    echo "✗ FAIL: unlock-account.php API endpoint does not exist\n";
}
echo "\n";

// Test 5: Verify user-management.js has unlockAccount function
echo "Test 5: Checking if unlockAccount function exists in user-management.js...\n";
$jsContent = file_get_contents(__DIR__ . '/public/js/user-management.js');
if (strpos($jsContent, 'function unlockAccount') !== false) {
    echo "✓ PASS: unlockAccount function exists in user-management.js\n";
} else {
    echo "✗ FAIL: unlockAccount function not found in user-management.js\n";
}
echo "\n";

// Test 6: Verify user-management.php has lock status display code
echo "Test 6: Checking if user-management.php has lock status display...\n";
$phpContent = file_get_contents(__DIR__ . '/public/user-management.php');
$hasLockIndicator = strpos($phpContent, 'lock-indicator') !== false;
$hasLockExpiration = strpos($phpContent, 'lock-expiration') !== false;
$hasUnlockButton = strpos($phpContent, 'unlockAccount') !== false;
$hasLockedClass = strpos($phpContent, "user-card.locked") !== false;

if ($hasLockIndicator && $hasLockExpiration && $hasUnlockButton && $hasLockedClass) {
    echo "✓ PASS: user-management.php has all lock status display elements\n";
    echo "  - Lock indicator: ✓\n";
    echo "  - Lock expiration display: ✓\n";
    echo "  - Unlock button: ✓\n";
    echo "  - Locked card styling: ✓\n";
} else {
    echo "✗ FAIL: user-management.php is missing some lock status elements\n";
    echo "  - Lock indicator: " . ($hasLockIndicator ? '✓' : '✗') . "\n";
    echo "  - Lock expiration display: " . ($hasLockExpiration ? '✓' : '✗') . "\n";
    echo "  - Unlock button: " . ($hasUnlockButton ? '✓' : '✗') . "\n";
    echo "  - Locked card styling: " . ($hasLockedClass ? '✓' : '✗') . "\n";
}
echo "\n";

echo "==========================================\n";
echo "Test Summary Complete\n";
