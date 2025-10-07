-- Création des tables pour la plateforme SEIC

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('etudiant','organisateur','visiteur') NOT NULL
);

CREATE TABLE editions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    annee YEAR NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE
);

CREATE TABLE groupes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_edition INT,
    id_salle INT,
    FOREIGN KEY (id_edition) REFERENCES editions(id),
    FOREIGN KEY (id_salle) REFERENCES salles(id)
);

CREATE TABLE salles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    localisation VARCHAR(255),
    id_groupe INT,
    FOREIGN KEY (id_groupe) REFERENCES groupes(id)
);

CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    categorie VARCHAR(100),
    membres TEXT,
    id_edition INT,
    FOREIGN KEY (id_edition) REFERENCES editions(id)
);

CREATE TABLE rapports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT,
    date_soumission DATETIME,
    fichier VARCHAR(255),
    etat_validation ENUM('en_attente','valide','refuse') DEFAULT 'en_attente',
    commentaire TEXT,
    FOREIGN KEY (id_projet) REFERENCES projets(id)
);

-- Données de test
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
('Alice Dupont', 'alice@lbs.tg', 'test', 'etudiant'),
('Bob Organisateur', 'bob@lbs.tg', 'test', 'organisateur'),
('Visiteur Test', 'visiteur@lbs.tg', 'test', 'visiteur');

INSERT INTO editions (annee, description, date_debut, date_fin) VALUES
(2023, 'SEIC 2023 - Innovation et Entrepreneuriat', '2023-06-01', '2023-06-05'),
(2024, 'SEIC 2024 - Impact Social', '2024-06-01', '2024-06-05');

INSERT INTO salles (nom, localisation) VALUES
('Salle A', 'Bâtiment 1, RDC'),
('Salle B', 'Bâtiment 2, 1er étage');

INSERT INTO groupes (nom, id_edition, id_salle) VALUES
('Groupe Alpha', 1, 1),
('Groupe Beta', 2, 2);

INSERT INTO projets (nom, description, categorie, membres, id_edition) VALUES
('Projet Innovant', 'Un projet sur l''innovation.', 'Technologie', 'Alice Dupont, Jean K.', 1),
('Projet Social', 'Projet à impact social.', 'Impact Social', 'Bob Organisateur, Marie L.', 2);

INSERT INTO rapports (id_projet, date_soumission, fichier, etat_validation, commentaire) VALUES
(1, '2023-06-02 10:00:00', 'rapport1.pdf', 'valide', 'Très bon rapport.'),
(2, '2024-06-02 11:00:00', 'rapport2.pdf', 'en_attente', NULL); 