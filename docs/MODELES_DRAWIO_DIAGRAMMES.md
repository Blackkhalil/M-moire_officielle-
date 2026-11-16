# 📐 Modèles Draw.io pour les Diagrammes UML - Plateforme SEIC LBS

Ce document fournit des modèles détaillés pour reproduire les diagrammes UML dans Draw.io. Chaque section décrit les éléments à créer, leurs positions, et les relations à dessiner.

---

## 📊 1. Diagramme de Classes

### Structure à créer dans Draw.io :

**Classes (Rectangles avec 3 sections) :**

1. **Utilisateur** (Position : Gauche, Haut)
   - Section 1 (Nom) : `Utilisateur`
   - Section 2 (Attributs) :
     ```
     + id : int
     + nom : string
     + email : string
     + mot_de_passe : string
     + role : string
     ```
   - Section 3 (Méthodes) : *(vide)*

2. **Projet** (Position : Centre, Haut)
   - Section 1 : `Projet`
   - Section 2 :
     ```
     + id : int
     + nom : string
     + description : string
     + categorie : string
     + membres : string
     + id_edition : int
     ```
   - Section 3 : *(vide)*

3. **Raport** (Position : Centre-Gauche, Milieu)
   - Section 1 : `Rapport`
   - Section 2 :
     ```
     + id : int
     + id_projet : int
     + id_utilisateur : int
     + date_soumission : datetime
     + fichier : string
     + etat_validation : string
     + commentaire_admin : string
     ```
   - Section 3 : *(vide)*

4. **Presentation** (Position : Centre-Droite, Milieu)
   - Section 1 : `Presentation`
   - Section 2 :
     ```
     + id : int
     + id_projet : int
     + id_utilisateur : int
     + date_soumission : datetime
     + fichier : string
     + etat_validation : string
     + commentaire_admin : string
     ```
   - Section 3 : *(vide)*

5. **Commentaire** (Position : Droite, Milieu)
   - Section 1 : `Commentaire`
   - Section 2 :
     ```
     + id : int
     + id_projet : int
     + auteur : string
     + contenu : string
     + date_creation : datetime
     ```
   - Section 3 : *(vide)*

6. **Edition** (Position : Gauche, Bas)
   - Section 1 : `Edition`
   - Section 2 :
     ```
     + id : int
     + annee : int
     + description : string
     + date_debut : date
     + date_fin : date
     ```
   - Section 3 : *(vide)*

7. **Groupe** (Position : Centre, Bas)
   - Section 1 : `Groupe`
   - Section 2 :
     ```
     + id : int
     + nom : string
     + id_edition : int
     + id_salle : int
     + nombre_membres : int
     ```
   - Section 3 : *(vide)*

8. **Salle** (Position : Droite, Bas)
   - Section 1 : `Salle`
   - Section 2 :
     ```
     + id : int
     + nom : string
     + localisation : string
     ```
   - Section 3 : *(vide)*

**Relations (Flèches avec labels) :**

1. **Utilisateur → Rapport**
   - Type : Flèche simple (→)
   - Label : `soumet`
   - Multiplicité côté Utilisateur : `1`
   - Multiplicité côté Rapport : `0..*`

2. **Utilisateur → Presentation**
   - Type : Flèche simple (→)
   - Label : `soumet`
   - Multiplicité côté Utilisateur : `1`
   - Multiplicité côté Presentation : `0..*`

3. **Projet → Rapport**
   - Type : Flèche simple (→)
   - Label : `possède`
   - Multiplicité côté Projet : `1`
   - Multiplicité côté Rapport : `0..*`

4. **Projet → Presentation**
   - Type : Flèche simple (→)
   - Label : `possède`
   - Multiplicité côté Projet : `1`
   - Multiplicité côté Presentation : `0..*`

5. **Projet → Commentaire**
   - Type : Flèche simple (→)
   - Label : `reçoit`
   - Multiplicité côté Projet : `1`
   - Multiplicité côté Commentaire : `0..*`

6. **Edition → Projet**
   - Type : Flèche simple (→)
   - Label : `contient`
   - Multiplicité côté Edition : `1`
   - Multiplicité côté Projet : `0..*`

7. **Edition → Groupe**
   - Type : Flèche simple (→)
   - Label : `contient`
   - Multiplicité côté Edition : `1`
   - Multiplicité côté Groupe : `0..*`

8. **Groupe → Projet**
   - Type : Flèche simple (→)
   - Label : `regroupe`
   - Multiplicité côté Groupe : `1`
   - Multiplicité côté Projet : `0..*`

9. **Groupe → Salle**
   - Type : Flèche simple (→)
   - Label : `se déroule dans`
   - Multiplicité côté Groupe : `1`
   - Multiplicité côté Salle : `1`

**Couleurs suggérées :**
- Classes principales : Fond blanc, bordure noire
- Relations : Lignes noires avec labels en italique

---

## 🔄 2. Diagramme de Séquence - Connexion Utilisateur

### Structure à créer dans Draw.io :

**Participants (Lignes de vie verticales, de haut en bas) :**

1. **Utilisateur** (Position : Extrême gauche)
   - Type : Acteur (icône bonhomme)
   - Label : `Utilisateur`

2. **Navigateur** (Position : Gauche)
   - Type : Rectangle avec ligne de vie
   - Label : `Navigateur`

3. **login.php** (Position : Centre-Gauche)
   - Type : Rectangle avec ligne de vie
   - Label : `login.php`

4. **auth.php** (Position : Centre)
   - Type : Rectangle avec ligne de vie
   - Label : `auth.php`

5. **Session PHP** (Position : Centre-Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `Session PHP`

6. **MySQL** (Position : Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `MySQL`

**Messages (Flèches horizontales) :**

**Phase 1 - Affichage du formulaire :**
1. `Utilisateur` → `Navigateur` : Flèche pleine → `Accéder à /login.php`
2. `Navigateur` → `login.php` : Flèche pleine → `GET /login.php`
3. `login.php` → `auth.php` : Flèche pleine → `require_once auth.php`
4. `auth.php` → `Session PHP` : Flèche pleine → `session_start()`
5. `auth.php` → `Session PHP` : Flèche pleine → `Vérifier si session existe`
6. `Session PHP` → `auth.php` : Flèche pointillée ← `Session vide`
7. `auth.php` → `login.php` : Flèche pointillée ← `is_logged_in() = false`
8. `login.php` → `Navigateur` : Flèche pleine → `Formulaire de connexion (HTML)`
9. `Navigateur` → `Utilisateur` : Flèche pleine → *(affichage)*

**Phase 2 - Soumission :**
10. `Utilisateur` → `Navigateur` : Flèche pleine → `Saisir email + mot de passe`
11. `Navigateur` → `login.php` : Flèche pleine → `POST /login.php`
12. `login.php` → `auth.php` : Flèche pleine → `login(email, mot_de_passe)`
13. `auth.php` → `MySQL` : Flèche pleine → `SELECT * FROM utilisateurs WHERE email = ?`
14. `MySQL` → `auth.php` : Flèche pointillée ← `{id, nom, email, hash, role}`
15. `auth.php` → `auth.php` : Flèche réflexive → `password_verify(mot_de_passe, hash)`

**Phase 3 - Alternative (Alt) :**
- **Boîte Alt** : Rectangle avec coin arrondi, label `alt Identifiants valides`
  - `auth.php` → `Session PHP` : `Créer $_SESSION[user_id, user_nom, user_role]`
  - `auth.php` → `login.php` : Flèche pointillée ← `true`
  - `login.php` → `Navigateur` : `HTTP 302 → dashboard.php`

- **Boîte Alt** : Rectangle avec coin arrondi, label `else Identifiants invalides`
  - `auth.php` → `login.php` : Flèche pointillée ← `false`
  - `login.php` → `Navigateur` : `Afficher "Email ou mot de passe incorrect"`

**Rectangles d'activation :**
- Sur chaque ligne de vie pendant les périodes d'activité (petits rectangles sur la ligne)

**Notes :**
- Utiliser des flèches pleines (→) pour les appels synchrones
- Utiliser des flèches pointillées (-->>) pour les retours
- Les lignes de vie doivent s'étendre verticalement sur toute la durée du scénario

---

## 🔄 3. Diagramme de Séquence - Soumission d'un Rapport

### Structure à créer dans Draw.io :

**Participants (de gauche à droite) :**

1. **Étudiant** (Position : Extrême gauche)
   - Type : Acteur
   - Label : `Étudiant`

2. **Navigateur** (Position : Gauche)
   - Type : Rectangle avec ligne de vie
   - Label : `Navigateur`

3. **soumettre_rapport.php** (Position : Centre-Gauche)
   - Type : Rectangle avec ligne de vie
   - Label : `soumettre_rapport.php`

4. **auth.php** (Position : Centre)
   - Type : Rectangle avec ligne de vie
   - Label : `auth.php`

5. **soumission_rapport.php** (Position : Centre)
   - Type : Rectangle avec ligne de vie
   - Label : `soumission_rapport.php`

6. **upload_config.php** (Position : Centre-Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `upload_config.php`

7. **uploads/rapports/** (Position : Centre-Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `uploads/rapports/`

8. **MySQL** (Position : Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `MySQL`

**Messages :**

**Phase 1 - Accès au formulaire :**
1. `Étudiant` → `Navigateur` : `Accéder à /soumettre_rapport.php`
2. `Navigateur` → `soumettre_rapport.php` : `GET /soumettre_rapport.php`
3. `soumettre_rapport.php` → `auth.php` : `Vérifier authentification`
4. `auth.php` → `soumettre_rapport.php` : Flèche pointillée ← `Utilisateur connecté`
5. `soumettre_rapport.php` → `MySQL` : `SELECT projets WHERE membres contient étudiant`
6. `MySQL` → `soumettre_rapport.php` : Flèche pointillée ← `Liste des projets de l'étudiant`
7. `soumettre_rapport.php` → `Navigateur` : `Formulaire de soumission (liste projets + upload)`
8. `Navigateur` → `Étudiant` : *(affichage)*

**Phase 2 - Soumission :**
9. `Étudiant` → `Navigateur` : `Choisir projet + fichier PDF`
10. `Navigateur` → `soumettre_rapport.php` : `POST /soumettre_rapport.php (multipart/form-data)`
11. `soumettre_rapport.php` → `soumission_rapport.php` : `Traiter soumission`
12. `soumission_rapport.php` → `upload_config.php` : `Lire limites d'upload (taille, extension)`
13. `upload_config.php` → `soumission_rapport.php` : Flèche pointillée ← `Paramètres d'upload`
14. `soumission_rapport.php` → `soumission_rapport.php` : Flèche réflexive → `Valider extension, type MIME, taille, erreurs`

**Phase 3 - Alternative :**
- **Boîte Alt "Fichier valide"** :
  - `soumission_rapport.php` → `uploads/rapports/` : `move_uploaded_file() vers /uploads/rapports/`
  - `uploads/rapports/` → `soumission_rapport.php` : Flèche pointillée ← `Fichier sauvegardé`
  - `soumission_rapport.php` → `MySQL` : `INSERT INTO rapports(...)`
  - `MySQL` → `soumission_rapport.php` : Flèche pointillée ← `ID rapport créé`
  - `soumission_rapport.php` → `soumettre_rapport.php` : Flèche pointillée ← `Succès`
  - `soumettre_rapport.php` → `Navigateur` : `Redirection → suivi_soumissions.php`
  - `Navigateur` → `Étudiant` : `Message "Rapport soumis avec succès"`

- **Boîte Alt "Fichier invalide"** :
  - `soumission_rapport.php` → `soumettre_rapport.php` : Flèche pointillée ← `Erreur de validation`
  - `soumettre_rapport.php` → `Navigateur` : `Afficher message d'erreur`
  - `Navigateur` → `Étudiant` : `Erreur visible`

---

## 🔄 4. Diagramme de Séquence - Consultation Projets et Commentaires

### Structure à créer dans Draw.io :

**Participants :**

1. **Visiteur** (Position : Extrême gauche)
   - Type : Acteur
   - Label : `Visiteur`

2. **Navigateur** (Position : Gauche)
   - Type : Rectangle avec ligne de vie
   - Label : `Navigateur`

3. **visitor_projects.php** (Position : Centre)
   - Type : Rectangle avec ligne de vie
   - Label : `visitor_projects.php`

4. **commentaires.php** (Position : Centre-Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `commentaires.php`

5. **MySQL** (Position : Droite)
   - Type : Rectangle avec ligne de vie
   - Label : `MySQL`

**Messages :**

**Phase 1 - Consultation :**
1. `Visiteur` → `Navigateur` : `Accéder à /visitor_projects.php`
2. `Navigateur` → `visitor_projects.php` : `GET /visitor_projects.php`
3. `visitor_projects.php` → `MySQL` : `SELECT projets publiés`
4. `MySQL` → `visitor_projects.php` : Flèche pointillée ← `Liste projets`

**Phase 2 - Boucle (Loop) :**
- **Boîte Loop** : Rectangle avec coin arrondi, label `loop Pour chaque projet`
  - `visitor_projects.php` → `commentaires.php` : `getCommentairesProjet(id_projet)`
  - `commentaires.php` → `MySQL` : `SELECT * FROM commentaires WHERE id_projet = ?`
  - `MySQL` → `commentaires.php` : Flèche pointillée ← `Liste commentaires`
  - `commentaires.php` → `visitor_projects.php` : Flèche pointillée ← `Commentaires du projet`

5. `visitor_projects.php` → `Navigateur` : `Afficher projets + commentaires`

**Phase 3 - Ajout de commentaire :**
6. `Visiteur` → `Navigateur` : `Saisir un nouveau commentaire`
7. `Navigateur` → `visitor_projects.php` : `POST commentaire`
8. `visitor_projects.php` → `commentaires.php` : `addCommentaire(id_projet, auteur, contenu)`
9. `commentaires.php` → `MySQL` : `INSERT INTO commentaires(...)`
10. `MySQL` → `commentaires.php` : Flèche pointillée ← `Succès`
11. `commentaires.php` → `visitor_projects.php` : Flèche pointillée ← `OK`
12. `visitor_projects.php` → `Navigateur` : `Rechargement / mise à jour de la liste des commentaires`

---

## 🔄 5. Diagramme d'Activités - Connexion Utilisateur

### Structure à créer dans Draw.io :

**Éléments à créer :**

1. **Nœud initial** (Position : Haut, Centre)
   - Type : Cercle plein noir
   - Label : *(aucun)*

2. **Activités** (Rectangles avec coins arrondis) :
   - `Ouvrir page login.php` (Position : Haut)
   - `Saisir email et mot de passe` (Position : Haut-Milieu)
   - `Cliquer sur 'Se connecter'` (Position : Milieu-Haut)
   - `Contrôler format des champs` (Position : Milieu)
   - `Afficher message d'erreur formulaire` (Position : Gauche, Milieu)
   - `Rechercher utilisateur par email en base` (Position : Milieu-Bas)
   - `Afficher 'Email ou mot de passe incorrect'` (Position : Gauche, Bas)
   - `Comparer mot de passe avec hash` (Position : Bas-Milieu)
   - `Créer session utilisateur` (Position : Bas)
   - `Redirection vers dashboard.php` (Position : Bas-Bas)
   - `Afficher tableau de bord` (Position : Bas-Bas-Bas)

3. **Nœud final** (Position : Bas, Centre)
   - Type : Cercle avec bordure épaisse
   - Label : *(aucun)*

4. **Décisions** (Losanges) :
   - `Format valide ?` (Position : Milieu, après "Contrôler format")
   - `Utilisateur trouvé ?` (Position : Milieu-Bas, après "Rechercher")
   - `Mot de passe correct ?` (Position : Bas-Milieu, après "Comparer")

**Flux (Flèches) :**

1. Nœud initial → `Ouvrir page login.php`
2. `Ouvrir page login.php` → `Saisir email et mot de passe`
3. `Saisir email et mot de passe` → `Cliquer sur 'Se connecter'`
4. `Cliquer sur 'Se connecter'` → `Contrôler format des champs`
5. `Contrôler format des champs` → Décision `Format valide ?`
   - **Non** → `Afficher message d'erreur formulaire` → `Saisir email et mot de passe` (retour)
   - **Oui** → `Rechercher utilisateur par email en base`
6. `Rechercher utilisateur` → Décision `Utilisateur trouvé ?`
   - **Non** → `Afficher 'Email ou mot de passe incorrect'` → `Saisir email et mot de passe` (retour)
   - **Oui** → `Comparer mot de passe avec hash`
7. `Comparer mot de passe` → Décision `Mot de passe correct ?`
   - **Non** → `Afficher 'Email ou mot de passe incorrect'` → `Saisir email et mot de passe` (retour)
   - **Oui** → `Créer session utilisateur`
8. `Créer session utilisateur` → `Redirection vers dashboard.php`
9. `Redirection vers dashboard.php` → `Afficher tableau de bord`
10. `Afficher tableau de bord` → Nœud final

**Labels sur les flèches :**
- Sur les flèches sortant des décisions : `Oui` / `Non` ou `Invalide` / `Valide`

---

## 🔄 6. Diagramme d'Activités - Soumission d'un Rapport

### Structure à créer dans Draw.io :

**Éléments à créer :**

1. **Nœud initial** (Position : Haut, Centre)

2. **Activités** :
   - `Étudiant connecté` (Position : Haut)
   - `Ouvrir page soumettre_rapport.php` (Position : Haut-Milieu)
   - `Sélectionner projet` (Position : Milieu-Haut)
   - `Choisir fichier PDF` (Position : Milieu)
   - `Cliquer sur 'Soumettre'` (Position : Milieu-Bas)
   - `Contrôler extension, taille, type MIME` (Position : Bas-Milieu)
   - `Afficher erreur de validation` (Position : Gauche, Bas)
   - `Enregistrer fichier dans uploads/rapports` (Position : Bas)
   - `Insérer enregistrement dans table rapports` (Position : Bas-Bas)
   - `Afficher message de succès` (Position : Bas-Bas-Bas)
   - `Redirection vers suivi_soumissions.php` (Position : Bas-Bas-Bas-Bas)

3. **Nœud final** (Position : Bas, Centre)

4. **Décision** :
   - `Fichier valide ?` (Position : Bas-Milieu, après "Contrôler")

**Flux :**

1. Nœud initial → `Étudiant connecté`
2. `Étudiant connecté` → `Ouvrir page soumettre_rapport.php`
3. `Ouvrir page soumettre_rapport.php` → `Sélectionner projet`
4. `Sélectionner projet` → `Choisir fichier PDF`
5. `Choisir fichier PDF` → `Cliquer sur 'Soumettre'`
6. `Cliquer sur 'Soumettre'` → `Contrôler extension, taille, type MIME`
7. `Contrôler` → Décision `Fichier valide ?`
   - **Non** → `Afficher erreur de validation` → `Choisir fichier PDF` (retour)
   - **Oui** → `Enregistrer fichier dans uploads/rapports`
8. `Enregistrer fichier` → `Insérer enregistrement dans table rapports`
9. `Insérer enregistrement` → `Afficher message de succès`
10. `Afficher message de succès` → `Redirection vers suivi_soumissions.php`
11. `Redirection` → Nœud final

---

## 🏗️ 7. Diagramme de Déploiement

### Structure à créer dans Draw.io :

**Nœuds (Rectangles avec coins arrondis) :**

1. **Client** (Position : Gauche, Centre)
   - Type : Rectangle avec sous-graphique
   - Contenu :
     - `Navigateur Web`
     - `Chrome / Firefox / Edge`

2. **Serveur Web (WAMP / Apache)** (Position : Centre, Centre)
   - Type : Rectangle avec sous-graphique
   - Contenu :
     - `Apache HTTP Server`
     - `PHP 8.x`
     - `Application SEIC LBS`
     - `(htdocs + app)`

3. **Serveur de Données** (Position : Droite, Centre)
   - Type : Rectangle avec sous-graphique
   - Contenu :
     - `MySQL / MariaDB`
     - `Système de fichiers`
     - `uploads/rapports, uploads/presentations`

**Connexions (Flèches) :**

1. `Client` → `Serveur Web` : Flèche → Label : `HTTP/HTTPS`
2. `Serveur Web` → `Serveur de Données` : Flèche → Label : `PDO MySQL`
3. `Serveur Web` → `Serveur de Données` : Flèche → Label : `Opérations fichiers`

**Sous-graphiques :**
- Utiliser des rectangles avec bordures pour créer des zones (Client, Serveur Web, Serveur de Données)
- Placer les composants à l'intérieur de chaque zone

**Couleurs suggérées :**
- Client : Fond bleu clair
- Serveur Web : Fond jaune clair
- Serveur de Données : Fond vert clair

---

## 📝 Instructions Générales pour Draw.io

### Conseils pour la création :

1. **Diagrammes de Séquence :**
   - Utiliser le modèle "UML Sequence" dans Draw.io
   - Les lignes de vie sont des lignes verticales pointillées
   - Les rectangles d'activation sont de petits rectangles sur les lignes de vie
   - Les messages sont des flèches horizontales
   - Les boîtes Alt/Loop sont des rectangles avec coins arrondis

2. **Diagrammes de Classes :**
   - Utiliser le modèle "UML Class" dans Draw.io
   - Les classes sont des rectangles avec 3 sections séparées
   - Les relations sont des flèches avec labels et multiplicités

3. **Diagrammes d'Activités :**
   - Utiliser le modèle "UML Activity" dans Draw.io
   - Les activités sont des rectangles avec coins arrondis
   - Les décisions sont des losanges
   - Les nœuds initiaux/finaux sont des cercles

4. **Diagramme de Déploiement :**
   - Utiliser des rectangles avec sous-graphiques
   - Les connexions sont des flèches simples avec labels

### Format d'export recommandé :
- **PNG** : Pour insertion dans documents Word/PDF
- **SVG** : Pour qualité vectorielle
- **PDF** : Pour documents finaux

---

**Auteur** : Khalil  
**Version** : 1.0  
**Date** : 2025
