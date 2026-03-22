-- Assign Role Examples
-- Use these SQL commands to assign roles directly

USE property_request_system;

-- Example 1: Make a user an IT Admin
UPDATE users 
SET department = 'it_admin' 
WHERE username = 'your_username';

-- Example 2: Make a user Treasury
UPDATE users 
SET department = 'treasury' 
WHERE username = 'your_username';

-- Example 3: Make a user Property Management Main Dept
UPDATE users 
SET department = 'property_mgmt_main_dept' 
WHERE username = 'your_username';

-- Example 4: Make a user Requester
UPDATE users 
SET department = 'requester' 
WHERE username = 'your_username';

-- View all users and their current roles
SELECT 
    user_id,
    username,
    full_name,
    department,
    identification_number,
    created_at
FROM users
ORDER BY created_at DESC;

-- Available Roles/Departments:
-- 'requester' - ጠያቂ
-- 'requester_main_dept' - ጠያቂው ዋና ክፍል
-- 'property_mgmt_main_dept' - የንብረት አስተዳደር ዋና ክፍል
-- 'property_mgmt_dept' - የንብረት አስተዳደር ክፍል
-- 'registry_office' - መዝገብ ቤት
-- 'treasury' - ግምጃ ቤት
-- 'it_admin' - የአይቲ አስተዳዳሪ
