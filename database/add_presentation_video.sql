-- Vidéo optionnelle liée à une présentation (extrait du PowerPoint ou démo projet)
ALTER TABLE presentations
    ADD COLUMN video_nom_fichier VARCHAR(255) NULL AFTER taille_fichier,
    ADD COLUMN video_chemin_fichier VARCHAR(500) NULL AFTER video_nom_fichier,
    ADD COLUMN video_taille_fichier INT NULL AFTER video_chemin_fichier;
