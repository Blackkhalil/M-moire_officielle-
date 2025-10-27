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
 * @version 1.0
 */

// On démarre la session pour pouvoir stocker les informations de l'utilisateur
session_start();
require_once __DIR__ . '/../config/config.php';

function register($nom, $email, $mot_de_passe, $role = 'etudiant') {
    global $pdo;
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
    return $stmt->execute([$nom, $email, $hash, $role]);
}

function login($email, $mot_de_passe) {
    global $pdo;
    
    // Debug
    error_log("Tentative de connexion: $email");
    
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        error_log("Utilisateur non trouvé: $email");
        return false;
    }
    
    error_log("Utilisateur trouvé: " . $user['nom']);
    error_log("Mot de passe stocké: " . $user['mot_de_passe']);
    error_log("Mot de passe fourni: " . $mot_de_passe);
    
    // Vérifier le mot de passe (texte brut ou haché)
    $password_valid = false;
    
    // Comparaison directe (pour mots de passe en texte brut)
    if ($user['mot_de_passe'] === $mot_de_passe) {
        $password_valid = true;
        error_log("Mot de passe valide (texte brut)");
    }
    // Essayer avec password_verify (pour mots de passe hachés)
    elseif (password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $password_valid = true;
        error_log("Mot de passe valide (haché)");
    } else {
        error_log("Mot de passe invalide");
    }
    
    if ($password_valid) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        error_log("Connexion réussie pour: " . $user['nom']);
        return true;
    }
    
    return false;
}

function logout() {
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
} 