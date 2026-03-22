<?php
require 'vendor/autoload.php';
require 'config/constants.php';

$dbConfig = require 'config/database.php';
PropertyRequestSystem\Utils\Database::configure($dbConfig);
$db = PropertyRequestSystem\Utils\Database::getConnection();

echo "=== IT Admin Users ===\n";
$stmt = $db->query("SELECT user_id, username, full_name FROM users WHERE department = 'it_admin'");
$admins = $stmt->fetchAll();
foreach ($admins as $admin) {
    echo "- ID: {$admin['user_id']}, Username: {$admin['username']}, Name: {$admin['full_name']}\n";
    
    // Check notifications for this admin
    $notifStmt = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ?");
    $notifStmt->execute([$admin['user_id']]);
    $count = $notifStmt->fetch()['count'];
    echo "  Notifications: $count\n";
}

echo "\n=== ICT Support Requests ===\n";
try {
    $ictStmt = $db->query("SELECT COUNT(*) as count FROM ict_support_requests");
    $ictCount = $ictStmt->fetch()['count'];
    echo "Total ICT Support Requests: $ictCount\n";
} catch (Exception $e) {
    echo "ICT Support table not found or error: " . $e->getMessage() . "\n";
}

echo "\n=== Recent Property Requests ===\n";
$reqStmt = $db->query("SELECT request_id, form20_data, created_at FROM requests ORDER BY created_at DESC LIMIT 5");
$requests = $reqStmt->fetchAll();
foreach ($requests as $req) {
    $data = json_decode($req['form20_data'], true);
    echo "- Request #{$req['request_id']}: {$data['item_description']} ({$req['created_at']})\n";
}

echo "\n=== All Notifications ===\n";
$allNotif = $db->query("SELECT n.notification_id, n.user_id, u.username, n.message, n.created_at 
                        FROM notifications n 
                        JOIN users u ON n.user_id = u.user_id 
                        ORDER BY n.created_at DESC LIMIT 10");
$notifs = $allNotif->fetchAll();
if (empty($notifs)) {
    echo "No notifications found in database\n";
} else {
    foreach ($notifs as $notif) {
        echo "- [{$notif['username']}] {$notif['message']} ({$notif['created_at']})\n";
    }
}
