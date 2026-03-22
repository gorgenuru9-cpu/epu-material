<?php
/**
 * Test script for User account lock methods
 * This script verifies the new account lock status methods work correctly
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/utils/Database.php';
require_once __DIR__ . '/src/models/User.php';

use PropertyRequestSystem\Models\User;
use PropertyRequestSystem\Utils\Database;

echo "Testing User Account Lock Methods\n";
echo "==================================\n\n";

// Test 1: Check unlocked account
echo "Test 1: Check unlocked account\n";
$user = User::findById(1); // Assuming user ID 1 exists
if ($user) {
    $isLocked = $user->isAccountLocked();
    $expiration = $user->getAccountLockExpiration();
    echo "  User: " . $user->getUsername() . "\n";
    echo "  Is Locked: " . ($isLocked ? "Yes" : "No") . "\n";
    echo "  Lock Expiration: " . ($expiration ? date('Y-m-d H:i:s', $expiration) : "None") . "\n";
    echo "  ✓ Test passed\n\n";
} else {
    echo "  ✗ User not found\n\n";
}

// Test 2: Manually lock an account and check status
echo "Test 2: Manually lock account and check status\n";
$db = Database::getConnection();
$lockUntil = date('Y-m-d H:i:s', time() + 900); // Lock for 15 minutes
$stmt = $db->prepare("
    UPDATE users 
    SET failed_login_attempts = 5, 
        account_locked_until = :lock_until 
    WHERE user_id = 1
");
$stmt->execute([':lock_until' => $lockUntil]);

$user = User::findById(1);
if ($user) {
    $isLocked = $user->isAccountLocked();
    $expiration = $user->getAccountLockExpiration();
    echo "  User: " . $user->getUsername() . "\n";
    echo "  Is Locked: " . ($isLocked ? "Yes" : "No") . "\n";
    echo "  Lock Expiration: " . ($expiration ? date('Y-m-d H:i:s', $expiration) : "None") . "\n";
    
    if ($isLocked && $expiration) {
        echo "  ✓ Test passed - Account is locked\n\n";
    } else {
        echo "  ✗ Test failed - Account should be locked\n\n";
    }
} else {
    echo "  ✗ User not found\n\n";
}

// Test 3: Unlock account using static method
echo "Test 3: Unlock account using static method\n";
User::unlockAccount(1);

$user = User::findById(1);
if ($user) {
    $isLocked = $user->isAccountLocked();
    $expiration = $user->getAccountLockExpiration();
    echo "  User: " . $user->getUsername() . "\n";
    echo "  Is Locked: " . ($isLocked ? "Yes" : "No") . "\n";
    echo "  Lock Expiration: " . ($expiration ? date('Y-m-d H:i:s', $expiration) : "None") . "\n";
    
    if (!$isLocked && !$expiration) {
        echo "  ✓ Test passed - Account is unlocked\n\n";
    } else {
        echo "  ✗ Test failed - Account should be unlocked\n\n";
    }
} else {
    echo "  ✗ User not found\n\n";
}

// Test 4: Check expired lock (simulate by setting lock in the past)
echo "Test 4: Check expired lock\n";
$lockUntil = date('Y-m-d H:i:s', time() - 100); // Lock expired 100 seconds ago
$stmt = $db->prepare("
    UPDATE users 
    SET failed_login_attempts = 5, 
        account_locked_until = :lock_until 
    WHERE user_id = 1
");
$stmt->execute([':lock_until' => $lockUntil]);

$user = User::findById(1);
if ($user) {
    $isLocked = $user->isAccountLocked();
    $expiration = $user->getAccountLockExpiration();
    echo "  User: " . $user->getUsername() . "\n";
    echo "  Is Locked: " . ($isLocked ? "Yes" : "No") . "\n";
    echo "  Lock Expiration: " . ($expiration ? date('Y-m-d H:i:s', $expiration) : "None") . "\n";
    
    if (!$isLocked && !$expiration) {
        echo "  ✓ Test passed - Expired lock is treated as unlocked\n\n";
    } else {
        echo "  ✗ Test failed - Expired lock should be treated as unlocked\n\n";
    }
} else {
    echo "  ✗ User not found\n\n";
}

// Clean up - ensure user 1 is unlocked
User::unlockAccount(1);

echo "All tests completed!\n";
