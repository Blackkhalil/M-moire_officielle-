-- ============================================================
-- SEIC LBS : schéma complet de la base de données (12 tables)
--
-- Ce fichier remplace les anciens scripts (init.sql, add_*.sql…).
-- Utilisation :
--   1. créer une base vide en utf8mb4 (par exemple « seic_lbs ») ;
--   2. importer ce fichier (phpMyAdmin > Importer, ou php database/setup_wamp.php) ;
--   3. facultatif : importer database/demo.sql pour disposer de données d'essai.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Comptes : étudiants, organisateurs, visiteurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('etudiant','organisateur','visiteur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Éditions de la SEIC
CREATE TABLE editions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    annee YEAR NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Salles du campus (la clé vers groupes est ajoutée plus bas : les deux tables se référencent)
CREATE TABLE salles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    localisation VARCHAR(255),
    id_groupe INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Groupes d'étudiants, rattachés à une édition et à une salle
CREATE TABLE groupes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_edition INT,
    id_salle INT,
    nombre_membres INT DEFAULT 0,
    FOREIGN KEY (id_edition) REFERENCES editions(id),
    FOREIGN KEY (id_salle) REFERENCES salles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE salles
    ADD CONSTRAINT fk_salles_groupe FOREIGN KEY (id_groupe) REFERENCES groupes(id);

-- Projets soumis par les étudiants
CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    categorie VARCHAR(100),
    nom_groupe VARCHAR(100) NULL,
    nombre_membres INT UNSIGNED NULL DEFAULT 0,
    membres TEXT,
    id_edition INT,
    FOREIGN KEY (id_edition) REFERENCES editions(id),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_projets_utilisateur (id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rapports PDF déposés, avec leur état de validation
CREATE TABLE rapports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT,
    id_utilisateur INT NULL,
    date_soumission DATETIME,
    fichier VARCHAR(255),
    etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
    commentaire TEXT,
    FOREIGN KEY (id_projet) REFERENCES projets(id),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_rapports_utilisateur (id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Présentations PowerPoint (et vidéo facultative), avec leur état de validation
CREATE TABLE presentations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT NOT NULL,
    id_utilisateur INT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_fichier INT NOT NULL,
    video_nom_fichier VARCHAR(255) NULL,
    video_chemin_fichier VARCHAR(500) NULL,
    video_taille_fichier INT NULL,
    date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP,
    etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
    commentaire TEXT,
    FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_presentations_projet (id_projet),
    INDEX idx_presentations_etat (etat_validation),
    INDEX idx_presentations_utilisateur (id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Commentaires laissés sur les projets
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT NOT NULL,
    id_utilisateur INT NOT NULL,
    commentaire TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_commentaires_projet (id_projet),
    INDEX idx_commentaires_utilisateur (id_utilisateur),
    INDEX idx_commentaires_date (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jetons de réinitialisation de mot de passe
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions, journal d'activité et notifications
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL UNIQUE,
    user_id INT,
    data TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_activities_user_id (user_id),
    INDEX idx_activities_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_id (user_id),
    INDEX idx_notifications_is_read (is_read),
    INDEX idx_notifications_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
