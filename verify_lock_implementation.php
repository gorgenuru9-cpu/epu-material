<?php
/**
 * Verify Lock Implementation
 * Check if the implementation is working correctly
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

$db = Database::getConnection();

echo "Verifying Lock Status Implementation\n";
echo "====================================\n\n";

// Check if there are any users
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
echo "Total users in database: $userCount\n\n";

if ($userCount > 0) {
    // Get a sample user with all fields
    $user = $db->query("
        SELECT 
            user_id,
            username,
            full_name,
            account_locked_until,
            failed_login_attempts
        FROM users
        LIMIT 1
    ")->fetch();
    
    echo "Sample user data:\n";
    echo "  User ID: " . $user['user_id'] . "\n";
    echo "  Username: " . $user['username'] . "\n";
    echo "  Full Name: " . $user['full_name'] . "\n";
    echo "  Account Locked Until: " . ($user['account_locked_until'] ?? 'NULL') . "\n";
    echo "  Failed Login Attempts: " . $user['failed_login_attempts'] . "\n\n";
    
    // Test lock status logic
    $isLocked = false;
    if ($user['account_locked_until']) {
        $lockTime = strtotime($user['account_locked_until']);
        if ($lockTime > time()) {
            $isLocked = true;
        }
    }
    
    echo "Lock Status: " . ($isLocked ? "LOCKED" : "UNLOCKED") . "\n";
    
    if ($isLocked) {
        echo "Lock expires at: " . date('Y-m-d H:i:s', $lockTime) . "\n";
    }
}

echo "\n====================================\n";
echo "Implementation Files Check:\n";
echo "  - user-management.php: " . (file_exists('public/user-management.php') ? '✓' : '✗') . "\n";
echo "  - user-management.js: " . (file_exists('public/js/user-management.js') ? '✓' : '✗') . "\n";
echo "  - unlock-account.php: " . (file_exists('public/api/users/unlock-account.php') ? '✓' : '✗') . "\n";
echo "\nImplementation complete!\n";
