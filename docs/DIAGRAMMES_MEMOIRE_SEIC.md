## Chapitre X – Diagrammes UML de la plateforme SEIC LBS

Ce document regroupe les principaux diagrammes UML utilisés dans le mémoire pour décrire la plateforme SEIC LBS : diagramme de classes, diagrammes de séquence, diagrammes d’activités et diagramme de déploiement.

---

## 1. Diagramme de classes

Le diagramme de classes ci‑dessous présente la structure statique du système SEIC LBS.  
Il met en évidence les principales entités (utilisateurs, projets, rapports, présentations, commentaires, etc.), leurs attributs ainsi que les relations entre elles (soumission, appartenance à une édition, regroupement en groupes, utilisation de salles).

```mermaid
classDiagram
    class Utilisateur {
        +int id
        +string nom
        +string email
        +string mot_de_passe
        +string role  // etudiant, visiteur, organisateur
    }

    class Projet {
        +int id
        +string nom
        +string description
        +string categorie
        +string membres
        +int id_edition
    }

    class Rapport {
        +int id
        +int id_projet
        +int id_utilisateur
        +datetime date_soumission
        +string fichier
        +string etat_validation
        +string commentaire_admin
    }

    class Presentation {
        +int id
        +int id_projet
        +int id_utilisateur
        +datetime date_soumission
        +string fichier
        +string etat_validation
        +string commentaire_admin
    }

    class Edition {
        +int id
        +int annee
        +string description
        +date date_debut
        +date date_fin
    }

    class Groupe {
        +int id
        +string nom
        +int id_edition
        +int id_salle
        +int nombre_membres
    }

    class Salle {
        +int id
        +string nom
        +string localisation
    }

    class Commentaire {
        +int id
        +int id_projet
        +string auteur
        +string contenu
        +datetime date_creation
    }

    Utilisateur "1" --> "0..*" Rapport : soumet
    Utilisateur "1" --> "0..*" Presentation : soumet
    Projet "1" --> "0..*" Rapport : possede
    Projet "1" --> "0..*" Presentation : possede
    Projet "1" --> "0..*" Commentaire : recoit
    Edition "1" --> "0..*" Projet : contient
    Edition "1" --> "0..*" Groupe : contient
    Groupe "1" --> "0..*" Projet : regroupe
    Groupe "1" --> "1" Salle : se_deroule_dans
```

**Figure X.1 : Diagramme de classes du système SEIC LBS**

---

## 2. Diagrammes de séquence

### 2.1 Diagramme de séquence – Connexion d’un utilisateur

Ce diagramme de séquence illustre le scénario de connexion d’un utilisateur à la plateforme SEIC LBS.  
Il montre les interactions entre l’acteur « Utilisateur » et les objets du système (navigateur, page `login.php`, module d’authentification, session, base de données) depuis l’affichage du formulaire de connexion jusqu’à l’accès au tableau de bord ou l’affichage d’un message d’erreur.

```mermaid
sequenceDiagram
    participant U as Utilisateur
    participant B as Navigateur
    participant L as login.php
    participant A as auth.php
    participant S as Session PHP
    participant D as MySQL

    U->>B: Accéder à /login.php
    B->>L: GET /login.php
    L->>A: require_once auth.php
    A->>S: session_start()
    A->>S: Vérifier si session existe
    S-->>A: Session vide
    A-->>L: is_logged_in() = false
    L->>B: Formulaire de connexion (HTML)

    U->>B: Saisir email + mot de passe
    B->>L: POST /login.php
    L->>A: login(email, mot_de_passe)
    A->>D: SELECT * FROM utilisateurs WHERE email = ?
    D-->>A: {id, nom, email, hash, role}
    A->>A: password_verify(mot_de_passe, hash)

    alt Identifiants valides
        A->>S: Créer $_SESSION[user_id, user_nom, user_role]
        A-->>L: true
        L->>B: HTTP 302 → dashboard.php
    else Identifiants invalides
        A-->>L: false
        L->>B: Afficher "Email ou mot de passe incorrect"
    end
```

**Figure X.2 : Diagramme de séquence – Connexion d’un utilisateur**

---

### 2.2 Diagramme de séquence – Soumission d’un rapport

Ce diagramme de séquence décrit le scénario de soumission d’un rapport PDF par un étudiant.  
Il détaille l’enchaînement des messages entre l’étudiant, l’interface web, la logique métier de soumission, la configuration d’upload, le système de fichiers et la base de données, depuis le choix du projet et du fichier jusqu’à la confirmation de succès ou le message d’erreur.

```mermaid
sequenceDiagram
    participant E as Étudiant
    participant B as Navigateur
    participant F as soumettre_rapport.php
    participant A as auth.php
    participant Back as soumission_rapport.php
    participant C as upload_config.php
    participant FS as uploads/rapports/
    participant DB as MySQL

    E->>B: Accéder à /soumettre_rapport.php
    B->>F: GET /soumettre_rapport.php
    F->>A: Vérifier authentification
    A-->>F: Utilisateur connecté
    F->>DB: SELECT projets WHERE membres contient étudiant
    DB-->>F: Liste des projets de l'étudiant
    F->>B: Formulaire de soumission (liste projets + upload)

    E->>B: Choisir projet + fichier PDF
    B->>F: POST /soumettre_rapport.php (multipart/form-data)
    F->>Back: Traiter soumission

    Back->>C: Lire limites d’upload (taille, extension)
    C-->>Back: Paramètres d’upload
    Back->>Back: Valider extension, type MIME, taille, erreurs

    alt Fichier valide
        Back->>FS: move_uploaded_file() vers /uploads/rapports/
        FS-->>Back: Fichier sauvegardé
        Back->>DB: INSERT INTO rapports(...)
        DB-->>Back: ID rapport créé
        Back-->>F: Succès
        F->>B: Redirection → suivi_soumissions.php
        B->>E: Message "Rapport soumis avec succès"
    else Fichier invalide
        Back-->>F: Erreur de validation
        F->>B: Afficher message d’erreur
        B->>E: Erreur visible
    end
```

**Figure X.3 : Diagramme de séquence – Soumission d’un rapport**

---

### 2.3 Diagramme de séquence – Consultation des projets et commentaires (visiteur)

Ce diagramme de séquence représente la consultation des projets par un visiteur ainsi que l’ajout de commentaires.  
Il met en évidence l’interaction entre l’acteur « Visiteur » et les composants `visitor_projects.php`, le module de commentaires et la base de données pour l’affichage et l’enregistrement des commentaires.

```mermaid
sequenceDiagram
    participant V as Visiteur
    participant B as Navigateur
    participant P as visitor_projects.php
    participant C as commentaires.php
    participant DB as MySQL

    V->>B: Accéder à /visitor_projects.php
    B->>P: GET /visitor_projects.php
    P->>DB: SELECT projets publiés
    DB-->>P: Liste projets
    loop Pour chaque projet
        P->>C: getCommentairesProjet(id_projet)
        C->>DB: SELECT * FROM commentaires WHERE id_projet = ?
        DB-->>C: Liste commentaires
        C-->>P: Commentaires du projet
    end
    P->>B: Afficher projets + commentaires

    V->>B: Saisir un nouveau commentaire
    B->>P: POST commentaire
    P->>C: addCommentaire(id_projet, auteur, contenu)
    C->>DB: INSERT INTO commentaires(...)
    DB-->>C: Succès
    C-->>P: OK
    P->>B: Rechargement / mise à jour de la liste des commentaires
```

**Figure X.4 : Diagramme de séquence – Consultation des projets et commentaires**

---

## 3. Diagrammes d’activités

### 3.1 Activité : Connexion d’un utilisateur

Le diagramme d’activités suivant modélise le déroulement du processus de connexion d’un utilisateur.  
Il décrit les différentes étapes : saisie des identifiants, contrôle de validité, vérification en base de données, création de la session et redirection vers le tableau de bord en cas de succès.

```mermaid
flowchart TD
    A[Début] --> B[Ouvrir page login.php]
    B --> C[Saisir email et mot de passe]
    C --> D[Cliquer sur 'Se connecter']
    D --> E[Contrôler format des champs]
    E -->|Invalide| F[Afficher message d'erreur formulaire]
    F --> C
    E -->|Valide| G[Rechercher utilisateur par email en base]
    G -->|Non trouvé| H[Afficher 'Email ou mot de passe incorrect']
    H --> C
    G -->|Trouvé| I[Comparer mot de passe avec hash]
    I -->|Incorrect| H
    I -->|Correct| J[Créer session utilisateur]
    J --> K[Redirection vers dashboard.php]
    K --> L[Afficher tableau de bord]
    L --> M[Fin]
```

**Figure X.5 : Diagramme d’activités – Connexion d’un utilisateur**

---

### 3.2 Activité : Soumission d’un rapport

Ce diagramme d’activités représente le processus de soumission d’un rapport par un étudiant.  
Il montre les principales étapes du flux : sélection du projet, choix du fichier, vérifications techniques (taille, format), enregistrement du fichier et insertion de l’enregistrement dans la table des rapports.

```mermaid
flowchart TD
    A[Début] --> B[Étudiant connecté]
    B --> C[Ouvrir page soumettre_rapport.php]
    C --> D[Sélectionner projet]
    D --> E[Choisir fichier PDF]
    E --> F[Cliquer sur 'Soumettre']
    F --> G[Contrôler extension, taille, type MIME]
    G -->|Invalide| H[Afficher erreur de validation]
    H --> E
    G -->|Valide| I[Enregistrer fichier dans uploads/rapports]
    I --> J[Insérer enregistrement dans table rapports]
    J --> K[Afficher message de succès]
    K --> L[Redirection vers suivi_soumissions.php]
    L --> M[Fin]
```

**Figure X.6 : Diagramme d’activités – Soumission d’un rapport**

---

## 4. Diagramme de déploiement

Le diagramme de déploiement ci‑dessous présente l’architecture physique de la solution SEIC LBS.  
Il distingue le poste client (navigateur web), le serveur web (Apache/PHP hébergeant l’application) et le serveur de données (MySQL/MariaDB et système de fichiers pour le stockage des rapports et présentations).

```mermaid
graph TD
    subgraph Client
        NAV[Navigateur Web<br/>Chrome / Firefox / Edge]
    end

    subgraph Serveur_Web["Serveur Web (WAMP / Apache)"]
        AP[Apache HTTP Server]
        PHP[PHP 8.x]
        APP[Application SEIC LBS<br/>(htdocs + app)]
    end

    subgraph Donnees["Serveur de Données"]
        DB[(MySQL / MariaDB)]
        FS[(Système de fichiers<br/>uploads/rapports, uploads/presentations)]
    end

    NAV -->|HTTP/HTTPS| AP
    AP --> PHP
    PHP --> APP
    APP --> DB
    APP --> FS
```

**Figure X.7 : Diagramme de déploiement de la plateforme SEIC LBS**

