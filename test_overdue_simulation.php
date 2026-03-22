<?php
/**
 * Test Script: Simulate Overdue Damaged Returns
 * 
 * This script creates test damaged returns and backdates them to simulate
 * overdue conditions, then runs the cron job to verify notifications are sent.
 */

require_once __DIR__ . '/vendor/autoload.php';

use PropertyRequestSystem\Utils\Database;
use PropertyRequestSystem\Models\ItemReturn;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Simulate Overdue Damaged Returns for Testing                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$db = Database::getConnection();

// Find assignments to create test returns
$stmt = $db->query("
    SELECT assignment_id, item_id
    FROM item_assignments
    LIMIT 3
");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($assignments) === 0) {
    echo "✗ No assignments found. Cannot create test data.\n";
    echo "  Please ensure there are item assignments in the system.\n";
    exit(1);
}

echo "Found " . count($assignments) . " assignment(s)\n\n";

// Create test damaged returns at different stages with different overdue durations
$testCases = [
    [
        'stage' => 'technical_assessment',
        'days_overdue' => 10,
        'description' => 'Laptop screen cracked - awaiting ICT assessment'
    ],
    [
        'stage' => 'departmental_review',
        'days_overdue' => 8,
        'description' => 'Printer not working - awaiting Property Dept review'
    ],
    [
        'stage' => 'main_property_approval',
        'days_overdue' => 15,
        'description' => 'Mouse damaged - awaiting Property Main approval'
    ]
];

$createdReturns = [];

foreach ($testCases as $index => $testCase) {
    if (!isset($assignments[$index])) {
        break;
    }
    
    $assignment = $assignments[$index];
    
    echo "Creating test case " . ($index + 1) . ":\n";
    echo "  Stage: {$testCase['stage']}\n";
    echo "  Days overdue: {$testCase['days_overdue']}\n";
    
    try {
        // Create damaged return
        $returnId = ItemReturn::createDamagedReturn(
            $assignment['assignment_id'],
            1,
            'Test overdue simulation',
            $testCase['description'],
            1, // User ID 1
            []
        );
        
        if ($returnId) {
            echo "  ✓ Created damaged return (ID: $returnId)\n";
            
            // Update workflow stage
            $db->prepare("
                UPDATE item_returns
                SET workflow_stage = :stage
                WHERE return_id = :return_id
            ")->execute([
                ':return_id' => $returnId,
                ':stage' => $testCase['stage']
            ]);
            
            echo "  ✓ Set workflow stage to: {$testCase['stage']}\n";
            
            // Backdate the returned_at timestamp
            $db->prepare("
                UPDATE item_returns
                SET returned_at = DATE_SUB(NOW(), INTERVAL :days DAY)
                WHERE return_id = :return_id
            ")->execute([
                ':return_id' => $returnId,
                ':days' => $testCase['days_overdue']
            ]);
            
            echo "  ✓ Backdated to {$testCase['days_overdue']} days ago\n";
            
            // Get the return details
            $return = ItemReturn::findById($returnId);
            $createdReturns[] = [
                'return_id' => $returnId,
                'mrv' => $return['voucher_number'],
                'stage' => $testCase['stage'],
                'days_overdue' => $testCase['days_overdue']
            ];
            
            echo "  ✓ MRV: {$return['voucher_number']}\n\n";
        } else {
            echo "  ✗ Failed to create damaged return\n\n";
        }
    } catch (Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n\n";
    }
}

if (count($createdReturns) === 0) {
    echo "✗ No test returns created. Exiting.\n";
    exit(1);
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Created Test Returns Summary                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

foreach ($createdReturns as $return) {
    echo "• MRV: {$return['mrv']}\n";
    echo "  Stage: {$return['stage']}\n";
    echo "  Days overdue: {$return['days_overdue']}\n\n";
}

echo "Now running the cron job to send overdue reminders...\n\n";
echo "═══════════════════════════════════════════════════════════════════\n";

// Execute the cron job
passthru('C:\xampp\php\php.exe cron/check_overdue_damaged_returns.php', $exitCode);

echo "═══════════════════════════════════════════════════════════════════\n\n";

if ($exitCode === 0) {
    echo "✓ Cron job executed successfully\n\n";
    
    echo "Verification:\n";
    echo "1. Check the notifications table for new reminder notifications\n";
    echo "2. Verify notifications were sent to the correct departments:\n";
    echo "   - technical_assessment → ict_specialist\n";
    echo "   - departmental_review → property_mgmt_dept\n";
    echo "   - main_property_approval → property_mgmt_main_dept\n\n";
    
    // Query notifications created in the last minute
    $stmt = $db->query("
        SELECT n.notification_id, n.user_id, u.department, n.message, n.created_at
        FROM notifications n
        JOIN users u ON n.user_id = u.user_id
        WHERE n.created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
          AND n.message LIKE '%የዘገየ የተጎዳ እቃ የመመለሻ ማስታወሻ%'
        ORDER BY n.created_at DESC
    ");
    
    $recentNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($recentNotifications) > 0) {
        echo "✓ Found " . count($recentNotifications) . " reminder notification(s) created:\n\n";
        
        foreach ($recentNotifications as $notification) {
            echo "  • User ID: {$notification['user_id']}\n";
            echo "    Department: {$notification['department']}\n";
            echo "    Created: {$notification['created_at']}\n";
            echo "    Message preview: " . substr($notification['message'], 0, 50) . "...\n\n";
        }
    } else {
        echo "⚠ No reminder notifications found in the last minute\n";
        echo "  This may be because:\n";
        echo "  - No users exist in the responsible departments\n";
        echo "  - The cron job encountered an error\n\n";
    }
} else {
    echo "✗ Cron job failed with exit code: $exitCode\n\n";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Cleanup                                                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "To clean up test data, run:\n";
foreach ($createdReturns as $return) {
    echo "  DELETE FROM item_returns WHERE return_id = {$return['return_id']};\n";
}
echo "\n";
