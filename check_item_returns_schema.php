<?php
/**
 * Check item_returns table schema
 */

require_once __DIR__ . '/vendor/autoload.php';

use PropertyRequestSystem\Utils\Database;

$dbConfig = require __DIR__ . '/config/database.php';
Database::configure($dbConfig);

$db = Database::getConnection();

echo "=== item_returns Table Schema ===\n\n";

$stmt = $db->query('DESCRIBE item_returns');

while ($row = $stmt->fetch()) {
    echo sprintf("%-30s %-30s %s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
    );
}

echo "\n";
