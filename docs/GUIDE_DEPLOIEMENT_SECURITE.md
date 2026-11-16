# 🔐 GUIDE DE DÉPLOIEMENT ET SÉCURITÉ - SEIC LBS

## ⚠️ ACTIONS CRITIQUES AVANT DÉPLOIEMENT

### 1. MIGRATION DES MOTS DE PASSE (URGENT)
Tous les utilisateurs avec mots de passe en texte clair doivent être migrés.

**Script SQL à exécuter :**
```sql
-- Migrer les mots de passe en texte clair
-- ATTENTION : Ce script détecte et hash automatiquement les mots de passe non-hashés
UPDATE utilisateurs 
SET mot_de_passe = CONCAT('$2y$12$', SHA2(CONCAT(mot_de_passe, RAND()), 256))
WHERE mot_de_passe NOT LIKE '$2y$%';
```

**⚠️ IMPORTANT : Après cette migration, les utilisateurs avec anciens mots de passe devront les réinitialiser !**

### 2. CONFIGURATION SÉCURISÉE

#### A. Générer une clé JWT sécurisée
```bash
php -r "echo bin2hex(random_bytes(32));"
```
Copiez le résultat dans `config/config.php` ligne 22

#### B. Modifier config/config.php
```php
// AVANT (INSECURE)
define('DB_PASS', '');
define('JWT_SECRET', 'votre_cle_secrete_unique');

// APRÈS (SECURE)
define('DB_PASS', 'MOT_DE_PASSE_FORT_ICI');
define('JWT_SECRET', 'VOTRE_CLE_GENEREE_64_CARACTERES');
```

#### C. Désactiver l'affichage des erreurs en production
Dans `config/config.php`, ajouter en haut :
```php
if ($_SERVER['HTTP_HOST'] !== 'localhost') {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

### 3. VÉRIFICATION DES FICHIERS .htaccess

Vérifiez que ces fichiers existent et bloquent l'accès :
- ✅ `config/.htaccess` (CRÉÉ)
- ✅ `app/.htaccess` (CRÉÉ)
- ✅ `uploads/.htaccess` (EXISTANT)
- ✅ `database/.htaccess` (À CRÉER)

**Créer database/.htaccess :**
```apache
Order Deny,Allow
Deny from all
```

### 4. CONFIGURATION APACHE/WAMP

#### A. Activer mod_rewrite
Dans httpd.conf :
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

#### B. Permettre .htaccess
```apache
<Directory "C:/wamp64/www/">
    AllowOverride All
    Require all granted
</Directory>
```

#### C. Redémarrer Apache
```bash
# Dans WAMP
Clic droit sur icône WAMP > Restart All Services
```

### 5. CONFIGURATION BASE DE DONNÉES

#### A. Créer un utilisateur dédié (recommandé)
```sql
-- Se connecter en root
mysql -u root -p

-- Créer utilisateur dédié
CREATE USER 'seic_user'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_FORT';
GRANT ALL PRIVILEGES ON seic_lbs.* TO 'seic_user'@'localhost';
FLUSH PRIVILEGES;
```

#### B. Importer la base de données
```bash
mysql -u root -p seic_lbs < database/init.sql
mysql -u root -p seic_lbs < database/add_commentaires_table.sql
mysql -u root -p seic_lbs < database/add_presentations_table.sql
mysql -u root -p seic_lbs < database/add_user_id_to_submissions.sql
mysql -u root -p seic_lbs < database/optimization_tables.sql
```

### 6. PERMISSIONS FICHIERS (WINDOWS)

Dans PowerShell (Admin) :
```powershell
# Accès en écriture pour uploads
icacls "C:\wamp64\www\M-moire_officielle-\uploads" /grant Users:F /T
```

### 7. TESTER LE DÉPLOIEMENT

#### A. Vérifier les protections
Testez ces URLs (doivent retourner 403 Forbidden) :
- http://localhost/M-moire_officielle-/config/config.php
- http://localhost/M-moire_officielle-/app/auth.php
- http://localhost/M-moire_officielle-/database/init.sql

#### B. Tester la connexion
1. Accéder à http://localhost/M-moire_officielle-/htdocs/
2. Créer un nouveau compte (le mot de passe doit avoir 8 caractères minimum maintenant)
3. Se connecter avec ce compte
4. Vérifier que la session fonctionne

#### C. Tester l'upload
1. Soumettre un rapport PDF
2. Vérifier que le fichier est uploadé dans uploads/rapports/
3. Vérifier qu'on ne peut pas y accéder directement via URL

### 8. CHECKLIST PRÉ-DÉPLOIEMENT

- [ ] Clé JWT changée et sécurisée (64+ caractères)
- [ ] Mot de passe BDD configuré (non vide)
- [ ] Tous les .htaccess créés (config/, app/, database/)
- [ ] Affichage erreurs désactivé en production
- [ ] Logs de debug supprimés (auth.php lignes 30, 37-43, 51-52, 56-59, 68)
- [ ] Base de données importée et à jour
- [ ] Utilisateur BDD dédié créé (recommandé)
- [ ] Permissions uploads/ configurées
- [ ] Tests de connexion OK
- [ ] Tests upload OK
- [ ] Tests de protection .htaccess OK

### 9. AMÉLIORATIONS RECOMMANDÉES (POST-DÉPLOIEMENT)

#### Priorité HAUTE
- [ ] Implémenter tokens CSRF dans tous les formulaires
- [ ] Renforcer validation MIME des fichiers uploadés
- [ ] Ajouter rate limiting sur login (max 5 tentatives/5min)
- [ ] Implémenter contrôle d'accès propriétaire pour downloads
- [ ] Forcer HTTPS en production

#### Priorité MOYENNE
- [ ] Système de réinitialisation de mot de passe par email
- [ ] Logging des actions critiques (connexion, upload, validation)
- [ ] Système de notifications par email
- [ ] Compression des uploads volumineux
- [ ] Preview PDF dans le navigateur

#### Priorité BASSE
- [ ] Migration vers framework (Laravel/Symfony)
- [ ] API RESTful complète
- [ ] Tests unitaires et d'intégration
- [ ] Dockerisation
- [ ] CI/CD avec GitHub Actions

### 10. EN CAS DE PROBLÈME

#### Erreur "Access Denied" lors connexion BDD
```php
// Vérifier config/config.php
define('DB_PASS', ''); // Mettre le mot de passe root WAMP
```

#### Erreur ".htaccess" non pris en compte
```apache
# Vérifier httpd.conf
AllowOverride All  # (pas None)
```

#### Session perdue après quelques minutes
```php
// Vérifier php.ini
session.gc_maxlifetime = 3600
```

#### Upload ne fonctionne pas
```ini
# Vérifier php.ini
upload_max_filesize = 50M
post_max_size = 50M
```

### 11. CONTACT ET SUPPORT

**Auteur** : Khalil  
**Email** : sibikhalil06@gmail.com  
**GitHub** : @Blackkhalil

---

## 📊 RÉSUMÉ DES AMÉLIORATIONS APPLIQUÉES

### ✅ Corrections Critiques
1. **Sécurité Authentification**
   - ❌ Suppression acceptation mots de passe texte clair
   - ❌ Suppression logs de mots de passe
   - ✅ Minimum 8 caractères pour mot de passe
   - ✅ Bcrypt avec cost 12
   - ✅ Rate limiting (sleep 1s sur échec)
   - ✅ Régénération session ID à la connexion
   - ✅ Timeout session après 1h d'inactivité

2. **Protection Fichiers Sensibles**
   - ✅ .htaccess créé dans config/
   - ✅ .htaccess créé dans app/
   - 📝 .htaccess à créer dans database/

3. **Sécurité Sessions**
   - ✅ cookie_httponly activé
   - ✅ cookie_samesite = Strict
   - ✅ cookie_secure si HTTPS
   - ✅ Vérification timeout session
   - ✅ Tracking IP utilisateur

4. **Fonctions CSRF**
   - ✅ Fonction generate_csrf_token() ajoutée
   - ✅ Fonction verify_csrf_token() ajoutée
   - 📝 À implémenter dans les formulaires

### ⏳ Améliorations à Faire Manuellement

1. **Ajouter tokens CSRF dans les formulaires** (voir section suivante)
2. **Renforcer validation MIME** (voir section suivante)
3. **Configurer les credentials** (JWT_SECRET, DB_PASS)
4. **Migrer les mots de passe existants**

---

## 🚀 PRÊT POUR LE TEST

Votre application est maintenant **plus sécurisée** mais nécessite encore quelques configurations manuelles avant la production.

Pour tester localement :
```bash
# 1. Accéder à l'application
http://localhost/M-moire_officielle-/htdocs/

# 2. Créer un compte test (minimum 8 caractères)
# 3. Tester les fonctionnalités
```
