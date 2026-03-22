<?php
/**
 * Test script for DamageReport model
 * This script verifies the DamageReport methods work correctly
 * 
 * Tests:
 * 1. Create damage report with valid data
 * 2. Retrieve damage report by return ID
 * 3. Update damage report
 * 4. Validate repairability assessment constraint
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/utils/Database.php';
require_once __DIR__ . '/src/models/DamageReport.php';
require_once __DIR__ . '/src/models/ItemReturn.php';
require_once __DIR__ . '/src/models/ItemAssignment.php';

use PropertyRequestSystem\Models\DamageReport;
use PropertyRequestSystem\Models\ItemReturn;
use PropertyRequestSystem\Utils\Database;

echo "Testing DamageReport Model\n";
echo "==========================\n\n";

$db = Database::getConnection();

// Setup: Create a test return for testing
echo "Setup: Creating test return...\n";
$stmt = $db->query("SELECT assignment_id FROM item_assignments LIMIT 1");
$assignmentId = $stmt->fetchColumn();

if (!$assignmentId) {
    echo "✗ No item assignments found. Please create test data first.\n";
    exit(1);
}

$stmt = $db->query("SELECT user_id FROM users WHERE department = 'ict_specialist' LIMIT 1");
$ictSpecialistId = $stmt->fetchColumn();

$createdTestUser = false;
if (!$ictSpecialistId) {
    echo "No ICT specialist found. Creating temporary test user...\n";
    $stmt = $db->prepare("
        INSERT INTO users (username, password_hash, full_name, department, identification_number)
        VALUES (:username, :password, :full_name, :department, :id_number)
    ");
    $testUsername = 'test_ict_' . time();
    $stmt->execute([
        ':username' => $testUsername,
        ':password' => password_hash('test123', PASSWORD_DEFAULT),
        ':full_name' => 'Test ICT Specialist',
        ':department' => 'ict_specialist',
        ':id_number' => 'TEST_ICT_' . time()
    ]);
    $ictSpecialistId = $db->lastInsertId();
    $createdTestUser = true;
    echo "✓ Temporary ICT specialist created (ID: $ictSpecialistId)\n";
}

$returnId = ItemReturn::create($assignmentId, 1, 'Test damaged item', 1);
if (!$returnId) {
    echo "✗ Failed to create test return\n";
    exit(1);
}
echo "✓ Test return created (ID: $returnId)\n\n";

// Test 1: Create damage report with valid data
echo "Test 1: Create damage report with valid data\n";
$reportId = DamageReport::create(
    $returnId,
    $ictSpecialistId,
    'Screen is cracked and display is not working properly',
    'requires_replacement',
    null,
    15000.00,
    'Item is beyond economical repair. Recommend replacement.',
    [
        ['filename' => 'damage_photo1.jpg', 'path' => 'uploads/damaged_items/photo1.jpg'],
        ['filename' => 'damage_photo2.jpg', 'path' => 'uploads/damaged_items/photo2.jpg']
    ]
);

if ($reportId) {
    echo "  ✓ Damage report created successfully (ID: $reportId)\n\n";
} else {
    echo "  ✗ Failed to create damage report\n\n";
    exit(1);
}

// Test 2: Retrieve damage report by return ID
echo "Test 2: Retrieve damage report by return ID\n";
$report = DamageReport::getByReturnId($returnId);

if ($report) {
    echo "  Report ID: " . $report['report_id'] . "\n";
    echo "  ICT Specialist: " . $report['ict_specialist_name'] . "\n";
    echo "  Technical Findings: " . substr($report['technical_findings'], 0, 50) . "...\n";
    echo "  Repairability: " . $report['repairability_assessment'] . "\n";
    echo "  Estimated Replacement Cost: " . $report['estimated_replacement_cost'] . "\n";
    echo "  Attachments: " . count($report['report_attachments']) . " files\n";
    
    if ($report['report_id'] == $reportId &&
        $report['return_id'] == $returnId &&
        $report['repairability_assessment'] === 'requires_replacement' &&
        count($report['report_attachments']) === 2) {
        echo "  ✓ Test passed - Report retrieved correctly\n\n";
    } else {
        echo "  ✗ Test failed - Report data mismatch\n\n";
    }
} else {
    echo "  ✗ Test failed - Report not found\n\n";
}

// Test 3: Update damage report
echo "Test 3: Update damage report\n";
$updateSuccess = DamageReport::update($reportId, [
    'technical_findings' => 'Updated: Screen is completely shattered and internal components are damaged',
    'estimated_replacement_cost' => 18000.00,
    'recommendation' => 'Updated: Immediate replacement required. Item is non-functional.'
]);

if ($updateSuccess) {
    $updatedReport = DamageReport::getByReturnId($returnId);
    if ($updatedReport['estimated_replacement_cost'] == 18000.00 &&
        strpos($updatedReport['technical_findings'], 'Updated:') !== false) {
        echo "  ✓ Test passed - Report updated successfully\n\n";
    } else {
        echo "  ✗ Test failed - Update did not persist\n\n";
    }
} else {
    echo "  ✗ Test failed - Update operation failed\n\n";
}

// Test 4: Validate repairability assessment constraint (Property 6)
echo "Test 4: Validate repairability assessment constraint\n";
$invalidReportId = DamageReport::create(
    $returnId,
    $ictSpecialistId,
    'Test invalid assessment',
    'invalid_value', // Invalid assessment value
    null,
    1000.00,
    'Test recommendation',
    []
);

if ($invalidReportId === false) {
    echo "  ✓ Test passed - Invalid repairability assessment rejected\n\n";
} else {
    echo "  ✗ Test failed - Invalid assessment was accepted\n\n";
}

// Test 5: Test all valid repairability assessments
echo "Test 5: Test all valid repairability assessments\n";
$validAssessments = ['repairable', 'requires_replacement', 'must_dispose'];
$allValid = true;

foreach ($validAssessments as $assessment) {
    $testReportId = DamageReport::create(
        $returnId,
        $ictSpecialistId,
        "Test for $assessment",
        $assessment,
        1000.00,
        2000.00,
        "Test recommendation for $assessment",
        []
    );
    
    if (!$testReportId) {
        echo "  ✗ Failed to create report with assessment: $assessment\n";
        $allValid = false;
    }
}

if ($allValid) {
    echo "  ✓ Test passed - All valid assessments accepted\n\n";
} else {
    echo "  ✗ Test failed - Some valid assessments were rejected\n\n";
}

// Test 6: Update with invalid repairability assessment
echo "Test 6: Update with invalid repairability assessment\n";
$invalidUpdate = DamageReport::update($reportId, [
    'repairability_assessment' => 'invalid_assessment'
]);

if ($invalidUpdate === false) {
    echo "  ✓ Test passed - Invalid assessment update rejected\n\n";
} else {
    echo "  ✗ Test failed - Invalid assessment update was accepted\n\n";
}

// Test 7: Retrieve non-existent report
echo "Test 7: Retrieve non-existent report\n";
$nonExistentReport = DamageReport::getByReturnId(999999);

if ($nonExistentReport === null) {
    echo "  ✓ Test passed - Returns null for non-existent report\n\n";
} else {
    echo "  ✗ Test failed - Should return null for non-existent report\n\n";
}

// Test 8: Update with empty data array
echo "Test 8: Update with empty data array\n";
$emptyUpdate = DamageReport::update($reportId, []);

if ($emptyUpdate === false) {
    echo "  ✓ Test passed - Empty update rejected\n\n";
} else {
    echo "  ✗ Test failed - Empty update should be rejected\n\n";
}

// Cleanup: Delete test return (cascade will delete reports)
echo "Cleanup: Removing test data...\n";
$stmt = $db->prepare("DELETE FROM item_returns WHERE return_id = :return_id");
$stmt->execute([':return_id' => $returnId]);
echo "✓ Test data cleaned up\n";

// Cleanup: Delete temporary ICT specialist user if created
if ($createdTestUser) {
    $stmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $ictSpecialistId]);
    echo "✓ Temporary ICT specialist user removed\n";
}

echo "\n";
echo "All tests completed!\n";
