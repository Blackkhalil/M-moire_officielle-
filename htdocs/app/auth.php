<?php
/**
 * Système d'authentification et de gestion des sessions utilisateur
 * 
 * Ce fichier gère tout ce qui concerne l'authentification :
 * - Vérification des identifiants de connexion
 * - Gestion des sessions utilisateur sécurisées
 * - Vérification des rôles (étudiant, organisateur, visiteur)
 * - Protection des pages nécessitant une authentification
 * 
 * @author Khalil
 * @version 2.0 - Sécurité renforcée
 */

// Chargement config avant session pour un cookie path cohérent (BASE_URL)
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');

    $cookiePath = rtrim(BASE_URL, '/');
    if ($cookiePath === '') {
        $cookiePath = '/';
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookiePath,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function register($nom, $email, $mot_de_passe, $role = 'etudiant') {
    global $pdo;

    if (strlen($mot_de_passe) < 8) {
        return false;
    }

    $hash = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
    $ok = $stmt->execute([$nom, $email, $hash, $role]);

    if ($ok) {
        require_once __DIR__ . '/mailer.php';
        send_welcome_email($email, $nom);
    }

    return $ok;
}

function login($email, $mot_de_passe) {
    global $pdo;

    $email = strtolower(trim((string) $email));
    $mot_de_passe = (string) $mot_de_passe;

    if ($email === '' || $mot_de_passe === '') {
        sleep(1);
        return false;
    }

    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE LOWER(email) = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sleep(1);
        return false;
    }

    $stored = (string) $user['mot_de_passe'];
    $valid = password_verify($mot_de_passe, $stored);

    if (!$valid && !str_starts_with($stored, '$2y$') && !str_starts_with($stored, '$2a$')) {
        $valid = hash_equals($stored, $mot_de_passe);
        if ($valid) {
            $upgrade = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?');
            $upgrade->execute([
                password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]),
                $user['id'],
            ]);
        }
    }

    if ($valid) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role'],
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
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
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

function is_organisateur(): bool
{
    $role = current_user()['role'] ?? '';
    return $role === 'organisateur';
}

/** @deprecated Utiliser is_organisateur() */
function is_staff(): bool
{
    return is_organisateur();
}

/**
 * Valide qu'un chemin de redirection est interne (évite les "open redirects").
 * Retourne null si le chemin est absent ou pointe vers un site externe.
 */
function safe_redirect_target(?string $path): ?string
{
    if ($path === null || $path === '') {
        return null;
    }
    if (str_starts_with($path, '//') || str_contains($path, '://') || str_starts_with($path, '\\')) {
        return null;
    }
    return $path;
}

/**
 * Redirige vers login.php en conservant la page demandée (via ?redirect=...),
 * pour que l'utilisateur y arrive directement une fois connecté
 * (ex. lien "Consulter la soumission" reçu par e-mail).
 */
function redirect_to_login(): void
{
    $current = safe_redirect_target($_SERVER['REQUEST_URI'] ?? null);
    redirect_to($current ? 'login.php?redirect=' . urlencode($current) : 'login.php');
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