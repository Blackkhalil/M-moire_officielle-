# SEIC LBS - Système de Gestion des Projets Étudiants

##  Description

**SEIC LBS** est une application web complète développée en PHP pour la gestion des projets étudiants lors du Salon de l'Innovation et de l'Entrepreneuriat (SEIC) de l'École de Commerce LBS.

Cette application permet aux étudiants de soumettre leurs projets, rapports PDF et présentations PowerPoint, aux organisateurs de les valider ou refuser avec des commentaires, avec des notifications automatiques par e-mail à chaque étape.

##  Fonctionnalités Principales

###  Pour les Étudiants
- **Soumission de projets** : titre, description, catégorie, groupe
- **Soumission de rapports PDF** : Upload sécurisé avec validation de format
- **Soumission de présentations PowerPoint** : Support des fichiers .ppt/.pptx (+ vidéo optionnelle)
- **Suivi des soumissions** : Tableau de bord personnel avec statuts de validation
- **E-mail de bienvenue** à l'inscription et **e-mail de notification** dès qu'un rapport ou une présentation est validé(e) ou refusé(e)

###  Pour les Organisateurs
- **Gestion des rapports et présentations** : Validation/refus avec commentaires personnalisés
- **Notification automatique par e-mail** dès qu'un étudiant soumet un projet, un rapport ou une présentation
- **Statistiques détaillées** : Tableaux de bord avec métriques de soumission
- **Gestion des projets, éditions, groupes et salles**

###  Fonctionnalités Techniques
- **Authentification sécurisée** : sessions PHP, CSRF, hachage bcrypt, rôles (étudiant/organisateur/visiteur)
- **Upload sécurisé** : Validation des fichiers et noms uniques
- **E-mails transactionnels** : PHPMailer + SMTP Mailtrap (sandbox en local, live en production)
- **Interface responsive** : Bootstrap 5 + design system CSS maison (`htdocs/css/modern-style.css`)
- **Zéro build** : PHP pur, pas de bundler/JS build (Bootstrap et Font Awesome via CDN)

##  Installation locale (WAMP)

### Prérequis
- **PHP 8.0+** avec extensions : PDO MySQL, mysqli
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Composer** (pour PHPMailer)
- **WAMP/XAMPP** recommandé pour le développement local

### Étapes d'installation

1. **Cloner le projet** dans le dossier servi par Apache (ex. `c:\wamp64\www\`)

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer la base de données**
   - Copier `htdocs/config/db.example.php` vers `htdocs/config/db.php` (fichier gitignored, jamais commité)
   - Créer une base MySQL `seic_lbs` et importer `database/init.sql`, puis les migrations dans `database/` (ou exécuter `php database/setup_wamp.php` qui fait tout automatiquement)

4. **Configurer l'environnement**
   - Copier `htdocs/config/env.example.txt` vers `htdocs/.env`
   - Renseigner au minimum `MAILTRAP_USER` / `MAILTRAP_PASS` (compte gratuit sur mailtrap.io, section *Email Testing*) pour tester l'envoi d'e-mails en local — sans ces identifiants, les e-mails échouent silencieusement (voir `htdocs/logs/php_errors.log`) sans bloquer l'application

5. **Accéder à l'application**
   - `http://localhost/<nom-du-dossier>/htdocs/index.php`

##  Structure du Projet

Tout ce dont l'application a besoin pour tourner (code métier, config, dépendances, uploads, logs) vit **à l'intérieur** de `htdocs/` — c'est volontaire : ça permet de déployer sur n'importe quel hébergement mutualisé, y compris les offres gratuites qui interdisent à PHP de lire des fichiers en dehors du dossier public (`open_basedir`). Chaque sous-dossier sensible (`app/`, `config/`, `vendor/`, `uploads/`, `logs/`) a son propre `.htaccess` qui bloque l'accès direct par navigateur (`Require all denied`) — seul PHP peut les lire via `require`/`include`, jamais un visiteur via une URL.

```
M-moire_officielle-/
├── htdocs/                   # Racine web publique (DocumentRoot / public_html)
│   ├── app/                  # Logique métier (backend), .htaccess bloque l'accès direct
│   │   ├── auth.php          # Authentification, sessions, CSRF, inscription
│   │   ├── mail_config.php   # Configuration SMTP (PHPMailer)
│   │   ├── mailer.php        # Envoi d'e-mails applicatifs (bienvenue, notifications)
│   │   ├── soumission_projet.php / soumission_rapport.php / soumission_powerpoint.php
│   │   ├── rapports_admin.php / gestion_powerpoint.php  # Validation organisateur
│   │   └── projets.php, editions.php, groupes_salles.php, ...
│   ├── config/                # Configuration, .htaccess bloque l'accès direct
│   │   ├── config.php         # Chargement .env + connexion PDO
│   │   ├── db.php             # Identifiants MySQL (gitignored, à créer depuis db.example.php)
│   │   └── upload_config.php  # Limites et types de fichiers acceptés
│   ├── vendor/                 # Dépendances Composer (PHPMailer), .htaccess bloque l'accès direct
│   ├── uploads/                 # Fichiers uploadés, .htaccess bloque l'accès direct
│   ├── logs/                     # Logs applicatifs (php_errors.log), .htaccess bloque l'accès direct
│   ├── partials/               # head.php / navbar.php / footer.php (layout partagé)
│   ├── css/modern-style.css    # Design system (couleurs, composants, responsive)
│   ├── .env                     # Variables d'environnement (gitignored)
│   ├── dashboard.php, projets.php, soumettre_*.php, ...
│   └── bootstrap.php            # Point d'entrée commun (charge app/auth.php)
├── database/                  # Scripts SQL (schéma initial + migrations) — pas besoin d'être en ligne
└── composer.phar               # Composer local (optionnel)
```

##  Configuration

### Base de données
`htdocs/config/db.php` (à créer depuis `htdocs/config/db.example.php`) définit `DB_HOST`/`DB_NAME`/`DB_USER`/`DB_PASS` selon `ENV = 'local'` ou `'production'`. `htdocs/config/config.php` lit ensuite ces valeurs (avec possibilité de les surcharger via `.env`, voir `htdocs/config/env.example.txt`).

### E-mails
`htdocs/app/mail_config.php` centralise la config SMTP (lue depuis `.env`) : `MAIL_MODE` (`sandbox` en local, `live` en production), `MAILTRAP_HOST/PORT/USER/PASS`, `MAILTRAP_FROM_EMAIL/FROM_NAME`. Ces réglages sont génériques (malgré leur nom) : en `live`, ils peuvent pointer vers Mailtrap (nécessite de vérifier un domaine) ou directement vers Gmail SMTP (`smtp.gmail.com`, avec un mot de passe d'application — voir les deux options commentées dans `htdocs/config/env.example.txt`). Les fonctions d'envoi métier sont dans `htdocs/app/mailer.php`.

L'organisateur qui reçoit les notifications de soumission est déterminé dynamiquement (`SELECT email FROM utilisateurs WHERE role = 'organisateur'`), pas via une adresse codée en dur.

### Uploads
`htdocs/config/upload_config.php` et `htdocs/.user.ini` définissent les limites de taille et extensions acceptées (PDF pour les rapports, PPT/PPTX pour les présentations, vidéos optionnelles).

## 👥 Utilisation

### Soumission d'un Rapport
1. Se connecter en tant qu'étudiant
2. Aller dans « Soumettre un rapport »
3. Sélectionner le projet, uploader le PDF
4. L'organisateur reçoit un e-mail de notification immédiat

### Validation par un Organisateur
1. Se connecter en tant qu'organisateur
2. Aller dans « Gestion des rapports » ou « Gestion des présentations »
3. Valider ou refuser avec un commentaire
4. L'étudiant reçoit un e-mail automatique avec la décision

##  Sécurité

- **Validation des fichiers** : extensions, taille, contenu
- **Protection contre l'injection SQL** : requêtes préparées PDO partout
- **CSRF** : jeton vérifié sur tous les formulaires POST
- **Sessions sécurisées** : cookies `httponly`, `samesite=Lax`, régénération d'ID à la connexion
- **Secrets hors dépôt** : `.env` et `config/db.php` sont gitignorés, seuls les fichiers `.example` sont versionnés
- **Dossiers sensibles protégés par `.htaccess`** (`Require all denied`) : `app/`, `config/`, `vendor/`, `uploads/`, `logs/` sont dans le dossier public mais inaccessibles par URL directe — seul PHP peut les lire via `require`/`include`

##  Déploiement (hébergement mutualisé gratuit ou payant)

Ce projet est un PHP « zéro build » (pas de bundler, pas de `npm run build`), et **tout tient dans `htdocs/`** — conçu pour fonctionner tel quel sur n'importe quel hébergement mutualisé, y compris les offres gratuites (InfinityFree, 000webhost, ByetHost...) qui restreignent PHP au seul dossier public (`open_basedir`). Il suffit d'uploader le contenu de `htdocs/` dans le dossier public de l'hébergeur (souvent nommé `public_html`, parfois `htdocs` ou `www` — peu importe le nom, seul son *contenu* compte).

1. **Générer les dépendances de production en local** avant l'upload :
   ```bash
   composer install --no-dev --optimize-autoloader --working-dir=htdocs
   ```
2. **Uploader** le contenu de `htdocs/` (fichiers et sous-dossiers, y compris `app/`, `config/`, `vendor/` — mais **sans** `config/db.php` ni `.env`) vers le dossier public du serveur.
3. **Créer les fichiers de config directement sur le serveur** (jamais uploadés depuis le PC, jamais commités) :
   - `config/db.php` depuis `config/db.example.php`, avec `ENV = 'production'` et les identifiants MySQL fournis par l'hébergeur (host souvent `localhost` sur les hébergements mutualisés classiques, ou une adresse distante type `sqlXXX.hebergeur.com` sur certaines offres gratuites)
   - `.env` depuis `config/env.example.txt`, avec `APP_ENV=production`, `BASE_URL=/`, `RESET_PASSWORD_BASE_URL` pointant vers le domaine réel, et le bloc SMTP (Gmail ou Mailtrap live)
4. **Importer la base de données** via phpMyAdmin : `database/init.sql` + migrations, ou un export complet (`mysqldump`) si on part d'une base déjà peuplée
5. **Vérifier les permissions** en écriture sur `uploads/` et `logs/`
6. **Checklist post-déploiement** :
   - Charger la page d'accueil et se connecter avec un compte de test
   - Vérifier qu'une page nécessitant la base de données (ex. `login.php`) fonctionne correctement
   - Soumettre un rapport de test et vérifier la réception de l'e-mail de notification organisateur
   - Vérifier `logs/php_errors.log` (aucune erreur SMTP/DB)
   - Confirmer que `display_errors` est désactivé (`APP_ENV=production` le fait automatiquement dans `config/config.php`)

##  Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit les changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

##  Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

##  Auteur

**Khalil**
- Email : sibikhalil06@gmail.com
- GitHub : [@Blackkhalil](https://github.com/Blackkhalil)

---

**SEIC LBS** - Système de Gestion des Projets Étudiants
*Développé avec ❤️ pour l'innovation et l'entrepreneuriat*
