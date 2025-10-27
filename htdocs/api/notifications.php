<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../config/config.php';

if (!is_logged_in()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$user = current_user();

// Vérifier si la table notifications existe
$table_exists = $pdo->query("SHOW TABLES LIKE 'notifications'")->rowCount() > 0;

if (!$table_exists) {
    header('Content-Type: application/json');
    echo json_encode([
        'notifications' => [],
        'unread_count' => 0,
        'message' => 'Système de notifications non encore installé'
    ]);
    exit;
}

// Charger le gestionnaire de notifications seulement si la table existe
require_once __DIR__ . '/../app/notification_manager.php';
$notificationManager = getNotificationManager($pdo);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_unread':
        $notifications = $notificationManager->getUnreadNotifications($user['id'], 5);
        $count = $notificationManager->getUnreadCount($user['id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => $count
        ]);
        break;
        
    case 'mark_read':
        $notificationId = $_POST['notification_id'] ?? null;
        if ($notificationId) {
            $success = $notificationManager->markAsRead($notificationId, $user['id']);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID de notification manquant']);
        }
        break;
        
    case 'mark_all_read':
        $success = $notificationManager->markAllAsRead($user['id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        break;
        
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Action non reconnue']);
}
?>
