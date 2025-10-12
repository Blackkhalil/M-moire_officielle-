<?php
/**
 * Gestionnaire de notifications en temps réel pour l'application SEIC LBS
 * 
 * Ce fichier gère le système de notifications internes qui permet :
 * - D'envoyer des notifications aux utilisateurs lors de la validation/refus de rapports
 * - De notifier tous les membres d'un projet en une seule fois
 * - De marquer les notifications comme lues/non lues
 * - De récupérer les notifications non lues pour l'affichage en temps réel
 * 
 * @author Votre nom
 * @version 1.0
 */

class NotificationManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Crée une notification pour un utilisateur
     */
    public function createNotification($userId, $type, $title, $message, $data = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, data, is_read, created_at) 
            VALUES (?, ?, ?, ?, ?, 0, NOW())
        ");
        return $stmt->execute([$userId, $type, $title, $message, json_encode($data)]);
    }
    
    /**
     * Récupère les notifications non lues d'un utilisateur
     */
    public function getUnreadNotifications($userId, $limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Marque une notification comme lue
     */
    public function markAsRead($notificationId, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notificationId, $userId]);
    }
    
    /**
     * Marque toutes les notifications comme lues
     */
    public function markAllAsRead($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Compte les notifications non lues
     */
    public function getUnreadCount($userId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Notifie les membres d'un projet
     */
    public function notifyProjectMembers($projetId, $type, $title, $message, $data = null) {
        // Récupérer les membres du projet
        $stmt = $this->pdo->prepare("SELECT membres FROM projets WHERE id = ?");
        $stmt->execute([$projetId]);
        $projet = $stmt->fetch();
        
        if (!$projet) return false;
        
        $membres = array_map('trim', explode(',', $projet['membres']));
        $success = true;
        
        foreach ($membres as $nom) {
            $userStmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE nom = ?");
            $userStmt->execute([$nom]);
            $user = $userStmt->fetch();
            
            if ($user) {
                $result = $this->createNotification($user['id'], $type, $title, $message, $data);
                if (!$result) $success = false;
            }
        }
        
        return $success;
    }
}

/**
 * Fonction utilitaire pour obtenir le gestionnaire de notifications
 */
function getNotificationManager($pdo) {
    return new NotificationManager($pdo);
}
?>
