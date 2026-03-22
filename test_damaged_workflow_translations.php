<?php
/**
 * Test Script for Damaged Workflow Translation Updates
 * Verifies that all damaged workflow pages use translation keys correctly
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use PropertyRequestSystem\Services\LanguageService;

// Initialize language service
$translations = require __DIR__ . '/lang/am.php';
LanguageService::setLanguage('am');

// Helper function
function __($key, $params = []) {
    return PropertyRequestSystem\Services\LanguageService::translate($key, $params);
}

echo "=== Testing Damaged Workflow Translations ===\n\n";

// Test translation keys
$testKeys = [
    'damaged.title',
    'damaged.return_request',
    'damaged.damage_description',
    'damaged.damage_evidence',
    'damaged.ict.title',
    'damaged.ict.technical_findings',
    'damaged.ict.repairability_assessment',
    'damaged.ict.recommendation',
    'damaged.property_dept.title',
    'damaged.property_dept.recommendation',
    'damaged.property_main.title',
    'damaged.property_main.approval_action',
    'damaged.registry.title',
    'damaged.registry.removal_doc_number',
    'damaged.treasury.title',
    'damaged.treasury.financial_review_notes',
    'damaged.submit_success',
    'damaged.ict.submit_success',
    'damaged.property_dept.submit_success',
    'damaged.property_main.submit_success',
    'damaged.registry.submit_success',
    'damaged.treasury.submit_success',
];

$allPassed = true;

foreach ($testKeys as $key) {
    $translation = __($key);
    
    // Check if translation exists (not returning the key itself)
    if ($translation === $key) {
        echo "❌ MISSING: $key\n";
        $allPassed = false;
    } else {
        // Check UTF-8 encoding
        if (mb_check_encoding($translation, 'UTF-8')) {
            echo "✅ OK: $key => " . mb_substr($translation, 0, 50) . (mb_strlen($translation) > 50 ? '...' : '') . "\n";
        } else {
            echo "❌ ENCODING ERROR: $key\n";
            $allPassed = false;
        }
    }
}

echo "\n=== Testing Form Validation Messages ===\n\n";

$validationKeys = [
    'error.validation',
    'error.general',
    'damaged.description_required',
    'damaged.justification_required',
    'damaged.reason_required',
    'damaged.file_too_large',
    'damaged.invalid_file_type',
    'damaged.file_upload_failed',
];

foreach ($validationKeys as $key) {
    $translation = __($key);
    
    if ($translation === $key) {
        echo "❌ MISSING: $key\n";
        $allPassed = false;
    } else {
        if (mb_check_encoding($translation, 'UTF-8')) {
            echo "✅ OK: $key => $translation\n";
        } else {
            echo "❌ ENCODING ERROR: $key\n";
            $allPassed = false;
        }
    }
}

echo "\n=== Summary ===\n";
if ($allPassed) {
    echo "✅ All translation keys are present and UTF-8 encoded correctly!\n";
} else {
    echo "❌ Some translation keys are missing or have encoding issues.\n";
}

echo "\n=== Testing Amharic Character Display ===\n";
echo "Sample Amharic text: " . __('damaged.title') . "\n";
echo "UTF-8 check: " . (mb_check_encoding(__('damaged.title'), 'UTF-8') ? 'PASS' : 'FAIL') . "\n";
echo "Character count: " . mb_strlen(__('damaged.title')) . "\n";
echo "Byte count: " . strlen(__('damaged.title')) . "\n";

?>
