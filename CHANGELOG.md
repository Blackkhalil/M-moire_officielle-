# 📝 Journal des modifications

## Version actuelle - Améliorations majeures

### ✅ Fonctionnalités ajoutées

1. **Choix du rôle lors de l'inscription**
   - Les utilisateurs peuvent choisir entre "Étudiant" et "Visiteur" lors de l'inscription
   - Validation du rôle sélectionné
   - Message de succès après inscription

2. **Système de commentaires pour les visiteurs**
   - Les visiteurs peuvent commenter les projets
   - Affichage des commentaires avec nom et date
   - Possibilité de supprimer ses propres commentaires
   - Compteur de commentaires par projet

3. **Affichage des commentaires pour tous les rôles**
   - **Organisateurs** : Voient tous les commentaires sur tous les projets
   - **Étudiants** : Voient uniquement les commentaires sur leurs propres projets
   - **Visiteurs** : Peuvent commenter et voir leurs commentaires

4. **Corrections du suivi des soumissions**
   - Méthode hybride robuste avec 3 systèmes de recherche
   - Fonctionne même sans colonne id_utilisateur
   - Recherche améliorée par nom (insensible à la casse)
   - Debug masqué pour la production

5. **Système de suivi par ID utilisateur**
   - Ajout de la colonne id_utilisateur aux tables rapports, presentations et projets
   - Script de mise à jour automatique de la base de données
   - Affichage du nom de l'utilisateur qui a soumis dans les pages admin

### 🔧 Fichiers modifiés

#### Backend (app/)
- `app/auth.php` - Fonction register() accepte le rôle
- `app/suivi_soumissions.php` - Méthode hybride pour récupérer les soumissions
- `app/soumission_rapport.php` - Enregistre id_utilisateur
- `app/soumission_powerpoint.php` - Enregistre id_utilisateur
- `app/soumission_projet.php` - Enregistre id_utilisateur
- `app/rapports_admin.php` - Affiche le nom de l'utilisateur qui a soumis
- `app/gestion_powerpoint.php` - Affiche le nom de l'utilisateur qui a soumis
- `app/projets.php` - Récupère les commentaires pour chaque projet
- `app/projets_edition.php` - Récupère les commentaires pour chaque projet
- `app/visitor_projects.php` - Intègre le système de commentaires
- `app/etudiant_projets.php` - Nouveau : Page pour étudiants avec filtrage par projets membres
- `app/commentaires.php` - Nouveau : Gestion des commentaires

#### Frontend (htdocs/)
- `htdocs/register.php` - Ajout du champ de sélection du rôle
- `htdocs/login.php` - Message de succès après inscription
- `htdocs/suivi_soumissions.php` - Debug masqué
- `htdocs/rapports_admin.php` - Colonne "Soumis par" ajoutée
- `htdocs/gestion_powerpoint.php` - Colonne "Soumis par" ajoutée
- `htdocs/visitor_projects.php` - Interface de commentaires intégrée
- `htdocs/projets.php` - Affichage des commentaires
- `htdocs/projets_edition.php` - Affichage des commentaires
- `htdocs/etudiant_projets.php` - Nouveau : Page pour étudiants
- `htdocs/dashboard.php` - Lien "Voir les projets" pour étudiants

#### Base de données (database/)
- `database/add_user_id_to_submissions.sql` - Script SQL pour ajouter id_utilisateur
- `database/add_commentaires_table.sql` - Script SQL pour créer la table commentaires
- `database/update_database.php` - Script PHP de mise à jour automatique

#### Documentation
- `SOLUTION_COMPLETE.md` - Guide complet de résolution
- `ACTION_IMMEDIATE.txt` - Guide rapide d'action
- `TEST_SUIVI_SOUMISSIONS.md` - Guide de test
- `FONCTIONNALITE_COMMENTAIRES.md` - Documentation des commentaires
- `CHANGELOG.md` - Ce fichier

### 🗑️ Fichiers supprimés
- `app/notification_manager.php` - Système de notifications retiré
- `htdocs/api/notifications.php` - API de notifications retirée

### 📋 Instructions pour Git

Pour commiter et pousser les modifications :

```bash
# Ajouter tous les fichiers modifiés
git add -A

# Créer un commit
git commit -m "Ajout choix rôle inscription + système commentaires + corrections suivi soumissions"

# Si vous avez un remote configuré
git push origin main
# ou
git push origin master
```

### ⚠️ Notes importantes

1. **Mise à jour de la base de données** : Exécutez `database/update_database.php` pour ajouter les nouvelles colonnes
2. **Les anciens rapports** : N'auront pas d'id_utilisateur mais seront trouvés par la méthode de fallback
3. **Les nouveaux utilisateurs** : Peuvent choisir leur rôle lors de l'inscription
