<?php
/**
 * Test Script: Damaged Return Request Page Verification
 * 
 * This script verifies that the damaged-return-request.php page:
 * - Exists and is accessible
 * - Has proper form structure
 * - Includes all required fields
 * - Has proper validation
 * - Includes Amharic labels
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Damaged Return Request Page - Verification Test              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: File Existence
echo "[Test 1] Checking if damaged-return-request.php exists...\n";
$filePath = __DIR__ . '/public/damaged-return-request.php';
if (file_exists($filePath)) {
    echo "      ✓ File exists at: public/damaged-return-request.php\n";
} else {
    echo "      ✗ File not found!\n";
    exit(1);
}

// Test 2: File Content Analysis
echo "\n[Test 2] Analyzing file content...\n";
$content = file_get_contents($filePath);

$requiredElements = [
    'damage_description' => 'Damage description textarea field',
    'damage_evidence' => 'File upload input for damage evidence',
    'quantity_returned' => 'Quantity returned input field',
    'return_reason' => 'Return reason select field',
    'enctype="multipart/form-data"' => 'Form multipart encoding for file upload',
    'ItemReturn::createDamagedReturn' => 'Damaged return creation method call',
    'NotificationService::notifyDepartment' => 'Notification to ICT department',
    'AuditLog::log' => 'Audit logging',
];

$passed = 0;
$failed = 0;

foreach ($requiredElements as $element => $description) {
    if (strpos($content, $element) !== false) {
        echo "      ✓ Found: $description\n";
        $passed++;
    } else {
        echo "      ✗ Missing: $description\n";
        $failed++;
    }
}

// Test 3: Validation Logic
echo "\n[Test 3] Checking validation logic...\n";
$validationChecks = [
    'if (empty($damageDescription))' => 'Damage description validation',
    'if ($fileSize > $maxFileSize)' => 'File size validation (10MB)',
    'if (!in_array($fileType, $allowedTypes))' => 'File type validation',
    '$maxFileSize = 10 * 1024 * 1024' => '10MB file size limit constant',
];

foreach ($validationChecks as $check => $description) {
    if (strpos($content, $check) !== false) {
        echo "      ✓ Found: $description\n";
        $passed++;
    } else {
        echo "      ✗ Missing: $description\n";
        $failed++;
    }
}

// Test 4: Amharic Labels
echo "\n[Test 4] Checking Amharic labels...\n";
$amharicLabels = [
    'የተጎዳ እቃ መመለስ' => 'Page title',
    'የጉዳት መግለጫ' => 'Damage description label',
    'የጉዳት ማስረጃ' => 'Damage evidence label',
    'የመመለሻ ምክንያት' => 'Return reason label',
    'የሚመለስ ብዛት' => 'Quantity returned label',
];

foreach ($amharicLabels as $label => $description) {
    if (strpos($content, $label) !== false) {
        echo "      ✓ Found: $description ($label)\n";
        $passed++;
    } else {
        echo "      ✗ Missing: $description ($label)\n";
        $failed++;
    }
}

// Test 5: Error Messages
echo "\n[Test 5] Checking error messages...\n";
$errorMessages = [
    'ፋይሉ ከ10MB በላይ ነው' => 'File too large error',
    'ልክ ያልሆነ የፋይል አይነት' => 'Invalid file type error',
    'እባክዎ የጉዳት መግለጫ ያስገቡ' => 'Missing damage description error',
];

foreach ($errorMessages as $message => $description) {
    if (strpos($content, $message) !== false) {
        echo "      ✓ Found: $description\n";
        $passed++;
    } else {
        echo "      ✗ Missing: $description\n";
        $failed++;
    }
}

// Test 6: File Upload Handling
echo "\n[Test 6] Checking file upload handling...\n";
$uploadChecks = [
    'uploads/damaged_items/' => 'Upload directory path',
    'move_uploaded_file' => 'File move operation',
    'pathinfo($fileName, PATHINFO_EXTENSION)' => 'File extension extraction',
    'uniqid()' => 'Unique filename generation',
];

foreach ($uploadChecks as $check => $description) {
    if (strpos($content, $check) !== false) {
        echo "      ✓ Found: $description\n";
        $passed++;
    } else {
        echo "      ✗ Missing: $description\n";
        $failed++;
    }
}

// Test 7: Language File Updates
echo "\n[Test 7] Checking language file updates...\n";
$langFilePath = __DIR__ . '/lang/am.php';
if (file_exists($langFilePath)) {
    $langContent = file_get_contents($langFilePath);
    $langKeys = [
        'damaged.title' => 'Damaged item title translation',
        'damaged.damage_description' => 'Damage description translation',
        'damaged.file_too_large' => 'File too large error translation',
    ];
    
    foreach ($langKeys as $key => $description) {
        if (strpos($langContent, $key) !== false) {
            echo "      ✓ Found: $description\n";
            $passed++;
        } else {
            echo "      ✗ Missing: $description\n";
            $failed++;
        }
    }
} else {
    echo "      ✗ Language file not found!\n";
    $failed += 3;
}

// Summary
echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Test Summary                                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

echo "Total Tests: $total\n";
echo "Passed: $passed ✓\n";
echo "Failed: $failed ✗\n";
echo "Success Rate: $percentage%\n\n";

if ($failed === 0) {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✓ All tests passed! Page is ready for use.                   ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    exit(0);
} else {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✗ Some tests failed. Please review the implementation.       ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    exit(1);
}
