-- Create item_returns table
CREATE TABLE IF NOT EXISTS item_returns (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    quantity_returned INT NOT NULL,
    return_reason TEXT,
    returned_by INT,
    returned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES item_assignments(assignment_id),
    FOREIGN KEY (returned_by) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
