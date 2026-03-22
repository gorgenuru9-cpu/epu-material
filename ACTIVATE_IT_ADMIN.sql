-- Activate IT Admin Role
-- Run this SQL to add IT admin to your database

USE property_request_system;

-- Step 1: Modify users table to add it_admin to department enum
ALTER TABLE users 
MODIFY COLUMN department ENUM(
    'requester',
    'requester_main_dept',
    'property_mgmt_main_dept',
    'property_mgmt_dept',
    'registry_office',
    'treasury',
    'it_admin'
) NOT NULL;

-- Step 2: Create default IT admin user
-- Username: admin
-- Password: admin123
INSERT INTO users (username, password_hash, full_name, department, identification_number)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'የአይቲ አስተዳዳሪ',
    'it_admin',
    'IT-ADMIN-001'
)
ON DUPLICATE KEY UPDATE username = username;

-- Step 3: Create user_tasks table for task assignment
CREATE TABLE IF NOT EXISTS user_tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    assigned_by INT NOT NULL,
    task_title VARCHAR(255) NOT NULL,
    task_description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    due_date DATE NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verification queries
SELECT 'IT Admin role added successfully!' as Status;
SELECT * FROM users WHERE department = 'it_admin';
