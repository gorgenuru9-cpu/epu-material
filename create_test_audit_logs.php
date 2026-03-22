<?php
/**
 * Create Test Audit Logs
 * Creates test audit log entries to verify pagination
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

echo "Creating Test Audit Logs\n";
echo "========================\n\n";

$db = Database::getConnection();

// Find a user
$stmt = $db->query("SELECT user_id, username FROM users LIMIT 1");
$user = $stmt->fetch();

if (!$user) {
    echo "❌ No users found. Please create a user first.\n";
    exit(1);
}

echo "Creating 50 test audit logs for user: {$user['username']} (ID: {$user['user_id']})\n\n";

// Get a request ID to use
$requestStmt = $db->query("SELECT request_id FROM requests LIMIT 1");
$request = $requestStmt->fetch();

if (!$request) {
    echo "❌ No requests found. Creating test audit logs without request_id is not possible.\n";
    echo "   The audit_logs table requires a request_id.\n";
    exit(1);
}

$requestId = $request['request_id'];
echo "Using request ID: $requestId\n\n";

// Create 50 test audit log entries
$actions = [
    'user_login',
    'user_logout',
    'request_created',
    'request_viewed',
    'request_updated',
    'profile_updated',
    'password_changed',
    'notification_viewed',
    'report_generated',
    'settings_updated'
];

$details = [
    'Logged in successfully',
    'Logged out',
    'Created new request',
    'Viewed request details',
    'Updated request information',
    'Updated profile information',
    'Changed password',
    'Viewed notification',
    'Generated monthly report',
    'Updated system settings'
];

$stmt = $db->prepare("
    INSERT INTO audit_logs (user_id, request_id, action, details, created_at)
    VALUES (:user_id, :request_id, :action, :details, :created_at)
");

for ($i = 0; $i < 50; $i++) {
    $actionIndex = $i % count($actions);
    $createdAt = date('Y-m-d H:i:s', strtotime("-$i hours"));
    
    $stmt->execute([
        ':user_id' => $user['user_id'],
        ':request_id' => $requestId,
        ':action' => $actions[$actionIndex],
        ':details' => $details[$actionIndex],
        ':created_at' => $createdAt
    ]);
    
    if (($i + 1) % 10 === 0) {
        echo "  Created " . ($i + 1) . " audit logs...\n";
    }
}

echo "\n✓ Successfully created 50 test audit logs\n\n";

// Verify count
$countStmt = $db->prepare("SELECT COUNT(*) FROM audit_logs WHERE user_id = :user_id");
$countStmt->execute([':user_id' => $user['user_id']]);
$totalCount = $countStmt->fetchColumn();

echo "Total audit logs for user: $totalCount\n";
echo "Expected pages (20 per page): " . ceil($totalCount / 20) . "\n\n";

echo "Test the pagination at:\n";
echo "http://localhost/user-activity.php?user_id={$user['user_id']}\n";
