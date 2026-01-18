# 💬 Fonctionnalité de Commentaires pour les Visiteurs

## ✅ Ce qui a été ajouté

### 1. **Table de base de données**
- Table `commentaires` créée automatiquement
- Liée aux projets et aux utilisateurs
- Suppression en cascade si projet ou utilisateur supprimé

### 2. **Système de commentaires**
- Les visiteurs peuvent commenter les projets
- Affichage des commentaires avec nom et date
- Possibilité de supprimer ses propres commentaires
- Interface collapsible (peut être masquée/affichée)

### 3. **Fonctionnalités**
- ✅ Ajouter un commentaire sur un projet
- ✅ Voir tous les commentaires d'un projet
- ✅ Supprimer ses propres commentaires
- ✅ Compteur de commentaires par projet
- ✅ Interface moderne et responsive

## 🚀 Utilisation

### Pour les visiteurs :

1. **Se connecter** en tant que visiteur
2. **Aller sur** "Voir les projets"
3. **Sélectionner une édition**
4. **Cliquer sur** "Voir/Commenter" sur n'importe quel projet
5. **Ajouter un commentaire** dans le champ texte
6. **Cliquer sur** "Publier"

### Fonctionnalités disponibles :

- **Voir les commentaires** : Cliquez sur "Voir/Commenter" pour voir tous les commentaires
- **Ajouter un commentaire** : Remplissez le formulaire et cliquez sur "Publier"
- **Supprimer un commentaire** : Cliquez sur l'icône poubelle à côté de votre commentaire

## 🔧 Installation

La table est créée automatiquement lors de la première utilisation, ou vous pouvez :

1. **Via le script de mise à jour** :
   ```
   http://localhost/M-moire_officielle-/database/update_database.php
   ```

2. **Via phpMyAdmin** :
   - Exécutez le fichier `database/add_commentaires_table.sql`

## 📝 Structure de la table

```sql
commentaires
├── id (INT, PRIMARY KEY)
├── id_projet (INT, FOREIGN KEY → projets)
├── id_utilisateur (INT, FOREIGN KEY → utilisateurs)
├── commentaire (TEXT)
└── date_creation (DATETIME)
```

## 🎨 Interface

- **Compteur** : Affiche le nombre de commentaires par projet
- **Bouton collapsible** : Permet de masquer/afficher la section commentaires
- **Formulaire intégré** : Ajout de commentaire directement sur la carte du projet
- **Liste des commentaires** : Affichage avec nom, date et possibilité de suppression

## ⚠️ Limitations

- Maximum 1000 caractères par commentaire
- Seuls les visiteurs peuvent commenter (pour l'instant)
- Un utilisateur ne peut supprimer que ses propres commentaires
