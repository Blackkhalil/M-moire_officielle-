-- Script de mise à jour pour ajouter la gestion des présentations PowerPoint
-- Exécuter ce script pour ajouter la nouvelle table

CREATE TABLE presentations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_fichier INT NOT NULL,
    date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP,
    etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
    commentaire TEXT,
    FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE
);

-- Ajouter un index pour améliorer les performances
CREATE INDEX idx_presentations_projet ON presentations(id_projet);
CREATE INDEX idx_presentations_etat ON presentations(etat_validation);
