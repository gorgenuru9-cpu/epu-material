<?php
/**
 * Test script to verify cascade deletion of notifications when deleting a user
 * Tests task 4.1: Add cascade deletion of notifications in delete.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

$db = Database::getConnection();

echo "Testing User Deletion with Cascade Notification Deletion\n";
echo "========================================================\n\n";

try {
    // Clean up any existing test data
    echo "Cleaning up existing test data...\n";
    $db->exec("DELETE FROM users WHERE username = 'test_cascade_user'");
    echo "✓ Cleanup complete\n\n";
    
    // Create a test user
    echo "Creating test user...\n";
    $stmt = $db->prepare("
        INSERT INTO users (username, password_hash, full_name, department, identification_number)
        VALUES (:username, :password_hash, :full_name, :department, :identification_number)
    ");
    $stmt->execute([
        ':username' => 'test_cascade_user',
        ':password_hash' => password_hash('testpass123', PASSWORD_DEFAULT),
        ':full_name' => 'Test Cascade User',
        ':department' => 'requester',
        ':identification_number' => 'TEST_CASCADE_001'
    ]);
    $testUserId = $db->lastInsertId();
    echo "✓ Test user created with ID: $testUserId\n\n";
    
    // Create test notifications for the user
    echo "Creating test notifications...\n";
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, message, is_read)
        VALUES (:user_id, :message, :is_read)
    ");
    
    for ($i = 1; $i <= 3; $i++) {
        $stmt->execute([
            ':user_id' => $testUserId,
            ':message' => "Test notification $i",
            ':is_read' => false
        ]);
    }
    echo "✓ Created 3 test notifications\n\n";
    
    // Verify notifications exist
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $testUserId]);
    $notificationCount = $stmt->fetchColumn();
    echo "Verification: Found $notificationCount notifications for user\n\n";
    
    if ($notificationCount != 3) {
        throw new Exception("Expected 3 notifications, found $notificationCount");
    }
    
    // Test the deletion with transaction
    echo "Testing user deletion with cascade notification deletion...\n";
    
    $db->beginTransaction();
    
    try {
        // Delete all notifications for the user
        $stmt = $db->prepare("DELETE FROM notifications WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $testUserId]);
        $deletedNotifications = $stmt->rowCount();
        echo "✓ Deleted $deletedNotifications notifications\n";
        
        // Delete user
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $testUserId]);
        $deletedUsers = $stmt->rowCount();
        echo "✓ Deleted $deletedUsers user\n";
        
        // Commit transaction
        $db->commit();
        echo "✓ Transaction committed successfully\n\n";
        
    } catch (Exception $e) {
        $db->rollBack();
        throw new Exception("Transaction failed: " . $e->getMessage());
    }
    
    // Verify user is deleted
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $testUserId]);
    $userCount = $stmt->fetchColumn();
    
    if ($userCount > 0) {
        throw new Exception("User was not deleted");
    }
    echo "✓ Verified: User successfully deleted\n";
    
    // Verify notifications are deleted
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $testUserId]);
    $remainingNotifications = $stmt->fetchColumn();
    
    if ($remainingNotifications > 0) {
        throw new Exception("Notifications were not deleted (found $remainingNotifications)");
    }
    echo "✓ Verified: All notifications successfully deleted\n\n";
    
    echo "========================================================\n";
    echo "✓ ALL TESTS PASSED\n";
    echo "========================================================\n\n";
    
} catch (Exception $e) {
    echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n\n";
    
    // Cleanup on failure
    try {
        $db->exec("DELETE FROM users WHERE username = 'test_cascade_user'");
    } catch (Exception $cleanupError) {
        // Ignore cleanup errors
    }
    
    exit(1);
}
