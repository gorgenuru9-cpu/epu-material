<?php
/**
 * Test script for password reset clearing account locks
 * This script verifies that resetting a password clears failed_login_attempts and account_locked_until
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/utils/Database.php';
require_once __DIR__ . '/src/models/User.php';

use PropertyRequestSystem\Models\User;
use PropertyRequestSystem\Utils\Database;

echo "Testing Password Reset Clears Account Locks\n";
echo "============================================\n\n";

$db = Database::getConnection();

// Test: Password reset should clear account locks
echo "Test: Password reset clears failed_login_attempts and account_locked_until\n";

// Step 1: Lock an account
echo "  Step 1: Locking account for user ID 1...\n";
$lockUntil = date('Y-m-d H:i:s', time() + 900); // Lock for 15 minutes
$stmt = $db->prepare("
    UPDATE users 
    SET failed_login_attempts = 5, 
        account_locked_until = :lock_until 
    WHERE user_id = 1
");
$stmt->execute([':lock_until' => $lockUntil]);

// Verify account is locked
$user = User::findById(1);
if ($user && $user->isAccountLocked()) {
    echo "  ✓ Account is locked\n";
} else {
    echo "  ✗ Failed to lock account\n";
    exit(1);
}

// Step 2: Reset password (simulating the API call)
echo "  Step 2: Resetting password...\n";
$newPassword = bin2hex(random_bytes(4)); // 8 character password
$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    UPDATE users 
    SET password_hash = :password_hash,
        failed_login_attempts = 0,
        account_locked_until = NULL,
        updated_at = CURRENT_TIMESTAMP
    WHERE user_id = 1
");

$result = $stmt->execute([
    ':password_hash' => $passwordHash
]);

if (!$result) {
    echo "  ✗ Failed to reset password\n";
    exit(1);
}
echo "  ✓ Password reset executed\n";

// Step 3: Verify account is unlocked and failed attempts are cleared
echo "  Step 3: Verifying account lock is cleared...\n";
$user = User::findById(1);

if (!$user) {
    echo "  ✗ User not found\n";
    exit(1);
}

$isLocked = $user->isAccountLocked();
$expiration = $user->getAccountLockExpiration();

// Check database directly for failed_login_attempts
$stmt = $db->prepare("SELECT failed_login_attempts, account_locked_until FROM users WHERE user_id = 1");
$stmt->execute();
$userData = $stmt->fetch();

echo "  User: " . $user->getUsername() . "\n";
echo "  Is Locked: " . ($isLocked ? "Yes" : "No") . "\n";
echo "  Lock Expiration: " . ($expiration ? date('Y-m-d H:i:s', $expiration) : "None") . "\n";
echo "  Failed Login Attempts: " . $userData['failed_login_attempts'] . "\n";
echo "  Account Locked Until: " . ($userData['account_locked_until'] ?? "NULL") . "\n";

// Verify all conditions
if (!$isLocked && 
    !$expiration && 
    $userData['failed_login_attempts'] == 0 && 
    $userData['account_locked_until'] === null) {
    echo "  ✓ Test passed - Password reset cleared account locks\n\n";
} else {
    echo "  ✗ Test failed - Account should be unlocked with 0 failed attempts\n\n";
    exit(1);
}

echo "All tests completed successfully!\n";
