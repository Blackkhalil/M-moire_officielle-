<?php
/**
 * Système d'authentification et de gestion des sessions utilisateur
 * 
 * Ce fichier gère tout ce qui concerne l'authentification :
 * - Vérification des identifiants de connexion
 * - Gestion des sessions utilisateur sécurisées
 * - Vérification des rôles (étudiant, organisateur, admin)
 * - Protection des pages nécessitant une authentification
 * 
 * @author Khalil
 * @version 2.0 - Sécurité renforcée
 */

// Configuration sécurisée de la session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

require_once __DIR__ . '/../config/config.php';

function register($nom, $email, $mot_de_passe, $role = 'etudiant') {
    global $pdo;
    
    if (strlen($mot_de_passe) < 8) {
        return false;
    }
    
    $hash = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
    return $stmt->execute([$nom, $email, $hash, $role]);
}

function login($email, $mot_de_passe) {
    global $pdo;
    
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        sleep(1);
        return false;
    }
    
    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
        session_regenerate_id(true);
        
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        $_SESSION['last_activity'] = time();
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        return true;
    }
    
    sleep(1);
    return false;
}

function logout() {
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

function is_logged_in() {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        logout();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_input() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function require_valid_csrf() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        exit('Requete invalide (CSRF).');
    }
}