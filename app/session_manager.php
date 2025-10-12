<?php
/**
 * Système de gestion des sessions pour éviter les conflits multi-utilisateurs
 */

class SessionManager {
    private static $instance = null;
    private $pdo;
    
    private function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public static function getInstance($pdo) {
        if (self::$instance === null) {
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }
    
    /**
     * Démarre une session sécurisée
     */
    public function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuration sécurisée des sessions
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 0); // Mettre à 1 en HTTPS
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
            
            // Régénérer l'ID de session périodiquement
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
    
    /**
     * Vérifie si une session est valide
     */
    public function isValidSession($sessionId) {
        $stmt = $this->pdo->prepare("SELECT id FROM sessions WHERE session_id = ? AND expires_at > NOW()");
        $stmt->execute([$sessionId]);
        return $stmt->fetchColumn() !== false;
    }
    
    /**
     * Nettoie les sessions expirées
     */
    public function cleanupExpiredSessions() {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
        $stmt->execute();
    }
    
    /**
     * Crée un verrou pour éviter les conflits d'écriture
     */
    public function acquireLock($resource, $timeout = 10) {
        $lockName = "lock_" . md5($resource);
        $stmt = $this->pdo->prepare("SELECT GET_LOCK(?, ?)");
        $stmt->execute([$lockName, $timeout]);
        return $stmt->fetchColumn() == 1;
    }
    
    /**
     * Libère un verrou
     */
    public function releaseLock($resource) {
        $lockName = "lock_" . md5($resource);
        $stmt = $this->pdo->prepare("SELECT RELEASE_LOCK(?)");
        $stmt->execute([$lockName]);
        return $stmt->fetchColumn() == 1;
    }
    
    /**
     * Enregistre une activité utilisateur
     */
    public function logActivity($userId, $action, $details = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_activities (user_id, action, details, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
}

/**
 * Fonction utilitaire pour obtenir le gestionnaire de sessions
 */
function getSessionManager($pdo) {
    return SessionManager::getInstance($pdo);
}
?>
