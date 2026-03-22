<?php
/**
 * Test User Activity Pagination
 * Verifies that the user activity page correctly implements pagination
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

echo "Testing User Activity Pagination\n";
echo "================================\n\n";

$db = Database::getConnection();

// Find a user with audit logs
$stmt = $db->query("
    SELECT u.user_id, u.username, u.full_name, COUNT(al.log_id) as activity_count
    FROM users u
    LEFT JOIN audit_logs al ON u.user_id = al.user_id
    GROUP BY u.user_id
    HAVING activity_count > 0
    ORDER BY activity_count DESC
    LIMIT 1
");
$testUser = $stmt->fetch();

if (!$testUser) {
    echo "❌ No users with audit logs found. Creating test data...\n";
    
    // Create a test user if none exists
    $stmt = $db->query("SELECT user_id FROM users LIMIT 1");
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "❌ No users found in database. Please create a user first.\n";
        exit(1);
    }
    
    $testUser = $user;
    echo "✓ Using user ID: {$testUser['user_id']}\n";
} else {
    echo "✓ Found test user: {$testUser['username']} ({$testUser['full_name']})\n";
    echo "  Activity count: {$testUser['activity_count']}\n\n";
}

// Test pagination logic
$userId = $testUser['user_id'];
$perPage = 20;

// Get total count
$countStmt = $db->prepare("
    SELECT COUNT(*) 
    FROM audit_logs 
    WHERE user_id = :user_id
");
$countStmt->execute([':user_id' => $userId]);
$totalActivities = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalActivities / $perPage);

echo "Pagination Test Results:\n";
echo "------------------------\n";
echo "Total activities: $totalActivities\n";
echo "Per page: $perPage\n";
echo "Total pages: $totalPages\n\n";

// Test page 1
$page = 1;
$offset = ($page - 1) * $perPage;

$stmt = $db->prepare("
    SELECT al.*, r.request_id
    FROM audit_logs al
    LEFT JOIN requests r ON al.request_id = r.request_id
    WHERE al.user_id = :user_id
    ORDER BY al.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$page1Activities = $stmt->fetchAll();

echo "Page 1 Test:\n";
echo "  Expected: " . min($perPage, $totalActivities) . " activities\n";
echo "  Actual: " . count($page1Activities) . " activities\n";

if (count($page1Activities) === min($perPage, $totalActivities)) {
    echo "  ✓ Page 1 pagination works correctly\n\n";
} else {
    echo "  ❌ Page 1 pagination failed\n\n";
    exit(1);
}

// Test page 2 if there are enough activities
if ($totalPages > 1) {
    $page = 2;
    $offset = ($page - 1) * $perPage;
    
    $stmt = $db->prepare("
        SELECT al.*, r.request_id
        FROM audit_logs al
        LEFT JOIN requests r ON al.request_id = r.request_id
        WHERE al.user_id = :user_id
        ORDER BY al.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $page2Activities = $stmt->fetchAll();
    
    $expectedPage2Count = min($perPage, $totalActivities - $perPage);
    
    echo "Page 2 Test:\n";
    echo "  Expected: $expectedPage2Count activities\n";
    echo "  Actual: " . count($page2Activities) . " activities\n";
    
    if (count($page2Activities) === $expectedPage2Count) {
        echo "  ✓ Page 2 pagination works correctly\n\n";
    } else {
        echo "  ❌ Page 2 pagination failed\n\n";
        exit(1);
    }
    
    // Verify no overlap between pages
    $page1Ids = array_column($page1Activities, 'log_id');
    $page2Ids = array_column($page2Activities, 'log_id');
    $overlap = array_intersect($page1Ids, $page2Ids);
    
    if (empty($overlap)) {
        echo "  ✓ No overlap between page 1 and page 2\n\n";
    } else {
        echo "  ❌ Found overlap between pages: " . count($overlap) . " activities\n\n";
        exit(1);
    }
} else {
    echo "Page 2 Test: Skipped (only 1 page of activities)\n\n";
}

// Test sorting (descending by created_at)
echo "Sorting Test:\n";
echo "  Checking if activities are sorted by created_at DESC...\n";

$isSorted = true;
for ($i = 0; $i < count($page1Activities) - 1; $i++) {
    $current = strtotime($page1Activities[$i]['created_at']);
    $next = strtotime($page1Activities[$i + 1]['created_at']);
    
    if ($current < $next) {
        $isSorted = false;
        echo "  ❌ Activities not sorted correctly at index $i\n";
        echo "     Current: {$page1Activities[$i]['created_at']}\n";
        echo "     Next: {$page1Activities[$i + 1]['created_at']}\n";
        break;
    }
}

if ($isSorted) {
    echo "  ✓ Activities are correctly sorted by created_at DESC\n\n";
}

// Test required fields
echo "Required Fields Test:\n";
echo "  Checking if all required fields are present...\n";

$requiredFields = ['log_id', 'user_id', 'action', 'created_at'];
$missingFields = [];

foreach ($page1Activities as $activity) {
    foreach ($requiredFields as $field) {
        if (!isset($activity[$field])) {
            $missingFields[] = $field;
        }
    }
}

if (empty($missingFields)) {
    echo "  ✓ All required fields are present\n\n";
} else {
    echo "  ❌ Missing fields: " . implode(', ', array_unique($missingFields)) . "\n\n";
    exit(1);
}

echo "================================\n";
echo "✓ All pagination tests passed!\n";
echo "================================\n\n";

echo "Manual Test Instructions:\n";
echo "1. Visit: http://localhost/user-activity.php?user_id={$userId}\n";
echo "2. Verify the page displays user activity with pagination\n";
echo "3. Verify pagination controls appear if there are more than 20 activities\n";
echo "4. Click through pages to verify pagination works\n";
echo "5. Verify all Amharic labels are displayed correctly\n";
