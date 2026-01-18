# 🔍 GUIDE DE TEST - Suivi des Soumissions

## ✅ VERSION SIMPLIFIÉE CRÉÉE

J'ai créé une version **SIMPLIFIÉE** et **FONCTIONNELLE** du code de suivi des soumissions.

## 🚀 ÉTAPES DE TEST

### 1. Accéder à la page avec le mode debug

1. Connectez-vous avec le compte "caleb" (ou n'importe quel étudiant)
2. Allez sur : `http://localhost/M-moire_officielle-/htdocs/suivi_soumissions.php?debug=1`
3. Vous verrez des informations de debug qui indiquent :
   - L'ID utilisateur
   - Le nom de l'utilisateur
   - Le nombre de rapports trouvés
   - Le nombre de présentations trouvées
   - Les méthodes utilisées pour trouver les rapports

### 2. Vérifier ce qui s'affiche

**Si vous voyez "0 rapports trouvés"**, cela signifie :
- Soit aucun rapport n'a été soumis par cet utilisateur
- Soit le nom de l'utilisateur ne correspond pas dans la liste des membres des projets

### 3. Diagnostic dans la base de données

Exécutez ces requêtes dans phpMyAdmin :

```sql
-- Vérifier l'utilisateur caleb
SELECT id, nom, email FROM utilisateurs WHERE email LIKE '%caleb%' OR nom LIKE '%caleb%';

-- Vérifier les rapports existants
SELECT r.*, p.nom as projet_nom, p.membres 
FROM rapports r 
JOIN projets p ON r.id_projet = p.id 
ORDER BY r.date_soumission DESC 
LIMIT 10;

-- Vérifier si id_utilisateur existe dans rapports
SHOW COLUMNS FROM rapports LIKE 'id_utilisateur';

-- Vérifier les rapports avec id_utilisateur (si la colonne existe)
SELECT r.*, u.nom as nom_utilisateur 
FROM rapports r 
LEFT JOIN utilisateurs u ON r.id_utilisateur = u.id 
ORDER BY r.date_soumission DESC;
```

### 4. Solutions selon le problème

#### Problème A : La colonne id_utilisateur n'existe pas
**Solution** : Exécutez le script de mise à jour
```
http://localhost/M-moire_officielle-/database/update_database.php
```

#### Problème B : Le nom ne correspond pas dans les membres
**Solution** : Vérifiez dans phpMyAdmin :
- Le nom exact dans `utilisateurs.nom`
- Comment il apparaît dans `projets.membres`
- Ils doivent correspondre EXACTEMENT (même casse, même espaces)

#### Problème C : Aucun rapport n'a été soumis
**Solution** : Soumettez un nouveau rapport et vérifiez qu'il apparaît

## 🔧 CODE SIMPLIFIÉ

Le nouveau code :
- ✅ Utilise 3 méthodes en cascade (si la première ne trouve rien, essaie la suivante)
- ✅ Affiche des messages de debug pour comprendre ce qui se passe
- ✅ Gère les erreurs sans planter
- ✅ Fonctionne même sans la colonne id_utilisateur

## 📝 TEST RAPIDE

1. **Connectez-vous** avec caleb
2. **Allez sur** : `suivi_soumissions.php?debug=1`
3. **Regardez** les informations de debug
4. **Envoyez-moi** ce que vous voyez dans le mode debug

Cela m'aidera à identifier le problème exact !
