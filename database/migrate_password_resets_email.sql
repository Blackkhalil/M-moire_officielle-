-- Migration : ancien schéma (id_utilisateur, token_hash) → nouveau (email, token)
-- Exécuter une seule fois si la table password_resets existe déjà avec l'ancienne structure.

DROP TABLE IF EXISTS password_resets;

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
);
