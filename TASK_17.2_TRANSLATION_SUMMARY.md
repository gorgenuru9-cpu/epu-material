# Task 17.2: Translation Updates Summary

## Completed Files:
1. ✅ **damaged-return-request.php** - Fully updated with __() translation calls
2. ⚠️ **ict-assessment.php** - Partially updated (title, headers, error messages)

## Remaining Files to Update:

### 3. property-dept-review.php
- Add __() helper function after Database::configure()
- Replace all hardcoded Amharic strings with translation keys
- Key translations needed:
  - Page title: __('damaged.property_dept.title')
  - Form labels: __('damaged.property_dept.recommendation'), __('damaged.property_dept.justification')
  - Buttons: __('damaged.property_dept.submit_recommendation')
  - Error messages: __('damaged.access_denied'), __('damaged.invalid_return')

### 4. property-main-approval-damaged.php
- Add __() helper function
- Replace hardcoded strings:
  - Title: __('damaged.property_main.title')
  - Form fields: __('damaged.property_main.approval_action'), __('damaged.property_main.reason')
  - Actions: __('damaged.property_main.action.approve'), __('damaged.property_main.action.request_revision'), __('damaged.property_main.action.reject')
  - Messages: __('damaged.property_main.submit_success')

### 5. registry-documentation-damaged.php
- Add __() helper function
- Replace strings:
  - Title: __('damaged.registry.title')
  - Form fields: __('damaged.registry.item_status_change'), __('damaged.registry.removal_doc_number')
  - Status options: __('damaged.registry.status.damaged'), __('damaged.registry.status.disposed')
  - Button: __('damaged.registry.submit_documentation')

### 6. treasury-clearance.php
- Add __() helper function
- Replace strings:
  - Title: __('damaged.treasury.title')
  - Form fields: __('damaged.treasury.financial_review_notes'), __('damaged.treasury.clearance_decision')
  - Decisions: __('damaged.treasury.decision.approve'), __('damaged.treasury.decision.request_more_info')
  - Button: __('damaged.treasury.submit_clearance')

### 7. damaged-return-details.php
- Add __() helper function
- Replace workflow stage labels with translation keys
- Update all section headers and labels

## Translation Keys Already in lang/am.php:
All required translation keys are already defined in lang/am.php including:
- damaged.* keys for all workflow pages
- damaged.ict.* keys for ICT assessment
- damaged.property_dept.* keys for property department
- damaged.property_main.* keys for property main approval
- damaged.registry.* keys for registry documentation
- damaged.treasury.* keys for treasury clearance
- Common keys: damaged.item_details, damaged.voucher_number, damaged.item_name, etc.

## Pattern to Follow:
```php
// 1. Add helper function after Database::configure()
function __($key, $params = []) {
    return PropertyRequestSystem\Services\LanguageService::translate($key, $params);
}

// 2. Replace hardcoded text in PHP
Session::setFlash('error', __('damaged.access_denied'));

// 3. Replace hardcoded text in HTML
<h1><?= __('damaged.ict.title') ?></h1>
<label><?= __('damaged.damage_description') ?> *</label>
```

## Form Validation Messages:
All form validation error messages should use translation keys:
- __('error.validation')
- __('damaged.description_required')
- __('damaged.justification_required')
- __('damaged.reason_required')

## Success Messages:
- __('damaged.submit_success')
- __('damaged.ict.submit_success')
- __('damaged.property_dept.submit_success')
- __('damaged.property_main.submit_success')
- __('damaged.registry.submit_success')
- __('damaged.treasury.submit_success')
