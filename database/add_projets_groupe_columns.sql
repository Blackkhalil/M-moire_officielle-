-- Colonnes utilisées par soumission_projet, gestion_powerpoint, etudiant_projets
ALTER TABLE projets
    ADD COLUMN nom_groupe VARCHAR(100) NULL AFTER categorie,
    ADD COLUMN nombre_membres INT UNSIGNED NULL DEFAULT 0 AFTER nom_groupe;
