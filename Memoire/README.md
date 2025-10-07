# Plateforme SEIC - LBS

Ce projet vise à centraliser la gestion de l'événement SEIC de Lomé Business School.

## Structure du projet

- `public/` : Fichiers accessibles publiquement (HTML, CSS, JS, images, logo)
- `app/` : Scripts PHP (authentification, gestion des éditions, projets, rapports, salles, administration)
- `uploads/` : Rapports PDF (accès sécurisé via PHP)
- `config/` : Configuration de la base de données et constantes
- `database/` : Scripts SQL d'initialisation
- `docs/` : Documentation utilisateur et technique

## Démarrage
1. Importer le script SQL de `database/init.sql` dans votre MySQL.
2. Configurer la connexion dans `config/config.php`.
3. Placer le logo officiel dans `public/images/` (remplacer le logo provisoire si besoin).
4. Accéder à `public/index.html` pour voir la page d'accueil.

---

Pour toute question, voir la documentation dans `docs/`. 