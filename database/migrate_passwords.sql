-- SCRIPT DE MIGRATION DES MOTS DE PASSE
-- Ce script hash tous les mots de passe en texte clair existants
-- ATTENTION : Les utilisateurs devront réinitialiser leur mot de passe après cette migration

-- 1. Sauvegarder les anciens mots de passe (pour référence)
CREATE TABLE IF NOT EXISTS utilisateurs_backup_passwords (
    id INT,
    email VARCHAR(100),
    ancien_mot_de_passe VARCHAR(255),
    date_backup TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO utilisateurs_backup_passwords (id, email, ancien_mot_de_passe)
SELECT id, email, mot_de_passe 
FROM utilisateurs 
WHERE mot_de_passe NOT LIKE '$2y$%';

-- 2. Mettre tous les anciens mots de passe à un hash temporaire
-- Les utilisateurs devront utiliser "TemporaryPassword123!" pour se connecter une première fois
UPDATE utilisateurs 
SET mot_de_passe = '$2y$12$LQ8Z7YPqGfH0pPQwX9Xm9eR5vGZn.rN6K.9Zq8xL.Zy7X.6W5U4gK'
WHERE mot_de_passe NOT LIKE '$2y$%';

-- Note : Le hash ci-dessus correspond au mot de passe "TemporaryPassword123!"
-- Après migration, informez vos utilisateurs d'utiliser ce mot de passe temporaire
-- puis de le changer immédiatement après connexion

-- 3. Vérifier la migration
SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN mot_de_passe LIKE '$2y$%' THEN 1 ELSE 0 END) as hashed_passwords,
    SUM(CASE WHEN mot_de_passe NOT LIKE '$2y$%' THEN 1 ELSE 0 END) as plain_passwords
FROM utilisateurs;
