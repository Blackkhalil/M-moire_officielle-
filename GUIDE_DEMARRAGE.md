# 🚀 Guide de Démarrage - SEIC LBS

## 📋 Prérequis Vérifiés ✅

Vous avez déjà :
- ✅ **WAMP installé** (dossier `c:\wamp64`)
- ✅ **PHP** (probablement version 8.x)
- ✅ **MySQL** (via WAMP)
- ✅ **Apache** (via WAMP)

---

## 🔧 Étape 1 : Démarrer WAMP

1. **Ouvrir WAMP** depuis le menu Démarrer Windows
2. **Attendre** que l'icône WAMP devienne **verte** dans la barre des tâches
3. **Cliquer gauche** sur l'icône WAMP → **"Start All Services"**
4. Vérifier que les services sont actifs (icône doit être verte)

> ⚠️ **Important** : Si l'icône est orange ou rouge, c'est qu'un port est déjà utilisé. Résolvez le problème avant de continuer.

---

## 🗄️ Étape 2 : Créer la Base de Données

### Option A : Via phpMyAdmin (RECOMMANDÉ)

1. **Cliquer gauche** sur l'icône WAMP → **"phpMyAdmin"**
2. Dans la barre latérale, **cliquer sur "Nouvelle base de données"**
3. Nom de la base : `seic_lbs`
4. **Cliquer sur "Créer"**
5. Une fois la base créée, **cliquer sur "Importer"** (onglet en haut)
6. **Parcourir** et sélectionner le fichier : `database/init.sql`
7. **Cliquer sur "Exécuter"** en bas de la page

### Option B : Via la ligne de commande MySQL

```bash
# Ouvrir l'invite de commande MySQL via WAMP
cd c:\wamp64\bin\mysql\mysql8.x.x\bin

# Se connecter à MySQL
mysql -u root -p

# Créer la base de données
CREATE DATABASE seic_lbs;

# Quitter MySQL
exit

# Importer le fichier init.sql
mysql -u root -p seic_lbs < C:\wamp64\www\M-moire_officielle--RP\database\init.sql
```

---

## 🔧 Étape 3 : Configuration (DÉJÀ FAIT ✅)

✅ La configuration est déjà mise à jour pour utiliser **localhost** avec :
- Host : `localhost`
- Database : `seic_lbs`
- User : `root`
- Password : (vide)

> 📝 Le fichier `config/config.php` a été configuré automatiquement.

---

## 🚀 Étape 4 : Lancer l'Application

1. **Ouvrir votre navigateur** (Chrome, Firefox, Edge...)
2. Aller à l'adresse : **`http://localhost/M-moire_officielle--RP/htdocs/index.php`**
   
   OU simplement :
   
   **`http://localhost/M-moire_officielle--RP/htdocs/`**

3. Vous devriez voir la **page d'accueil de SEIC LBS** !

---

## 👥 Étape 5 : Comptes de Test

### Connexion Étudiant
- **Email** : `alice@lbs.tg`
- **Mot de passe** : `test`
- **Rôle** : Étudiant

### Connexion Organisateur
- **Email** : `bob@lbs.tg`
- **Mot de passe** : `test`
- **Rôle** : Organisateur

### Connexion Visiteur
- **Email** : `visiteur@lbs.tg`
- **Mot de passe** : `test`
- **Rôle** : Visiteur

---

## 🎯 Fonctionnalités Disponibles

### Pour les Étudiants
- ✅ Soumettre un projet
- ✅ Déposer un rapport PDF
- ✅ Déposer une présentation PowerPoint
- ✅ Suivre l'état de vos soumissions
- ✅ Recevoir des notifications

### Pour les Organisateurs
- ✅ Gérer les éditions
- ✅ Gérer les projets
- ✅ Valider/refuser les rapports
- ✅ Gérer les groupes et salles
- ✅ Administrer les présentations

### Pour les Visiteurs
- ✅ Consulter les projets
- ✅ Voir les groupes et salles

---

## 🔍 Test de Fonctionnement

### Test 1 : Page d'Accueil
Aller sur : `http://localhost/M-moire_officielle--RP/htdocs/`

### Test 2 : Connexion
1. Cliquer sur **"Se connecter"**
2. Entrer : `alice@lbs.tg` / `test`
3. Vous devriez être redirigé vers le **Dashboard**

### Test 3 : Dashboard Responsive
1. Ouvrir les **Outils de développement** (F12)
2. Activer le **mode mobile** (Ctrl+Shift+M)
3. Vérifier que le dashboard s'adapte bien !

---

## ⚠️ Résolution de Problèmes

### Problème 1 : Page blanche
**Solution** : Vérifier que les **Extensions PHP** sont activées dans WAMP
1. Cliquer sur WAMP → **"PHP"** → **"Extensions"**
2. Vérifier que sont coché : `php_pdo_mysql`, `php_gd2`, `php_curl`

### Problème 2 : Erreur de connexion à la base de données
**Solution** : Vérifier que MySQL est démarré
1. Cliquer sur WAMP → **"Services"** → **"MySQL"** → **"Démarrer le service"**

### Problème 3 : Les fichiers uploadés ne s'enregistrent pas
**Solution** : Vérifier les permissions du dossier `uploads`
1. Clic droit sur `uploads` → **Propriétés** → **Sécurité**
2. Donner les permissions **Lecture/Écriture** à l'utilisateur Apache

### Problème 4 : Port 80 déjà utilisé
**Solution** : Changer le port Apache dans WAMP
1. Cliquer sur WAMP → **"Utiliser un port autre que 80"**
2. Utiliser le port **8080** par exemple
3. Accéder à : `http://localhost:8080/M-moire_officielle--RP/htdocs/`

---

## 📁 Structure des Fichiers

```
C:\wamp64\www\M-moire_officielle--RP\
├── htdocs/          ← Pages web principales (dossier à ouvrir dans le navigateur)
│   ├── index.php    ← Page d'accueil
│   ├── dashboard.php ← Tableau de bord
│   ├── login.php    ← Page de connexion
│   └── ...
├── app/             ← Logique backend (PHP)
├── config/          ← Configuration
├── database/        ← Scripts SQL
└── uploads/         ← Fichiers uploadés (PDF, PowerPoint)
```

---

## 🔄 Changer pour Production

Si vous voulez déployer en ligne avec InfinityFree, reprenez la configuration originale :

```php
// Dans config/config.php
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_NAME', 'if0_40166846_seic');
define('DB_USER', 'if0_40166846');
define('DB_PASS', 'LOS6JUhZJIM');
```

---

## 📝 Informations Utiles

- **Version PHP requise** : 8.0+
- **Base de données** : MySQL 5.7+
- **Framework** : Bootstrap 5.3
- **Icônes** : Font Awesome 6.4

---

## 🎓 Auteur

**Khalil**
- Email : sibikhalil06@gmail.com

---

**Bonne chance avec votre projet SEIC LBS ! 🚀**

