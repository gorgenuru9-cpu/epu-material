<?php
/**
 * Test script for FileUploadService
 * This script verifies the FileUploadService methods work correctly
 * 
 * Tests:
 * 1. Validate file size - valid sizes
 * 2. Validate file size - invalid sizes (over 10MB)
 * 3. Validate file type - valid image types
 * 4. Validate file type - valid document types
 * 5. Validate file type - invalid types
 * 6. Sanitize filename - removes dangerous characters
 * 7. Sanitize filename - generates unique names
 * 8. Upload damage evidence - complete workflow
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/services/FileUploadService.php';

use PropertyRequestSystem\Services\FileUploadService;

echo "Testing FileUploadService\n";
echo "=========================\n\n";

// Test 1: Validate file size - valid sizes
echo "Test 1: Validate file size - valid sizes\n";
$validSizes = [
    1024,                    // 1KB
    1024 * 1024,            // 1MB
    5 * 1024 * 1024,        // 5MB
    10 * 1024 * 1024        // Exactly 10MB
];

$allValid = true;
foreach ($validSizes as $size) {
    $result = FileUploadService::validateFileSize($size);
    if (!$result) {
        echo "  ✗ Failed for size: " . ($size / (1024 * 1024)) . "MB\n";
        $allValid = false;
    }
}

if ($allValid) {
    echo "  ✓ Test passed - All valid sizes accepted\n\n";
} else {
    echo "  ✗ Test failed - Some valid sizes rejected\n\n";
}

// Test 2: Validate file size - invalid sizes (over 10MB and zero)
echo "Test 2: Validate file size - invalid sizes\n";
$invalidSizes = [
    0,                              // Zero size
    10 * 1024 * 1024 + 1,          // 10MB + 1 byte
    15 * 1024 * 1024,              // 15MB
    20 * 1024 * 1024               // 20MB
];

$allInvalid = true;
foreach ($invalidSizes as $size) {
    $result = FileUploadService::validateFileSize($size);
    if ($result) {
        echo "  ✗ Accepted invalid size: " . ($size / (1024 * 1024)) . "MB\n";
        $allInvalid = false;
    }
}

if ($allInvalid) {
    echo "  ✓ Test passed - All invalid sizes rejected (Property 4)\n\n";
} else {
    echo "  ✗ Test failed - Some invalid sizes accepted\n\n";
}

// Test 3: Validate file type - valid image types
echo "Test 3: Validate file type - valid image types\n";
$validImageTypes = [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/gif',
    'image/webp'
];

$allValid = true;
foreach ($validImageTypes as $type) {
    $result = FileUploadService::validateFileType($type);
    if (!$result) {
        echo "  ✗ Failed for type: $type\n";
        $allValid = false;
    }
}

if ($allValid) {
    echo "  ✓ Test passed - All valid image types accepted\n\n";
} else {
    echo "  ✗ Test failed - Some valid image types rejected\n\n";
}

// Test 4: Validate file type - valid document types
echo "Test 4: Validate file type - valid document types\n";
$validDocTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'text/plain'
];

$allValid = true;
foreach ($validDocTypes as $type) {
    $result = FileUploadService::validateFileType($type);
    if (!$result) {
        echo "  ✗ Failed for type: $type\n";
        $allValid = false;
    }
}

if ($allValid) {
    echo "  ✓ Test passed - All valid document types accepted\n\n";
} else {
    echo "  ✗ Test failed - Some valid document types rejected\n\n";
}

// Test 5: Validate file type - invalid types
echo "Test 5: Validate file type - invalid types\n";
$invalidTypes = [
    'application/x-executable',
    'application/x-sh',
    'text/html',
    'application/javascript',
    'video/mp4',
    'audio/mpeg'
];

$allInvalid = true;
foreach ($invalidTypes as $type) {
    $result = FileUploadService::validateFileType($type);
    if ($result) {
        echo "  ✗ Accepted invalid type: $type\n";
        $allInvalid = false;
    }
}

if ($allInvalid) {
    echo "  ✓ Test passed - All invalid types rejected\n\n";
} else {
    echo "  ✗ Test failed - Some invalid types accepted\n\n";
}

// Test 6: Sanitize filename - removes dangerous characters
echo "Test 6: Sanitize filename - removes dangerous characters\n";
$dangerousFilenames = [
    '../../../etc/passwd.txt',
    'file with spaces.jpg',
    'file<script>alert(1)</script>.pdf',
    'file|pipe&ampersand.png',
    'file;semicolon:colon.doc',
    'አማርኛ_ፋይል.jpg'  // Amharic characters
];

$allSafe = true;
foreach ($dangerousFilenames as $filename) {
    $sanitized = FileUploadService::sanitizeFilename($filename);
    
    // Check that sanitized filename doesn't contain dangerous characters
    if (preg_match('/[<>|:;"\'&\\\\\\/]/', $sanitized)) {
        echo "  ✗ Dangerous characters remain in: $sanitized\n";
        $allSafe = false;
    }
    
    // Check that it doesn't contain path traversal
    if (strpos($sanitized, '..') !== false || strpos($sanitized, '/') !== false) {
        echo "  ✗ Path traversal possible in: $sanitized\n";
        $allSafe = false;
    }
}

if ($allSafe) {
    echo "  ✓ Test passed - All dangerous characters removed\n\n";
} else {
    echo "  ✗ Test failed - Some dangerous characters remain\n\n";
}

// Test 7: Sanitize filename - generates unique names
echo "Test 7: Sanitize filename - generates unique names\n";
$filename = 'test_image.jpg';
$sanitized1 = FileUploadService::sanitizeFilename($filename);
usleep(10000); // Sleep 10ms to ensure different timestamp
$sanitized2 = FileUploadService::sanitizeFilename($filename);

if ($sanitized1 !== $sanitized2) {
    echo "  Filename 1: $sanitized1\n";
    echo "  Filename 2: $sanitized2\n";
    echo "  ✓ Test passed - Unique filenames generated\n\n";
} else {
    echo "  ✗ Test failed - Duplicate filenames generated\n\n";
}

// Test 8: File size validation using validateFileSize method
echo "Test 8: File size validation - direct method test\n";

// Test oversized file
$oversizedFileSize = 15 * 1024 * 1024;  // 15MB
$result = FileUploadService::validateFileSize($oversizedFileSize);

if (!$result) {
    echo "  ✓ Test passed - Oversized file (15MB) rejected\n\n";
} else {
    echo "  ✗ Test failed - Oversized file should be rejected\n\n";
}

// Test 9: Upload damage evidence - missing file
echo "Test 9: Upload damage evidence - missing file\n";
$missingFile = [
    'name' => '',
    'type' => '',
    'tmp_name' => '',
    'error' => UPLOAD_ERR_NO_FILE,
    'size' => 0
];

$result = FileUploadService::uploadDamageEvidence($missingFile);

if (!$result['success']) {
    echo "  ✓ Test passed - Missing file rejected\n";
    echo "  Error message: " . $result['error'] . "\n\n";
} else {
    echo "  ✗ Test failed - Missing file should be rejected\n\n";
}

// Test 10: Filename length limit
echo "Test 10: Sanitize filename - length limit\n";
$longFilename = str_repeat('a', 200) . '.jpg';
$sanitized = FileUploadService::sanitizeFilename($longFilename);

// The sanitized filename should have reasonable length (not exceed ~150 chars with prefix)
if (strlen($sanitized) < 200) {
    echo "  Original length: " . strlen($longFilename) . "\n";
    echo "  Sanitized length: " . strlen($sanitized) . "\n";
    echo "  ✓ Test passed - Long filename truncated appropriately\n\n";
} else {
    echo "  ✗ Test failed - Filename not truncated\n\n";
}

// Test 11: Extension preservation
echo "Test 11: Sanitize filename - extension preservation\n";
$testFiles = [
    'document.pdf' => 'pdf',
    'image.JPG' => 'jpg',  // Should be lowercase
    'spreadsheet.xlsx' => 'xlsx',
    'text.TXT' => 'txt'
];

$allPreserved = true;
foreach ($testFiles as $filename => $expectedExt) {
    $sanitized = FileUploadService::sanitizeFilename($filename);
    $actualExt = pathinfo($sanitized, PATHINFO_EXTENSION);
    
    if ($actualExt !== $expectedExt) {
        echo "  ✗ Extension mismatch for $filename: expected $expectedExt, got $actualExt\n";
        $allPreserved = false;
    }
}

if ($allPreserved) {
    echo "  ✓ Test passed - All extensions preserved correctly\n\n";
} else {
    echo "  ✗ Test failed - Some extensions not preserved\n\n";
}

// Test 12: Validate file size boundary (exactly 10MB)
echo "Test 12: Validate file size - boundary test (exactly 10MB)\n";
$exactSize = 10 * 1024 * 1024;
$result = FileUploadService::validateFileSize($exactSize);

if ($result) {
    echo "  ✓ Test passed - Exactly 10MB file accepted (Property 4)\n\n";
} else {
    echo "  ✗ Test failed - Exactly 10MB file should be accepted\n\n";
}

// Test 13: Validate file size boundary (10MB + 1 byte)
echo "Test 13: Validate file size - boundary test (10MB + 1 byte)\n";
$overSize = 10 * 1024 * 1024 + 1;
$result = FileUploadService::validateFileSize($overSize);

if (!$result) {
    echo "  ✓ Test passed - 10MB + 1 byte file rejected (Property 4)\n\n";
} else {
    echo "  ✗ Test failed - 10MB + 1 byte file should be rejected\n\n";
}

echo "\n";
echo "All tests completed!\n";
echo "\nNote: Tests 8-9 simulate file upload validation without actual file operations.\n";
echo "For complete upload testing, use actual file uploads in a web environment.\n";

