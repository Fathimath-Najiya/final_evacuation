<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

// Initialize Firebase Admin SDK
$factory = (new Factory)->withServiceAccount('path/to/serviceAccountKey.json');
$messaging = $factory->createMessaging();

// Connect to PostgreSQL database
$pdo = new PDO('pgsql:host=your_host;dbname=your_database', 'your_username', 'your_password');

// Function to send alert notifications
function sendAlertNotification($buildingId, $alertMessage, $messaging, $pdo) {
    try {
        // Validate inputs
        if (empty($buildingId) || empty($alertMessage)) {
            throw new Exception('Building ID and alert message are required.');
        }

        // Sanitize inputs
        $buildingId = htmlspecialchars($buildingId);
        $alertMessage = htmlspecialchars($alertMessage);

        // Fetch user tokens associated with the building from PostgreSQL
        $stmt = $pdo->prepare('SELECT u.fcm_token FROM users u INNER JOIN user_building ub ON u.user_id = ub.user_id WHERE ub.building_id = :buildingId');
        $stmt->execute(['buildingId' => $buildingId]);
        $tokens = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Prepare and send notifications using FCM
        $notification = Notification::create('Alert Notification', $alertMessage);
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withTokens($tokens);

        $messaging->sendMulticast($message);

        return json_encode(['success' => true, 'message' => 'Notifications sent successfully']);
    } catch (Exception $e) {
        return json_encode(['success' => false, 'message' => 'Error sending notifications: ' . $e->getMessage()]);
    }
}

// Check if data is received via POST
if ($_POST) {
    $buildingId = $_POST['buildingId'] ?? null;
    $alertMessage = $_POST['alertMessage'] ?? null;

    if ($buildingId !== null && $alertMessage !== null) {
        echo sendAlertNotification($buildingId, $alertMessage, $messaging, $pdo);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}
?>
