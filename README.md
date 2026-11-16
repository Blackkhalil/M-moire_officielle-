# SEIC LBS — plateforme de gestion de la SEIC

Application web réalisée pour mon mémoire de fin de cycle (Licence professionnelle en Système d'Information, option Génie Logiciel, Lomé Business School) :

> *Digitalisation et optimisation des processus de gestion événementielle : cas de la Semaine de l'Entrepreneuriat, de l'Innovation et de la Créativité (SEIC) de Lomé Business School.*

La SEIC est la **Semaine de l'Entrepreneuriat, de l'Innovation et de la Créativité** de l'école. La plateforme remplace une gestion semi-manuelle (inscriptions par e-mail, rapports sur papier, salles attribuées à la main) par un outil unique : dépôt des projets et des rapports, validation par les organisateurs, suivi par chacun.

## Ce que fait la plateforme

| Profil | Ce qu'il peut faire |
|---|---|
| **Étudiant** | Soumettre un projet, un rapport PDF et une présentation PowerPoint ; suivre l'état de chaque dépôt (en attente, validé, refusé) et lire le commentaire de l'organisateur. |
| **Organisateur** | Gérer les éditions, les projets, les groupes et les salles ; valider ou refuser chaque dépôt (commentaire obligatoire en cas de refus) ; exporter une édition en archive ZIP. |
| **Visiteur** | Consulter les projets par édition et les commenter. |

Un e-mail part à chaque étape : bienvenue à l'inscription, alerte aux organisateurs à chaque dépôt, décision envoyée à l'étudiant.

## Technique

- PHP 8 sans framework, MySQL ou MariaDB (PDO, requêtes préparées)
- Bootstrap 5, JavaScript
- PHPMailer pour les e-mails (fourni dans `htdocs/vendor`)
- Sécurité : mots de passe hachés (bcrypt), jetons CSRF, accès par rôle, contrôle des fichiers déposés côté serveur

## Organisation du dépôt

```
htdocs/              Le site : c'est ce dossier que sert le serveur web
  *.php              Les pages (accueil, connexion, tableau de bord, dépôts…)
  app/               La logique : authentification, dépôts, validation, e-mails
  config/            La configuration (modèles .example à copier)
  partials/          Les morceaux de page communs (en-tête, menu, pied de page)
  css/ images/       La mise en forme et les images
  uploads/           Les fichiers déposés (non versionnés)
  vendor/            PHPMailer
database/
  schema.sql         Le schéma complet de la base (12 tables)
  demo.sql           Des données de démonstration (facultatif)
  setup_wamp.php     L'installation de la base en une commande
docs/                L'architecture, les diagrammes UML, le guide de déploiement
DEMARRER_PROJET.bat  Le lancement du projet sous Windows (WampServer)
CHANGELOG.md         L'historique des évolutions
```

## Lancer le projet sur son ordinateur (WampServer)

1. Placer ce dossier dans `C:\wamp64\www\` en gardant son nom, `M-moire_officielle-`.
2. Démarrer WampServer et attendre que son icône soit verte.
3. Copier `htdocs/config/db.example.php` vers `htdocs/config/db.php` (les réglages par défaut conviennent à WampServer).
4. Créer la base de données, une seule fois, dans une invite de commandes ouverte dans ce dossier :

   ```
   C:\wamp64\bin\php\php8.3.28\php.exe database\setup_wamp.php --demo
   ```

   Adapter le numéro de version de PHP à celui de votre WampServer. L'option `--demo` ajoute des données d'essai ; sans elle, la base est créée vide.
5. Double-cliquer sur `DEMARRER_PROJET.bat`. Le projet s'ouvre à l'adresse `http://localhost/M-moire_officielle-/htdocs/`.

Si le dossier porte un autre nom, indiquer son chemin dans un fichier `htdocs/.env` : `BASE_URL=/nom-du-dossier/htdocs/`.

### Comptes de démonstration

Créés par l'option `--demo`, avec le même mot de passe `seic-demo-2026` :

| Adresse | Profil |
|---|---|
| `etudiant@example.com` | Étudiant |
| `organisateur@example.com` | Organisateur |
| `visiteur@example.com` | Visiteur |

Ces comptes sont fictifs : ne jamais importer `demo.sql` sur un site en production.

### Base de données déjà installée

`setup_wamp.php` ne touche à rien si la base contient déjà des tables. Pour repartir de zéro (toutes les données sont alors effacées) : ajouter l'option `--reinitialiser`.

## E-mails

Sans configuration, l'application fonctionne mais n'envoie aucun e-mail. Pour les activer, copier `htdocs/config/mail.example.php` vers `htdocs/config/mail.php` et y saisir les identifiants SMTP. Le détail est dans `htdocs/config/env.example.txt`.

## Mise en ligne

Le projet est prévu pour un hébergement mutualisé PHP et MySQL :

1. envoyer le contenu de `htdocs/` dans le dossier public de l'hébergeur ;
2. créer une base et y importer `database/schema.sql` (jamais `demo.sql`) ;
3. créer `config/db.php` avec `ENV` à `production` et les identifiants de l'hébergeur ;
4. créer un fichier `.env` avec `BASE_URL=/` ;
5. créer le premier compte organisateur : s'inscrire sur le site, puis passer ce compte en `organisateur` dans phpMyAdmin (table `utilisateurs`, colonne `role`).

Le guide complet est dans `docs/GUIDE_DEPLOIEMENT_SECURITE.md`.

Ne jamais versionner `config/db.php`, `config/mail.php`, `.env`, ni le contenu de `uploads/` : ils sont exclus par `.gitignore`.

## Documentation

- `docs/ARCHITECTURE_SEIC.md` : architecture de l'application
- `docs/DIAGRAMMES_UML_ACTUALISES.md` : cas d'utilisation, classes, séquences
- `docs/diagrammes/` : source des diagrammes (draw.io)
- `docs/GUIDE_DEPLOIEMENT_SECURITE.md` : déploiement et sécurité
- `docs/RAPPORT_MISE_A_JOUR.md` : revue de sécurité et corrections

## Auteur

SIBI Ibrahim Khalil — [GitHub](https://github.com/Blackkhalil)

Projet académique réalisé dans le cadre d'un mémoire de licence à Lomé Business School.
