<?php
/**
 * Test Completion Notification Service
 * Tests the notifyDamagedReturnCompletion method
 */

require_once __DIR__ . '/vendor/autoload.php';

use PropertyRequestSystem\Utils\Database;
use PropertyRequestSystem\Services\NotificationService;
use PropertyRequestSystem\Models\ItemReturn;
use PropertyRequestSystem\Models\DamageReport;

// Load database configuration
$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

echo "=== Testing Completion Notification Service ===\n\n";

try {
    // Test 1: Create a test damaged return with all workflow stages completed
    echo "Test 1: Creating test damaged return...\n";
    
    // First, we need to find an existing assignment or create one
    $db = Database::getConnection();
    
    // Get an existing assignment
    $stmt = $db->query("SELECT assignment_id FROM item_assignments LIMIT 1");
    $assignment = $stmt->fetch();
    
    if (!$assignment) {
        echo "✗ No assignments found. Please create an assignment first.\n";
        exit(1);
    }
    
    $assignmentId = $assignment['assignment_id'];
    echo "✓ Using assignment ID: $assignmentId\n";
    
    // Create damaged return
    $returnId = ItemReturn::createDamagedReturn(
        $assignmentId,
        1,
        'Testing completion notification',
        'Detailed damage description for testing',
        1, // User ID 1
        []
    );
    
    if (!$returnId) {
        echo "✗ Failed to create damaged return\n";
        exit(1);
    }
    
    echo "✓ Created damaged return with ID: $returnId\n";
    
    // Test 2: Create a damage report for the return
    echo "\nTest 2: Creating damage report...\n";
    
    $reportId = DamageReport::create(
        $returnId,
        1, // ICT specialist user ID
        'Test technical findings',
        'requires_replacement', // This should result in "Replaced" disposition
        0.0,
        1500.0,
        'Test recommendation',
        []
    );
    
    if (!$reportId) {
        echo "✗ Failed to create damage report\n";
        exit(1);
    }
    
    echo "✓ Created damage report with ID: $reportId\n";
    
    // Test 3: Transition workflow to financial_clearance stage
    echo "\nTest 3: Transitioning to financial_clearance stage...\n";
    
    $transitionSuccess = ItemReturn::transitionStage($returnId, 'financial_clearance', 1);
    
    if (!$transitionSuccess) {
        echo "✗ Failed to transition to financial_clearance stage\n";
        exit(1);
    }
    
    echo "✓ Transitioned to financial_clearance stage\n";
    
    // Test 4: Close the workflow
    echo "\nTest 4: Closing workflow...\n";
    
    $closeSuccess = ItemReturn::closeWorkflow($returnId);
    
    if (!$closeSuccess) {
        echo "✗ Failed to close workflow\n";
        exit(1);
    }
    
    echo "✓ Workflow closed successfully\n";
    
    // Test 5: Send completion notification
    echo "\nTest 5: Sending completion notification...\n";
    
    $notificationSent = NotificationService::notifyDamagedReturnCompletion($returnId);
    
    if (!$notificationSent) {
        echo "✗ Failed to send completion notification\n";
        exit(1);
    }
    
    echo "✓ Completion notification sent successfully\n";
    
    // Test 6: Verify notification was created
    echo "\nTest 6: Verifying notification in database...\n";
    
    $stmt = $db->prepare("
        SELECT n.*, u.full_name
        FROM notifications n
        JOIN users u ON n.user_id = u.user_id
        WHERE n.message LIKE '%የተጎዳ እቃ የመመለሻ ጥያቄዎ ተጠናቅቋል%'
        ORDER BY n.created_at DESC
        LIMIT 1
    ");
    $stmt->execute();
    $notification = $stmt->fetch();
    
    if (!$notification) {
        echo "✗ Notification not found in database\n";
        exit(1);
    }
    
    echo "✓ Notification found in database\n";
    echo "  - Recipient: {$notification['full_name']}\n";
    echo "  - Message preview: " . substr($notification['message'], 0, 100) . "...\n";
    
    // Test 7: Verify notification contains required elements
    echo "\nTest 7: Verifying notification content...\n";
    
    $requiredElements = [
        'የተጎዳ እቃ የመመለሻ ጥያቄዎ ተጠናቅቋል' => 'Completion message',
        'MRV' => 'MRV number',
        'የመጨረሻ ውሳኔ' => 'Final disposition',
        'ተተክቷል (Replaced)' => 'Replaced disposition (based on requires_replacement)',
        'የማጠናቀቂያ ሪፖርት ለማየት' => 'Report link text',
        '/request-details.php?id=' => 'Report link URL'
    ];
    
    $allElementsPresent = true;
    foreach ($requiredElements as $element => $description) {
        if (strpos($notification['message'], $element) !== false) {
            echo "  ✓ Contains: $description\n";
        } else {
            echo "  ✗ Missing: $description\n";
            $allElementsPresent = false;
        }
    }
    
    if (!$allElementsPresent) {
        echo "\n✗ Some required elements are missing from notification\n";
        echo "\nFull notification message:\n";
        echo $notification['message'] . "\n";
        exit(1);
    }
    
    // Test 8: Test with different repairability assessments
    echo "\nTest 8: Testing different disposition types...\n";
    
    $dispositionTests = [
        'repairable' => 'ተጠግኗል (Repaired)',
        'must_dispose' => 'ተወግዷል (Disposed)'
    ];
    
    foreach ($dispositionTests as $repairability => $expectedDisposition) {
        echo "  Testing $repairability...\n";
        
        // Create new damaged return
        $testReturnId = ItemReturn::createDamagedReturn(
            $assignmentId,
            1,
            "Testing $repairability",
            "Test damage for $repairability",
            1,
            []
        );
        
        // Create damage report with specific repairability
        DamageReport::create(
            $testReturnId,
            1,
            'Test findings',
            $repairability,
            0.0,
            0.0,
            'Test recommendation',
            []
        );
        
        // Transition and close
        ItemReturn::transitionStage($testReturnId, 'financial_clearance', 1);
        ItemReturn::closeWorkflow($testReturnId);
        
        // Send notification
        NotificationService::notifyDamagedReturnCompletion($testReturnId);
        
        // Verify disposition in notification
        $stmt = $db->prepare("
            SELECT message
            FROM notifications
            WHERE message LIKE '%የተጎዳ እቃ የመመለሻ ጥያቄዎ ተጠናቅቋል%'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute();
        $testNotification = $stmt->fetch();
        
        if (strpos($testNotification['message'], $expectedDisposition) !== false) {
            echo "    ✓ Correct disposition: $expectedDisposition\n";
        } else {
            echo "    ✗ Expected disposition not found: $expectedDisposition\n";
            $allElementsPresent = false;
        }
    }
    
    if ($allElementsPresent) {
        echo "\n✓ All tests passed!\n";
        echo "\n=== Completion Notification Service is working correctly ===\n";
    } else {
        echo "\n✗ Some tests failed\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
