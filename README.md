# SEIC LBS - Système de Gestion des Projets Étudiants

## 📋 Description

**SEIC LBS** est une application web complète développée en PHP pour la gestion des projets étudiants lors du Salon de l'Innovation et de l'Entrepreneuriat (SEIC) de l'École de Commerce LBS.

Cette application permet aux étudiants de soumettre leurs rapports et présentations PowerPoint, aux organisateurs de les valider ou refuser avec des commentaires, et offre un système de notifications en temps réel.

## ✨ Fonctionnalités Principales

### 👨‍🎓 Pour les Étudiants
- **Soumission de rapports PDF** : Upload sécurisé avec validation de format
- **Soumission de présentations PowerPoint** : Support des fichiers .pptx
- **Suivi des soumissions** : Tableau de bord personnel avec statuts de validation
- **Notifications en temps réel** : Alertes instantanées lors des changements de statut
- **Consultation des commentaires** : Retours détaillés des organisateurs

### 👨‍💼 Pour les Organisateurs
- **Gestion des rapports** : Validation/refus avec commentaires personnalisés
- **Gestion des présentations** : Interface d'administration complète
- **Statistiques détaillées** : Tableaux de bord avec métriques de soumission
- **Notifications automatiques** : Envoi d'emails aux étudiants lors des décisions
- **Gestion des projets** : Administration des projets et des équipes

### 🔧 Fonctionnalités Techniques
- **Authentification sécurisée** : Système de connexion avec rôles (étudiant/organisateur)
- **Upload sécurisé** : Validation des fichiers et protection contre les attaques
- **Base de données optimisée** : Requêtes performantes avec index
- **Interface responsive** : Design moderne avec Bootstrap
- **Multi-utilisateurs** : Support de plusieurs utilisateurs simultanés

## 🚀 Installation

### Prérequis
- **PHP 8.0+** avec extensions : PDO, MySQL, GD
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Serveur web** : Apache, Nginx, ou serveur de développement PHP
- **WAMP/XAMPP** (recommandé pour le développement local)

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone https://github.com/votre-username/seic-lbs.git
   cd seic-lbs
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL nommée `seic_lbs`
   - Importer le fichier `database/init.sql`
   - Exécuter les scripts d'optimisation dans `database/`

3. **Configurer l'application**
   - Modifier `config/config.php` avec vos paramètres de base de données
   - Ajuster les chemins dans `config/upload_config.php`

4. **Configurer les permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/rapports/
   chmod 755 uploads/presentations/
   ```

5. **Accéder à l'application**
   - Ouvrir `http://localhost/seic-lbs/public/` dans votre navigateur
   - Se connecter avec les identifiants par défaut

## 📁 Structure du Projet

```
seic-lbs/
├── app/                    # Logique métier (backend)
│   ├── auth.php           # Système d'authentification
│   ├── soumission_rapport.php    # Gestion soumission rapports
│   ├── soumission_powerpoint.php # Gestion soumission présentations
│   ├── rapports_admin.php  # Administration des rapports
│   ├── notification_manager.php   # Système de notifications
│   └── suivi_soumissions.php     # Suivi des soumissions
├── public/                 # Interface utilisateur (frontend)
│   ├── dashboard.php      # Tableau de bord principal
│   ├── soumettre_rapport.php     # Formulaire soumission rapport
│   ├── soumettre_powerpoint.php  # Formulaire soumission présentation
│   ├── suivi_soumissions.php     # Page de suivi étudiant
│   └── api/               # API REST pour notifications
├── config/                # Configuration
│   ├── config.php         # Configuration principale
│   └── upload_config.php  # Configuration des uploads
├── database/              # Scripts de base de données
│   ├── init.sql          # Structure initiale
│   └── optimization_tables.sql # Tables d'optimisation
├── uploads/              # Fichiers uploadés
│   ├── rapports/         # Rapports PDF
│   └── presentations/     # Présentations PowerPoint
└── README.md             # Ce fichier
```

## 🔧 Configuration

### Base de Données
Modifier `config/config.php` :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'seic_lbs');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

### Uploads
Modifier `config/upload_config.php` :
```php
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['pdf', 'pptx']);
```

## 👥 Utilisation

### Connexion
- **Étudiants** : Utilisent leurs identifiants pour accéder au système
- **Organisateurs** : Accès administrateur pour gérer les soumissions

### Soumission d'un Rapport
1. Se connecter en tant qu'étudiant
2. Aller dans "Soumettre un rapport"
3. Sélectionner le projet
4. Uploader le fichier PDF
5. Confirmer la soumission

### Validation par un Organisateur
1. Se connecter en tant qu'organisateur
2. Aller dans "Gestion des rapports"
3. Consulter les rapports en attente
4. Valider ou refuser avec commentaires
5. Les étudiants reçoivent une notification automatique

## 🔒 Sécurité

- **Validation des fichiers** : Vérification des extensions et types MIME
- **Protection contre l'injection SQL** : Requêtes préparées PDO
- **Gestion des sessions** : Sessions sécurisées avec régénération d'ID
- **Upload sécurisé** : Validation stricte et noms de fichiers uniques
- **Protection des dossiers** : Fichiers index.php pour empêcher l'accès direct

## 🚀 Déploiement

### Production
1. **Serveur web** : Apache/Nginx avec PHP-FPM
2. **Base de données** : MySQL/MariaDB avec optimisations
3. **SSL** : Certificat HTTPS obligatoire
4. **Sauvegarde** : Scripts automatiques de sauvegarde
5. **Monitoring** : Logs d'erreur et monitoring des performances

### Variables d'environnement
```bash
DB_HOST=localhost
DB_NAME=seic_lbs
DB_USER=production_user
DB_PASS=secure_password
BASE_URL=https://votre-domaine.com
```

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit les changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

## 📝 Changelog

### Version 1.0.0 (2025-01-20)
- ✅ Système de soumission de rapports PDF
- ✅ Système de soumission de présentations PowerPoint
- ✅ Interface d'administration pour organisateurs
- ✅ Système de notifications en temps réel
- ✅ Suivi des soumissions pour étudiants
- ✅ Interface responsive avec Bootstrap
- ✅ Optimisations pour multi-utilisateurs

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👨‍💻 Auteur

**Khalil**
- Email : sibikhalil06@gmail.com
- GitHub : [@Blackkhalil](https://github.com/Blackkhalil)

## 🙏 Remerciements

- École de Commerce LBS pour le support
- Communauté PHP pour les bonnes pratiques
- Bootstrap pour l'interface utilisateur
- Tous les contributeurs du projet

---

**SEIC LBS** - Système de Gestion des Projets Étudiants  
*Développé avec ❤️ pour l'innovation et l'entrepreneuriat*