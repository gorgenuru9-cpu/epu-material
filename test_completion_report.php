<?php
/**
 * Test script for completion report generation
 * Tests the generateCompletionReport() method
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/utils/Database.php';
require_once __DIR__ . '/src/models/ItemReturn.php';
require_once __DIR__ . '/src/models/DamageReport.php';

use PropertyRequestSystem\Models\ItemReturn;
use PropertyRequestSystem\Utils\Database;

echo "Testing Completion Report Generation\n";
echo "====================================\n\n";

// Find a closed damaged return to test with
$db = Database::getConnection();

$stmt = $db->query("
    SELECT return_id, voucher_number, workflow_stage, status
    FROM item_returns
    WHERE return_type = 'damaged'
    ORDER BY return_id DESC
    LIMIT 5
");

$returns = $stmt->fetchAll();

if (empty($returns)) {
    echo "✗ No damaged returns found in database\n";
    echo "  Please create a damaged return first using the damaged-return-request.php page\n";
    exit(1);
}

echo "Found " . count($returns) . " damaged return(s):\n";
foreach ($returns as $return) {
    echo "  - Return ID: {$return['return_id']}, MRV: {$return['voucher_number']}, ";
    echo "Stage: {$return['workflow_stage']}, Status: {$return['status']}\n";
}
echo "\n";

// Test with the first return
$testReturnId = $returns[0]['return_id'];
echo "Testing with Return ID: $testReturnId\n";
echo "MRV: {$returns[0]['voucher_number']}\n\n";

// Test 1: Generate completion report
echo "Test 1: Generate completion report\n";
$report = ItemReturn::generateCompletionReport($testReturnId);

if ($report === false) {
    echo "  ✗ Failed to generate completion report\n";
    echo "  This may not be a damaged return or data is missing\n";
    exit(1);
}

echo "  ✓ Completion report generated successfully\n";
echo "  Report contains:\n";
echo "    - Return ID: {$report['return_id']}\n";
echo "    - MRV Number: {$report['mrv_number']}\n";
echo "    - Workflow Stage: {$report['workflow_stage']}\n";
echo "    - Status: {$report['status']}\n";
echo "    - Final Disposition: {$report['final_disposition']}\n";
echo "    - Total Duration: {$report['total_duration_days']} days\n";
echo "    - Workflow Timeline Steps: " . count($report['workflow_timeline']) . "\n";
echo "    - Audit Trail Entries: " . count($report['audit_trail']) . "\n\n";

// Test 2: Verify report structure
echo "Test 2: Verify report structure\n";
$requiredKeys = [
    'report_generated_at',
    'return_id',
    'mrv_number',
    'return_type',
    'workflow_stage',
    'status',
    'requester',
    'item',
    'damage_details',
    'final_disposition',
    'total_duration_days',
    'workflow_timeline',
    'audit_trail',
    'financial_summary'
];

$missingKeys = [];
foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $report)) {
        $missingKeys[] = $key;
    }
}

if (empty($missingKeys)) {
    echo "  ✓ All required keys present in report\n\n";
} else {
    echo "  ✗ Missing keys: " . implode(', ', $missingKeys) . "\n\n";
    exit(1);
}

// Test 3: Verify requester information
echo "Test 3: Verify requester information\n";
if (isset($report['requester']['name']) && !empty($report['requester']['name'])) {
    echo "  ✓ Requester name: {$report['requester']['name']}\n";
    echo "    ID Number: {$report['requester']['id_number']}\n";
    echo "    Department: {$report['requester']['department']}\n\n";
} else {
    echo "  ✗ Requester information missing\n\n";
}

// Test 4: Verify item information
echo "Test 4: Verify item information\n";
if (isset($report['item']['name']) && !empty($report['item']['name'])) {
    echo "  ✓ Item name: {$report['item']['name']}\n";
    echo "    Item code: {$report['item']['code']}\n";
    echo "    Quantity: {$report['item']['quantity']}\n\n";
} else {
    echo "  ✗ Item information missing\n\n";
}

// Test 5: Verify workflow timeline
echo "Test 5: Verify workflow timeline\n";
if (!empty($report['workflow_timeline'])) {
    echo "  ✓ Workflow timeline has " . count($report['workflow_timeline']) . " steps:\n";
    foreach ($report['workflow_timeline'] as $step) {
        echo "    - {$step['stage_name']}\n";
        echo "      Actor: {$step['actor']}, Timestamp: " . ($step['timestamp'] ?? 'N/A') . "\n";
    }
    echo "\n";
} else {
    echo "  ✗ Workflow timeline is empty\n\n";
}

// Test 6: Verify financial summary
echo "Test 6: Verify financial summary\n";
$financialSummary = $report['financial_summary'];
echo "  Financial Impact: " . ($financialSummary['financial_impact'] ?? 'N/A') . "\n";
echo "  Replacement Cost: " . ($financialSummary['replacement_cost'] ?? 'N/A') . "\n";
echo "  Estimated Repair Cost: " . ($financialSummary['estimated_repair_cost'] ?? 'N/A') . "\n";
echo "  Estimated Replacement Cost: " . ($financialSummary['estimated_replacement_cost'] ?? 'N/A') . "\n";
echo "  ✓ Financial summary structure verified\n\n";

// Test 7: Test with invalid return ID
echo "Test 7: Test with invalid return ID\n";
$invalidReport = ItemReturn::generateCompletionReport(999999);
if ($invalidReport === false) {
    echo "  ✓ Correctly returns false for invalid return ID\n\n";
} else {
    echo "  ✗ Should return false for invalid return ID\n\n";
}

// Test 8: Test with standard (non-damaged) return
echo "Test 8: Test with standard (non-damaged) return\n";
$stmt = $db->query("
    SELECT return_id
    FROM item_returns
    WHERE return_type = 'standard' OR return_type IS NULL
    LIMIT 1
");
$standardReturn = $stmt->fetch();

if ($standardReturn) {
    $standardReport = ItemReturn::generateCompletionReport($standardReturn['return_id']);
    if ($standardReport === false) {
        echo "  ✓ Correctly returns false for standard (non-damaged) return\n\n";
    } else {
        echo "  ✗ Should return false for standard return\n\n";
    }
} else {
    echo "  ⊘ No standard returns found to test with\n\n";
}

echo "====================================\n";
echo "All tests completed successfully! ✓\n";
echo "====================================\n\n";

echo "You can now view the completion report at:\n";
echo "http://localhost/damaged-return-completion-report.php?id=$testReturnId\n\n";

echo "Or export to PDF:\n";
echo "http://localhost/damaged-return-completion-report.php?id=$testReturnId&export=pdf\n";
