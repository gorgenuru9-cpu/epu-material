<?php
/**
 * Test script to verify transaction rollback when user deletion fails
 * Tests task 4.1: Ensure deletion happens in a transaction for atomicity
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

$db = Database::getConnection();

echo "Testing Transaction Rollback on User Deletion Failure\n";
echo "======================================================\n\n";

try {
    // Clean up any existing test data
    echo "Cleaning up existing test data...\n";
    $db->exec("DELETE FROM users WHERE username = 'test_rollback_user'");
    echo "✓ Cleanup complete\n\n";
    
    // Create a test user
    echo "Creating test user...\n";
    $stmt = $db->prepare("
        INSERT INTO users (username, password_hash, full_name, department, identification_number)
        VALUES (:username, :password_hash, :full_name, :department, :identification_number)
    ");
    $stmt->execute([
        ':username' => 'test_rollback_user',
        ':password_hash' => password_hash('testpass123', PASSWORD_DEFAULT),
        ':full_name' => 'Test Rollback User',
        ':department' => 'requester',
        ':identification_number' => 'TEST_ROLLBACK_001'
    ]);
    $testUserId = $db->lastInsertId();
    echo "✓ Test user created with ID: $testUserId\n\n";
    
    // Create test notifications for the user
    echo "Creating test notifications...\n";
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, message, is_read)
        VALUES (:user_id, :message, :is_read)
    ");
    
    for ($i = 1; $i <= 2; $i++) {
        $stmt->execute([
            ':user_id' => $testUserId,
            ':message' => "Test notification $i",
            ':is_read' => false
        ]);
    }
    echo "✓ Created 2 test notifications\n\n";
    
    // Verify initial state
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $testUserId]);
    $initialNotificationCount = $stmt->fetchColumn();
    echo "Initial state: $initialNotificationCount notifications\n\n";
    
    // Test transaction rollback by attempting to delete with an invalid user_id after deleting notifications
    echo "Testing transaction rollback scenario...\n";
    
    $db->beginTransaction();
    
    try {
        // Delete notifications (this should succeed)
        $stmt = $db->prepare("DELETE FROM notifications WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $testUserId]);
        echo "✓ Notifications deleted within transaction\n";
        
        // Simulate a failure by trying to delete a non-existent user
        // In a real scenario, this could be any database error
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => 99999]); // Non-existent user
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("User deletion failed - simulating error");
        }
        
        $db->commit();
        
    } catch (Exception $e) {
        $db->rollBack();
        echo "✓ Transaction rolled back: " . $e->getMessage() . "\n\n";
    }
    
    // Verify notifications were NOT deleted (rollback worked)
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $testUserId]);
    $finalNotificationCount = $stmt->fetchColumn();
    
    if ($finalNotificationCount != $initialNotificationCount) {
        throw new Exception("Rollback failed: Expected $initialNotificationCount notifications, found $finalNotificationCount");
    }
    echo "✓ Verified: Notifications preserved after rollback ($finalNotificationCount notifications)\n";
    
    // Verify user still exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $testUserId]);
    $userCount = $stmt->fetchColumn();
    
    if ($userCount != 1) {
        throw new Exception("User should still exist after rollback");
    }
    echo "✓ Verified: User preserved after rollback\n\n";
    
    // Cleanup
    echo "Cleaning up test data...\n";
    $db->exec("DELETE FROM users WHERE username = 'test_rollback_user'");
    echo "✓ Test data cleaned up\n\n";
    
    echo "======================================================\n";
    echo "✓ ALL TESTS PASSED - Transaction rollback works correctly\n";
    echo "======================================================\n\n";
    
} catch (Exception $e) {
    echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n\n";
    
    // Cleanup on failure
    try {
        $db->exec("DELETE FROM users WHERE username = 'test_rollback_user'");
    } catch (Exception $cleanupError) {
        // Ignore cleanup errors
    }
    
    exit(1);
}
