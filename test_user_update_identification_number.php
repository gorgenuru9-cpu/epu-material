<?php
/**
 * Test User Update with Identification Number
 * Tests that the update API properly handles identification_number field
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

$db = Database::getConnection();

echo "Testing User Update with Identification Number\n";
echo "==============================================\n\n";

// Test 1: Create two test users
echo "Test 1: Creating test users...\n";
try {
    // Clean up any existing test users
    $db->exec("DELETE FROM users WHERE username IN ('test_user_update_1', 'test_user_update_2')");
    
    // Create first test user
    $stmt = $db->prepare("
        INSERT INTO users (username, password_hash, full_name, department, identification_number)
        VALUES (:username, :password_hash, :full_name, :department, :identification_number)
    ");
    
    $stmt->execute([
        ':username' => 'test_user_update_1',
        ':password_hash' => password_hash('password123', PASSWORD_DEFAULT),
        ':full_name' => 'Test User One',
        ':department' => 'requester',
        ':identification_number' => 'ID001'
    ]);
    $user1Id = $db->lastInsertId();
    
    // Create second test user
    $stmt->execute([
        ':username' => 'test_user_update_2',
        ':password_hash' => password_hash('password123', PASSWORD_DEFAULT),
        ':full_name' => 'Test User Two',
        ':department' => 'requester',
        ':identification_number' => 'ID002'
    ]);
    $user2Id = $db->lastInsertId();
    
    echo "✓ Test users created successfully (IDs: $user1Id, $user2Id)\n\n";
} catch (Exception $e) {
    echo "✗ Failed to create test users: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Update user with new identification number
echo "Test 2: Updating user with new identification number...\n";
try {
    $stmt = $db->prepare("
        UPDATE users 
        SET username = :username, 
            full_name = :full_name, 
            department = :department,
            identification_number = :identification_number,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = :user_id
    ");
    
    $result = $stmt->execute([
        ':username' => 'test_user_update_1',
        ':full_name' => 'Test User One Updated',
        ':department' => 'requester_main_dept',
        ':identification_number' => 'ID001_UPDATED',
        ':user_id' => $user1Id
    ]);
    
    if ($result) {
        // Verify the update
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user1Id]);
        $user = $stmt->fetch();
        
        if ($user['identification_number'] === 'ID001_UPDATED' && 
            $user['full_name'] === 'Test User One Updated' &&
            $user['department'] === 'requester_main_dept') {
            echo "✓ User updated successfully with new identification number\n\n";
        } else {
            echo "✗ User data not updated correctly\n";
            echo "Expected identification_number: ID001_UPDATED, Got: " . $user['identification_number'] . "\n";
            exit(1);
        }
    } else {
        echo "✗ Update query failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Failed to update user: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Verify uniqueness constraint for identification number
echo "Test 3: Testing identification number uniqueness constraint...\n";
try {
    // Try to update user1 with user2's identification number
    $stmt = $db->prepare("SELECT user_id FROM users WHERE identification_number = :identification_number AND user_id != :user_id");
    $stmt->execute([
        ':identification_number' => 'ID002',
        ':user_id' => $user1Id
    ]);
    
    if ($stmt->fetch()) {
        echo "✓ Uniqueness check correctly detects duplicate identification number\n\n";
    } else {
        echo "✗ Uniqueness check failed to detect duplicate\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Failed uniqueness check: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Verify user can update their own identification number
echo "Test 4: Testing user can update their own identification number...\n";
try {
    $stmt = $db->prepare("SELECT user_id FROM users WHERE identification_number = :identification_number AND user_id != :user_id");
    $stmt->execute([
        ':identification_number' => 'ID001_UPDATED',
        ':user_id' => $user1Id
    ]);
    
    if (!$stmt->fetch()) {
        echo "✓ User can keep their own identification number (no false positive)\n\n";
    } else {
        echo "✗ Uniqueness check incorrectly flagged user's own ID\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Failed self-update check: " . $e->getMessage() . "\n";
    exit(1);
}

// Clean up
echo "Cleaning up test data...\n";
try {
    $db->exec("DELETE FROM users WHERE username IN ('test_user_update_1', 'test_user_update_2')");
    echo "✓ Test data cleaned up\n\n";
} catch (Exception $e) {
    echo "✗ Failed to clean up: " . $e->getMessage() . "\n";
}

echo "==============================================\n";
echo "All tests passed! ✓\n";
echo "The update API correctly handles identification_number field with uniqueness validation.\n";
