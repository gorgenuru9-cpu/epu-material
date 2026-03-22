<?php
/**
 * Test script for UserStatistics service
 * This script verifies the UserStatistics methods work correctly
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/utils/Database.php';
require_once __DIR__ . '/src/services/UserStatistics.php';

use PropertyRequestSystem\Services\UserStatistics;
use PropertyRequestSystem\Utils\Database;

echo "Testing UserStatistics Service\n";
echo "==============================\n\n";

// Test 1: Get total users
echo "Test 1: Get total users\n";
$totalUsers = UserStatistics::getTotalUsers();
echo "  Total Users: " . $totalUsers . "\n";
if ($totalUsers >= 0) {
    echo "  ✓ Test passed\n\n";
} else {
    echo "  ✗ Test failed - Invalid count\n\n";
}

// Test 2: Get active users (last 7 days)
echo "Test 2: Get active users (last 7 days)\n";
$activeUsers = UserStatistics::getActiveUsers(7);
echo "  Active Users (7 days): " . $activeUsers . "\n";
if ($activeUsers >= 0 && $activeUsers <= $totalUsers) {
    echo "  ✓ Test passed\n\n";
} else {
    echo "  ✗ Test failed - Invalid count\n\n";
}

// Test 3: Get active users with custom days parameter
echo "Test 3: Get active users (last 30 days)\n";
$activeUsers30 = UserStatistics::getActiveUsers(30);
echo "  Active Users (30 days): " . $activeUsers30 . "\n";
if ($activeUsers30 >= 0 && $activeUsers30 <= $totalUsers && $activeUsers30 >= $activeUsers) {
    echo "  ✓ Test passed\n\n";
} else {
    echo "  ✗ Test failed - Invalid count or logic error\n\n";
}

// Test 4: Get new users (last 30 days)
echo "Test 4: Get new users (last 30 days)\n";
$newUsers = UserStatistics::getNewUsers(30);
echo "  New Users (30 days): " . $newUsers . "\n";
if ($newUsers >= 0 && $newUsers <= $totalUsers) {
    echo "  ✓ Test passed\n\n";
} else {
    echo "  ✗ Test failed - Invalid count\n\n";
}

// Test 5: Get new users with custom days parameter
echo "Test 5: Get new users (last 7 days)\n";
$newUsers7 = UserStatistics::getNewUsers(7);
echo "  New Users (7 days): " . $newUsers7 . "\n";
if ($newUsers7 >= 0 && $newUsers7 <= $newUsers) {
    echo "  ✓ Test passed\n\n";
} else {
    echo "  ✗ Test failed - Invalid count or logic error\n\n";
}

// Test 6: Get user request stats for a specific user
echo "Test 6: Get user request stats\n";
$db = Database::getConnection();
$stmt = $db->query("SELECT user_id FROM users LIMIT 1");
$userId = $stmt->fetchColumn();

if ($userId) {
    $requestStats = UserStatistics::getUserRequestStats($userId);
    echo "  User ID: " . $userId . "\n";
    echo "  Total Requests: " . $requestStats['total_requests'] . "\n";
    echo "  Completed Requests: " . $requestStats['completed_requests'] . "\n";
    
    if (isset($requestStats['total_requests']) && 
        isset($requestStats['completed_requests']) &&
        $requestStats['completed_requests'] <= $requestStats['total_requests']) {
        echo "  ✓ Test passed\n\n";
    } else {
        echo "  ✗ Test failed - Invalid stats structure or logic\n\n";
    }
} else {
    echo "  ⚠ Skipped - No users in database\n\n";
}

// Test 7: Get user activity stats for a specific user
echo "Test 7: Get user activity stats\n";
if ($userId) {
    $activityStats = UserStatistics::getUserActivityStats($userId);
    echo "  User ID: " . $userId . "\n";
    echo "  Total Activities: " . $activityStats['total_activities'] . "\n";
    echo "  Last Activity: " . ($activityStats['last_activity'] ?? 'None') . "\n";
    
    if (isset($activityStats['total_activities']) && 
        isset($activityStats['last_activity'])) {
        echo "  ✓ Test passed\n\n";
    } else {
        echo "  ✗ Test failed - Invalid stats structure\n\n";
    }
} else {
    echo "  ⚠ Skipped - No users in database\n\n";
}

// Test 8: Get stats for user with no requests
echo "Test 8: Get stats for user with no requests\n";
// Create a temporary test user
$stmt = $db->prepare("
    INSERT INTO users (username, password_hash, full_name, department, identification_number)
    VALUES (:username, :password, :full_name, :department, :id_number)
");
$testUsername = 'test_stats_user_' . time();
$stmt->execute([
    ':username' => $testUsername,
    ':password' => password_hash('test123', PASSWORD_DEFAULT),
    ':full_name' => 'Test Statistics User',
    ':department' => DEPT_REQUESTER,
    ':id_number' => 'TEST_STATS_' . time()
]);
$testUserId = $db->lastInsertId();

$requestStats = UserStatistics::getUserRequestStats($testUserId);
$activityStats = UserStatistics::getUserActivityStats($testUserId);

echo "  Test User ID: " . $testUserId . "\n";
echo "  Total Requests: " . $requestStats['total_requests'] . "\n";
echo "  Total Activities: " . $activityStats['total_activities'] . "\n";

if ($requestStats['total_requests'] === 0 && 
    $requestStats['completed_requests'] === 0 &&
    $activityStats['total_activities'] === 0 &&
    $activityStats['last_activity'] === null) {
    echo "  ✓ Test passed - Zero stats for new user\n\n";
} else {
    echo "  ✗ Test failed - Expected zero stats\n\n";
}

// Clean up test user
$stmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $testUserId]);

echo "All tests completed!\n";
