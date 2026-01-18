# Instructions pour ajouter le suivi des utilisateurs

## 📋 Description

Ce script ajoute le champ `id_utilisateur` aux tables `rapports`, `presentations` et `projets` pour permettre de savoir qui a soumis chaque élément.

## 🚀 Étapes d'installation

### 1. Exécuter le script SQL

1. Ouvrez **phpMyAdmin** (via WAMP → phpMyAdmin)
2. Sélectionnez la base de données `seic_lbs`
3. Cliquez sur l'onglet **SQL**
4. Copiez-collez le contenu du fichier `add_user_id_to_submissions.sql`
5. Cliquez sur **Exécuter**

### 2. Vérification

Après l'exécution, vérifiez que les colonnes ont été ajoutées :

```sql
SHOW COLUMNS FROM rapports LIKE 'id_utilisateur';
SHOW COLUMNS FROM presentations LIKE 'id_utilisateur';
SHOW COLUMNS FROM projets LIKE 'id_utilisateur';
```

Ces trois requêtes doivent retourner une ligne chacune.

## ✅ Résultat

Après l'installation :

- ✅ Les nouveaux rapports enregistreront automatiquement l'ID de l'utilisateur qui les a soumis
- ✅ Les nouvelles présentations enregistreront automatiquement l'ID de l'utilisateur qui les a soumises
- ✅ Les nouveaux projets enregistreront automatiquement l'ID de l'utilisateur qui les a créés
- ✅ Le suivi des soumissions fonctionnera directement par ID utilisateur (plus besoin de chercher dans les membres)
- ✅ Les pages admin afficheront le nom de la personne qui a soumis chaque rapport/présentation

## 🔄 Compatibilité

Le code est rétrocompatible : si les colonnes n'existent pas encore, le système utilisera l'ancienne méthode (recherche par nom dans les membres du projet).

## ⚠️ Note importante

Les rapports, présentations et projets créés **avant** l'exécution de ce script n'auront pas d'`id_utilisateur`. Seuls les nouveaux éléments auront cette information.
