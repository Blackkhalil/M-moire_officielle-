-- Ajouter le rôle administrateur et créer un compte admin
ALTER TABLE utilisateurs MODIFY COLUMN role ENUM('etudiant','organisateur','visiteur','administrateur') NOT NULL;

-- Créer un compte administrateur
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
('Admin SEIC', 'admin@lbs.tg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur');
-- Mot de passe: "password"
