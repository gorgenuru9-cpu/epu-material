<?php
/**
 * Test Script: Immutable Record Protection
 * 
 * Tests Task 15.2 implementation:
 * - Damage report deletion protection
 * - Damage report update audit trail
 * - Property Dept recommendation update audit trail
 * - Property Main decision update audit trail
 * - Damaged return deletion protection
 * 
 * Validates: Requirements 9.2, Property 22
 */

require_once __DIR__ . '/vendor/autoload.php';

use PropertyRequestSystem\Utils\Database;
use PropertyRequestSystem\Models\DamageReport;
use PropertyRequestSystem\Models\ItemReturn;
use PropertyRequestSystem\Models\AuditLog;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Immutable Record Protection Test (Task 15.2)                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$testsPassed = 0;
$testsFailed = 0;

/**
 * Test 1: Damage Report Deletion Protection
 */
echo "[Test 1] Damage Report Deletion Protection\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    // Create a test damaged return
    $returnId = ItemReturn::createDamagedReturn(
        1, // assignment_id
        1, // quantity
        'Test return for immutable protection',
        'Test damage description',
        1, // returned_by
        []
    );
    
    if (!$returnId) {
        echo "✗ Failed to create test damaged return\n";
        $testsFailed++;
    } else {
        echo "✓ Created test damaged return (ID: $returnId)\n";
        
        // Create a damage report
        $reportId = DamageReport::create(
            $returnId,
            1, // ict_specialist_id
            'Test technical findings',
            'repairable',
            100.00,
            500.00,
            'Test recommendation',
            []
        );
        
        if (!$reportId) {
            echo "✗ Failed to create test damage report\n";
            $testsFailed++;
        } else {
            echo "✓ Created test damage report (ID: $reportId)\n";
            
            // Attempt to delete the damage report (should fail)
            $deleteResult = DamageReport::delete($reportId, 1);
            
            if ($deleteResult === false) {
                echo "✓ Damage report deletion correctly blocked\n";
                $testsPassed++;
                
                // Verify audit log entry was created
                $auditLogs = AuditLog::getRequestHistory($returnId);
                $deletionBlocked = false;
                foreach ($auditLogs as $log) {
                    if ($log->getAction() === 'damage_report_deletion_blocked') {
                        $deletionBlocked = true;
                        break;
                    }
                }
                
                if ($deletionBlocked) {
                    echo "✓ Deletion attempt logged in audit trail\n";
                    $testsPassed++;
                } else {
                    echo "✗ Deletion attempt NOT logged in audit trail\n";
                    $testsFailed++;
                }
            } else {
                echo "✗ Damage report deletion should have been blocked\n";
                $testsFailed++;
            }
        }
    }
} catch (Exception $e) {
    echo "✗ Test 1 failed with exception: " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

/**
 * Test 2: Damage Report Update Audit Trail
 */
echo "[Test 2] Damage Report Update Audit Trail\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    if (isset($reportId) && $reportId) {
        // Update the damage report
        $updateData = [
            'technical_findings' => 'Updated technical findings',
            'recommendation' => 'Updated recommendation'
        ];
        
        $updateResult = DamageReport::update($reportId, $updateData, 1);
        
        if ($updateResult) {
            echo "✓ Damage report updated successfully\n";
            $testsPassed++;
            
            // Verify audit log entry was created with original values
            $auditLogs = AuditLog::getRequestHistory($returnId);
            $updateLogged = false;
            foreach ($auditLogs as $log) {
                if ($log->getAction() === 'damage_report_updated') {
                    $updateLogged = true;
                    $details = json_decode($log->getDetails(), true);
                    
                    if (isset($details['changes'])) {
                        echo "✓ Update logged in audit trail with original values preserved\n";
                        echo "  Changes tracked: " . count($details['changes']) . " fields\n";
                        $testsPassed++;
                    } else {
                        echo "✗ Update logged but original values not preserved\n";
                        $testsFailed++;
                    }
                    break;
                }
            }
            
            if (!$updateLogged) {
                echo "✗ Update NOT logged in audit trail\n";
                $testsFailed++;
            }
        } else {
            echo "✗ Damage report update failed\n";
            $testsFailed++;
        }
    } else {
        echo "⊘ Skipping test (no damage report created)\n";
    }
} catch (Exception $e) {
    echo "✗ Test 2 failed with exception: " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

/**
 * Test 3: Property Dept Recommendation Update Audit Trail
 */
echo "[Test 3] Property Dept Recommendation Update Audit Trail\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    if (isset($returnId) && $returnId) {
        // Transition to departmental_review stage first
        ItemReturn::transitionStage($returnId, 'departmental_review', 1);
        
        // Set initial recommendation
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE item_returns 
            SET property_dept_recommendation = :recommendation
            WHERE return_id = :return_id
        ");
        $stmt->execute([
            ':return_id' => $returnId,
            ':recommendation' => 'Initial recommendation'
        ]);
        
        // Update the recommendation using the protected method
        $updateResult = ItemReturn::updatePropertyDeptRecommendation(
            $returnId,
            'Updated recommendation with justification',
            1
        );
        
        if ($updateResult) {
            echo "✓ Property Dept recommendation updated successfully\n";
            $testsPassed++;
            
            // Verify audit log entry was created
            $auditLogs = AuditLog::getRequestHistory($returnId);
            $updateLogged = false;
            foreach ($auditLogs as $log) {
                if ($log->getAction() === 'property_dept_recommendation_updated') {
                    $updateLogged = true;
                    $details = json_decode($log->getDetails(), true);
                    
                    if (isset($details['original_value']) && isset($details['new_value'])) {
                        echo "✓ Update logged with original and new values preserved\n";
                        $testsPassed++;
                    } else {
                        echo "✗ Update logged but values not properly preserved\n";
                        $testsFailed++;
                    }
                    break;
                }
            }
            
            if (!$updateLogged) {
                echo "✗ Update NOT logged in audit trail\n";
                $testsFailed++;
            }
        } else {
            echo "✗ Property Dept recommendation update failed\n";
            $testsFailed++;
        }
    } else {
        echo "⊘ Skipping test (no damaged return created)\n";
    }
} catch (Exception $e) {
    echo "✗ Test 3 failed with exception: " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

/**
 * Test 4: Property Main Decision Update Audit Trail
 */
echo "[Test 4] Property Main Decision Update Audit Trail\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    if (isset($returnId) && $returnId) {
        // Transition to main_property_approval stage
        ItemReturn::transitionStage($returnId, 'main_property_approval', 1);
        
        // Set initial decision
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE item_returns 
            SET property_main_decision = :decision
            WHERE return_id = :return_id
        ");
        $stmt->execute([
            ':return_id' => $returnId,
            ':decision' => 'Initial approval decision'
        ]);
        
        // Update the decision using the protected method
        $updateResult = ItemReturn::updatePropertyMainDecision(
            $returnId,
            'Updated approval decision with additional notes',
            1
        );
        
        if ($updateResult) {
            echo "✓ Property Main decision updated successfully\n";
            $testsPassed++;
            
            // Verify audit log entry was created
            $auditLogs = AuditLog::getRequestHistory($returnId);
            $updateLogged = false;
            foreach ($auditLogs as $log) {
                if ($log->getAction() === 'property_main_decision_updated') {
                    $updateLogged = true;
                    $details = json_decode($log->getDetails(), true);
                    
                    if (isset($details['original_value']) && isset($details['new_value'])) {
                        echo "✓ Update logged with original and new values preserved\n";
                        $testsPassed++;
                    } else {
                        echo "✗ Update logged but values not properly preserved\n";
                        $testsFailed++;
                    }
                    break;
                }
            }
            
            if (!$updateLogged) {
                echo "✗ Update NOT logged in audit trail\n";
                $testsFailed++;
            }
        } else {
            echo "✗ Property Main decision update failed\n";
            $testsFailed++;
        }
    } else {
        echo "⊘ Skipping test (no damaged return created)\n";
    }
} catch (Exception $e) {
    echo "✗ Test 4 failed with exception: " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

/**
 * Test 5: Damaged Return Deletion Protection
 */
echo "[Test 5] Damaged Return Deletion Protection\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    if (isset($returnId) && $returnId) {
        // Attempt to delete the damaged return (should fail because it has recommendations)
        $deleteResult = ItemReturn::deleteDamagedReturn($returnId, 1);
        
        if ($deleteResult === false) {
            echo "✓ Damaged return deletion correctly blocked (has recommendations)\n";
            $testsPassed++;
            
            // Verify audit log entry was created
            $auditLogs = AuditLog::getRequestHistory($returnId);
            $deletionBlocked = false;
            foreach ($auditLogs as $log) {
                if ($log->getAction() === 'damaged_return_deletion_blocked') {
                    $deletionBlocked = true;
                    break;
                }
            }
            
            if ($deletionBlocked) {
                echo "✓ Deletion attempt logged in audit trail\n";
                $testsPassed++;
            } else {
                echo "✗ Deletion attempt NOT logged in audit trail\n";
                $testsFailed++;
            }
        } else {
            echo "✗ Damaged return deletion should have been blocked\n";
            $testsFailed++;
        }
        
        // Test deletion at early stage (create new return without damage report)
        $earlyReturnId = ItemReturn::createDamagedReturn(
            1,
            1,
            'Test early stage deletion',
            'Test damage',
            1,
            []
        );
        
        if ($earlyReturnId) {
            $deleteEarlyResult = ItemReturn::deleteDamagedReturn($earlyReturnId, 1);
            
            if ($deleteEarlyResult === true) {
                echo "✓ Deletion allowed at early stage (no damage report)\n";
                $testsPassed++;
            } else {
                echo "✗ Deletion should be allowed at early stage\n";
                $testsFailed++;
            }
        }
    } else {
        echo "⊘ Skipping test (no damaged return created)\n";
    }
} catch (Exception $e) {
    echo "✗ Test 5 failed with exception: " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

/**
 * Test Summary
 */
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Test Summary                                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$totalTests = $testsPassed + $testsFailed;
$passRate = $totalTests > 0 ? round(($testsPassed / $totalTests) * 100, 1) : 0;

echo "Total Tests: $totalTests\n";
echo "Passed: $testsPassed\n";
echo "Failed: $testsFailed\n";
echo "Pass Rate: $passRate%\n\n";

if ($testsFailed === 0) {
    echo "✓ All tests passed! Immutable record protection is working correctly.\n";
    echo "  - Damage reports cannot be deleted\n";
    echo "  - Damage report updates are logged with original values\n";
    echo "  - Property Dept recommendation updates are logged\n";
    echo "  - Property Main decision updates are logged\n";
    echo "  - Damaged returns with recommendations/approvals cannot be deleted\n";
    echo "  - Early stage damaged returns can be deleted\n\n";
    
    echo "Requirements validated:\n";
    echo "  ✓ Requirement 9.2: Immutable records maintained\n";
    echo "  ✓ Property 22: Records not deletable, updates create audit trail\n\n";
} else {
    echo "✗ Some tests failed. Please review the implementation.\n\n";
}

// Cleanup test data
if (isset($returnId) && $returnId) {
    echo "Cleaning up test data...\n";
    $db = Database::getConnection();
    
    // Delete damage report
    if (isset($reportId) && $reportId) {
        $db->exec("DELETE FROM damage_reports WHERE report_id = $reportId");
        echo "  ✓ Deleted test damage report\n";
    }
    
    // Delete audit logs
    $db->exec("DELETE FROM audit_logs WHERE request_id = $returnId");
    echo "  ✓ Deleted test audit logs\n";
    
    // Delete item return
    $db->exec("DELETE FROM item_returns WHERE return_id = $returnId");
    echo "  ✓ Deleted test item return\n";
}

echo "\nTest completed.\n";
