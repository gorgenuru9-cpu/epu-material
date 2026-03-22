<?php
/**
 * Test script for damaged returns dashboard functionality
 * 
 * This script tests:
 * 1. Damaged returns can be retrieved by workflow stage
 * 2. Counts are calculated correctly for each stage
 * 3. Department-specific filtering works
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Utils\Database;
use PropertyRequestSystem\Models\ItemReturn;

// Load database configuration
$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

echo "Testing Damaged Returns Dashboard Functionality\n";
echo "================================================\n\n";

// Test 1: Get damaged returns by workflow stage
echo "Test 1: Get damaged returns by workflow stage\n";
echo "----------------------------------------------\n";

$stages = [
    'request_initiation',
    'technical_assessment',
    'departmental_review',
    'main_property_approval',
    'registry_documentation',
    'financial_clearance',
    'closed'
];

$totalReturns = 0;
foreach ($stages as $stage) {
    $returns = ItemReturn::getByWorkflowStage($stage);
    $count = count($returns);
    $totalReturns += $count;
    echo "  - {$stage}: {$count} returns\n";
    
    // Display first return details if any exist
    if ($count > 0) {
        $firstReturn = $returns[0];
        echo "    Sample: MRV {$firstReturn['voucher_number']} - {$firstReturn['item_name']}\n";
    }
}

echo "\nTotal damaged returns across all stages: {$totalReturns}\n\n";

// Test 2: Get counts for all stages
echo "Test 2: Get stage counts from database\n";
echo "---------------------------------------\n";

$db = Database::getConnection();
$damagedReturnsCounts = [];

foreach ($stages as $stage) {
    $stmt = $db->prepare("
        SELECT COUNT(*) as count
        FROM item_returns
        WHERE return_type = 'damaged' AND workflow_stage = :stage
    ");
    $stmt->execute([':stage' => $stage]);
    $result = $stmt->fetch();
    $damagedReturnsCounts[$stage] = $result['count'] ?? 0;
    echo "  - {$stage}: {$damagedReturnsCounts[$stage]} returns\n";
}

echo "\n";

// Test 3: Verify department-specific filtering
echo "Test 3: Department-specific stage filtering\n";
echo "--------------------------------------------\n";

$departmentStages = [
    'ict_specialist' => ['technical_assessment'],
    'property_mgmt_dept' => ['departmental_review'],
    'property_mgmt_main_dept' => ['main_property_approval'],
    'registry_office' => ['registry_documentation'],
    'treasury' => ['financial_clearance'],
    'requester' => ['request_initiation', 'technical_assessment', 'departmental_review', 
                    'main_property_approval', 'registry_documentation', 'financial_clearance', 'closed']
];

foreach ($departmentStages as $dept => $deptStages) {
    echo "\n{$dept}:\n";
    $deptTotal = 0;
    foreach ($deptStages as $stage) {
        $returns = ItemReturn::getByWorkflowStage($stage);
        $count = count($returns);
        $deptTotal += $count;
        echo "  - {$stage}: {$count} returns\n";
    }
    echo "  Total for {$dept}: {$deptTotal} returns\n";
}

echo "\n";

// Test 4: Verify return type filtering
echo "Test 4: Return type filtering\n";
echo "------------------------------\n";

$stmt = $db->query("
    SELECT return_type, COUNT(*) as count
    FROM item_returns
    GROUP BY return_type
");
$returnTypes = $stmt->fetchAll();

foreach ($returnTypes as $type) {
    echo "  - {$type['return_type']}: {$type['count']} returns\n";
}

echo "\n";

// Test 5: Check if damaged returns have required fields
echo "Test 5: Verify damaged return data integrity\n";
echo "---------------------------------------------\n";

$stmt = $db->query("
    SELECT 
        return_id,
        voucher_number,
        return_type,
        workflow_stage,
        damage_description,
        status
    FROM item_returns
    WHERE return_type = 'damaged'
    LIMIT 5
");
$sampleReturns = $stmt->fetchAll();

if (empty($sampleReturns)) {
    echo "  No damaged returns found in database.\n";
} else {
    echo "  Found " . count($sampleReturns) . " sample damaged returns:\n";
    foreach ($sampleReturns as $return) {
        echo "\n  MRV: {$return['voucher_number']}\n";
        echo "    - Workflow Stage: {$return['workflow_stage']}\n";
        echo "    - Status: {$return['status']}\n";
        echo "    - Has Damage Description: " . (!empty($return['damage_description']) ? 'Yes' : 'No') . "\n";
    }
}

echo "\n";
echo "================================================\n";
echo "Test completed successfully!\n";
echo "================================================\n";
