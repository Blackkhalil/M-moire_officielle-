-- Script pour ajouter le champ id_utilisateur aux tables rapports et presentations
-- Cela permet de savoir qui a soumis chaque rapport ou présentation
-- Exécuter ce script dans phpMyAdmin ou via MySQL

-- Ajouter id_utilisateur à la table rapports
ALTER TABLE rapports 
ADD COLUMN id_utilisateur INT NULL AFTER id_projet,
ADD FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL;

-- Ajouter id_utilisateur à la table presentations
ALTER TABLE presentations 
ADD COLUMN id_utilisateur INT NULL AFTER id_projet,
ADD FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL;

-- Ajouter id_utilisateur à la table projets (pour savoir qui a créé le projet)
ALTER TABLE projets 
ADD COLUMN id_utilisateur INT NULL AFTER id,
ADD FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL;

-- Créer des index pour améliorer les performances
CREATE INDEX idx_rapports_utilisateur ON rapports(id_utilisateur);
CREATE INDEX idx_presentations_utilisateur ON presentations(id_utilisateur);
CREATE INDEX idx_projets_utilisateur ON projets(id_utilisateur);
