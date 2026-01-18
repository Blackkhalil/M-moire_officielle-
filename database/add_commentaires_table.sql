-- Table pour les commentaires des visiteurs sur les projets
CREATE TABLE IF NOT EXISTS commentaires (
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
);
