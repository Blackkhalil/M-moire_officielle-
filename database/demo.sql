-- ============================================================
-- SEIC LBS : données de démonstration (facultatif)
--
-- À importer après database/schema.sql, sur une base vide.
-- Ces données sont fictives. Trois comptes d'essai sont créés,
-- avec le même mot de passe : seic-demo-2026
--   etudiant@example.com       (étudiant)
--   organisateur@example.com   (organisateur)
--   visiteur@example.com       (visiteur)
-- Ne jamais importer ce fichier sur un site en production.
-- ============================================================

SET NAMES utf8mb4;

INSERT INTO utilisateurs (id, nom, email, mot_de_passe, role) VALUES
(1, 'Étudiant Démo', 'etudiant@example.com', '$2y$12$H9ByVWphZYwYLivUDKeJOOMCFpKRTa9H7UfU8srlWkP18Y8lhALni', 'etudiant'),
(2, 'Organisateur Démo', 'organisateur@example.com', '$2y$12$H9ByVWphZYwYLivUDKeJOOMCFpKRTa9H7UfU8srlWkP18Y8lhALni', 'organisateur'),
(3, 'Visiteur Démo', 'visiteur@example.com', '$2y$12$H9ByVWphZYwYLivUDKeJOOMCFpKRTa9H7UfU8srlWkP18Y8lhALni', 'visiteur');

INSERT INTO editions (id, annee, description, date_debut, date_fin) VALUES
(1, 2025, 'SEIC 2025 (édition de démonstration)', '2025-04-07', '2025-04-11'),
(2, 2026, 'SEIC 2026 (édition de démonstration)', '2026-04-06', '2026-04-10');

INSERT INTO salles (id, nom, localisation) VALUES
(1, 'Salle A', 'Bâtiment principal, rez-de-chaussée'),
(2, 'Salle B', 'Bâtiment principal, 1er étage');

INSERT INTO groupes (id, nom, id_edition, id_salle, nombre_membres) VALUES
(1, 'Groupe Alpha', 1, 1, 4),
(2, 'Groupe Bêta', 2, 2, 5);

INSERT INTO projets (id, id_utilisateur, nom, description, categorie, nom_groupe, nombre_membres, membres, id_edition) VALUES
(1, 1, 'Potager partagé du campus', 'Projet de démonstration : un potager entretenu par les étudiants pour approvisionner la cafétéria.', 'Environnement', 'Groupe Alpha', 4, 'Étudiant Démo et trois camarades', 1),
(2, 1, 'Covoiturage étudiant', 'Projet de démonstration : une application pour partager les trajets entre le campus et la ville.', 'Technologie', 'Groupe Bêta', 5, 'Étudiant Démo et quatre camarades', 2);
