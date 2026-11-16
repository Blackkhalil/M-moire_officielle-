# Diagrammes UML — SEIC LBS

**Fichier Draw.io (contexte + global + détaillés) :** [`diagrammes/SEIC_Cas_Utilisation.drawio`](diagrammes/SEIC_Cas_Utilisation.drawio)

Ouvrir avec [diagrams.net](https://app.diagrams.net) ou l’extension Draw.io intégrée à VS Code / Cursor.

**Version :** 3.0 — Juillet 2026  
**Acteurs :** Étudiant, Organisateur (administrateur du site), Visiteur (non connecté ou inscrit)

> **Règle métier :** il n'existe pas de rôle `admin` séparé. L'**organisateur** cumule toutes les fonctions d'administration.

---

## 1. Diagramme de contexte

```mermaid
flowchart LR
    subgraph Acteurs
        E[Étudiant]
        O[Organisateur]
        V[Visiteur]
    end

    SEIC[(Plateforme SEIC LBS)]

    E -->|Soumet projets, rapports, PPT| SEIC
    E -->|Consulte ses soumissions| SEIC
    O -->|Valide, organise, administre| SEIC
    V -->|Consulte le site| SEIC

    SEIC --> MySQL[(MySQL seic_lbs)]
    SEIC --> FS[Dossier uploads]
    SEIC --> Mail[Email notifications]
```

---

## 2. Diagramme global des cas d'utilisation

```mermaid
flowchart TB
    subgraph Commun
        UC00[UC00 Se connecter]
        UC01[UC01 S'inscrire]
        UC02[UC02 Se déconnecter]
    end

    subgraph Organisateur
        UC10[UC10 Gérer les éditions]
        UC11[UC11 Gérer les projets]
        UC12[UC12 Valider les rapports]
        UC13[UC13 Valider les présentations]
        UC14[UC14 Gérer groupes et salles]
        UC15[UC15 Consulter projets par édition]
        UC16[UC16 Importer PDF]
        UC17[UC17 Exporter une édition ZIP]
    end

    subgraph Étudiant
        UC20[UC20 Soumettre un projet]
        UC21[UC21 Soumettre un rapport PDF]
        UC22[UC22 Soumettre une présentation]
        UC23[UC23 Suivre ses soumissions]
        UC24[UC24 Consulter les projets]
    end

    subgraph Visiteur
        UC30[UC30 Consulter l'accueil]
        UC31[UC31 Consulter les projets publics]
        UC32[UC32 Laisser un commentaire]
    end

    O((Organisateur)) --- UC10 & UC11 & UC12 & UC13 & UC14 & UC15 & UC16 & UC17
    E((Étudiant)) --- UC20 & UC21 & UC22 & UC23 & UC24
    V((Visiteur)) --- UC30 & UC31 & UC32
    O & E & V --- UC00 & UC01 & UC02
```

---

## 3. Cas d'utilisation détaillés

### 3.1 Organisateur

| ID | Cas d'utilisation | Préconditions | Postconditions |
|----|-------------------|---------------|----------------|
| UC10 | Gérer les éditions | Connecté, rôle organisateur | Édition créée/modifiée/supprimée |
| UC11 | Gérer les projets | Connecté, rôle organisateur | Projet enregistré en BDD |
| UC12 | Valider les rapports | Rapport en attente | État `valide` ou `refuse` + commentaire |
| UC13 | Valider les présentations | Présentation en attente | État mis à jour |
| UC14 | Gérer groupes et salles | Connecté, rôle organisateur | Attribution groupe/salle |
| UC15 | Projets par édition | Édition existante | Liste filtrée affichée |
| UC16 | Importer PDF | Fichier PDF valide | Rapport enregistré |
| UC17 | Exporter ZIP | Édition sélectionnée | Archive téléchargée |

### 3.2 Étudiant

| ID | Cas d'utilisation | Préconditions | Postconditions |
|----|-------------------|---------------|----------------|
| UC20 | Soumettre un projet | Connecté, rôle étudiant | Projet créé |
| UC21 | Soumettre un rapport | Projet existant, PDF | Rapport `en_attente` |
| UC22 | Soumettre présentation | Projet existant, PPT | Présentation `en_attente` |
| UC23 | Suivi soumissions | Au moins une soumission | Statuts affichés |
| UC24 | Consulter projets | Connecté | Liste + commentaires visibles |

### 3.3 Visiteur

| ID | Cas d'utilisation | Préconditions | Postconditions |
|----|-------------------|---------------|----------------|
| UC30 | Consulter l'accueil | Aucune | Page index affichée |
| UC31 | Consulter projets | Compte visiteur ou accès public | Projets listés |
| UC32 | Laisser commentaire | Connecté visiteur, projet ouvert | Commentaire en BDD |

### 3.4 Commun

| ID | Cas d'utilisation | Acteurs | Résultat |
|----|-------------------|---------|----------|
| UC00 | Se connecter | Tous | Session ouverte, redirection dashboard |
| UC01 | S'inscrire | Étudiant, Visiteur | Compte créé, hash bcrypt |
| UC02 | Se déconnecter | Tous connectés | Session détruite |

---

## 4. Diagrammes de séquence

### UC00 — Se connecter

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant L as login.php
    participant A as auth.php
    participant DB as MySQL

    U->>L: POST email + mot de passe
    L->>L: verify_csrf_token()
    L->>A: login(email, password)
    A->>DB: SELECT utilisateurs WHERE email
    DB-->>A: utilisateur + hash
    A->>A: password_verify()
    A->>A: session_regenerate_id()
    A-->>L: true
    L->>U: redirect dashboard.php
```

### UC12 — Valider un rapport (Organisateur)

```mermaid
sequenceDiagram
    actor O as Organisateur
    participant R as rapports_admin.php
    participant DB as MySQL

    O->>R: Ouvrir page gestion rapports
    R->>R: is_organisateur()
    R->>DB: SELECT rapports JOIN projets
    DB-->>R: liste rapports
    O->>R: POST valider/refuser + commentaire
    R->>DB: UPDATE rapports SET etat_validation
    DB-->>R: OK
    R-->>O: Confirmation affichée
```

### UC21 — Soumettre un rapport (Étudiant)

```mermaid
sequenceDiagram
    actor E as Étudiant
    participant S as soumission_rapport.php
    participant FS as uploads/rapports
    participant DB as MySQL

    E->>S: POST formulaire + fichier PDF
    S->>S: is_logged_in() + rôle étudiant
    S->>S: Validation type/taille fichier
    S->>FS: move_uploaded_file()
    S->>DB: INSERT INTO rapports
    DB-->>S: id rapport
    S-->>E: Message succès
```

### UC32 — Laisser un commentaire (Visiteur inscrit)

```mermaid
sequenceDiagram
    actor V as Visiteur
    participant P as visitor_projects.php
    participant C as commentaires.php
    participant DB as MySQL

    V->>P: Consulter projets édition
    V->>C: POST commentaire
    C->>DB: INSERT INTO commentaires
    DB-->>C: OK
    C-->>V: Redirection avec succès
```

---

## 5. Diagrammes d'activité

### UC00 — Connexion

```mermaid
flowchart TD
    A[Début] --> B[Afficher formulaire]
    B --> C{POST reçu ?}
    C -->|Non| B
    C -->|Oui| D{CSRF valide ?}
    D -->|Non| E[Erreur session]
    D -->|Oui| F[Rechercher utilisateur]
    F --> G{Trouvé ?}
    G -->|Non| H[Erreur identifiants]
    G -->|Oui| I{password_verify ?}
    I -->|Non| H
    I -->|Oui| J[Créer session]
    J --> K[Rediriger dashboard]
    E --> B
    H --> B
```

### UC12 — Validation rapport

```mermaid
flowchart TD
    A[Début] --> B{Organisateur connecté ?}
    B -->|Non| C[Rediriger login]
    B -->|Oui| D[Lister rapports en attente]
    D --> E{Sélection action}
    E -->|Valider| F[etat = valide]
    E -->|Refuser| G[etat = refuse]
    F --> H[Ajouter commentaire optionnel]
    G --> H
    H --> I[UPDATE BDD]
    I --> J[Fin]
```

### UC21 — Soumission rapport PDF

```mermaid
flowchart TD
    A[Début] --> B{Étudiant connecté ?}
    B -->|Non| C[Rediriger login]
    B -->|Oui| D[Sélectionner projet]
    D --> E[Choisir fichier PDF]
    E --> F{Format PDF et taille OK ?}
    F -->|Non| G[Message erreur]
    F -->|Oui| H[Enregistrer fichier uploads]
    H --> I[INSERT rapports en_attente]
    I --> J[Fin succès]
    G --> D
```

---

## 6. Diagramme de classes

```mermaid
classDiagram
    class Utilisateur {
        +int id
        +string nom
        +string email
        +string mot_de_passe
        +enum role
        +seConnecter()
        +seDeconnecter()
    }

    class Edition {
        +int id
        +year annee
        +string description
        +date date_debut
        +date date_fin
    }

    class Projet {
        +int id
        +string nom
        +string description
        +string categorie
        +int id_edition
        +int id_utilisateur
    }

    class Rapport {
        +int id
        +int id_projet
        +datetime date_soumission
        +string fichier
        +enum etat_validation
        +string commentaire
    }

    class Presentation {
        +int id
        +int id_projet
        +string nom_fichier
        +string chemin_fichier
        +enum etat_validation
    }

    class Commentaire {
        +int id
        +int id_projet
        +int id_utilisateur
        +string commentaire
        +datetime date_creation
    }

    class Organisateur {
        +gererEditions()
        +gererProjets()
        +validerRapport()
        +validerPresentation()
        +gererGroupesSalles()
    }

    class Etudiant {
        +soumettreProjet()
        +soumettreRapport()
        +soumettrePresentation()
        +suivreSoumissions()
    }

    Utilisateur <|-- Organisateur
    Utilisateur <|-- Etudiant
    Utilisateur <|-- Visiteur
    Edition "1" --> "*" Projet
    Projet "1" --> "*" Rapport
    Projet "1" --> "*" Presentation
    Projet "1" --> "*" Commentaire
    Utilisateur --> Commentaire
```

---

## 7. Diagramme de déploiement

```mermaid
flowchart TB
    subgraph Client
        Browser[Navigateur Web]
    end

    subgraph Serveur_WAMP
        Apache[Apache HTTP Server]
        PHP[PHP 8.x]
        subgraph App
            Htdocs[htdocs/ pages publiques]
            AppLogic[app/ logique métier]
            Config[config/]
        end
    end

    subgraph Donnees
        MySQL[(MySQL seic_lbs)]
        Uploads[uploads/ rapports presentations]
        Logs[logs/]
    end

    Browser -->|HTTP HTTPS| Apache
    Apache --> PHP
    PHP --> Htdocs
    Htdocs --> AppLogic
    AppLogic --> Config
    AppLogic --> MySQL
    AppLogic --> Uploads
    PHP --> Logs
```

---

## Correspondance code ↔ cas d'utilisation

| UC | Fichier(s) principal(aux) |
|----|---------------------------|
| UC00 | `htdocs/login.php`, `app/auth.php` |
| UC01 | `htdocs/register.php` |
| UC02 | `htdocs/logout.php` |
| UC10 | `htdocs/editions.php`, `app/editions.php` |
| UC11 | `htdocs/projets.php`, `app/projets.php` |
| UC12 | `htdocs/rapports_admin.php`, `app/rapports_admin.php` |
| UC13 | `htdocs/gestion_powerpoint.php` |
| UC14 | `htdocs/groupes_salles.php` |
| UC15 | `htdocs/projets_edition.php` |
| UC16 | `htdocs/import_pdf.php` |
| UC17 | `htdocs/export_zip.php` |
| UC20–22 | `htdocs/soumettre_*.php` |
| UC23 | `htdocs/suivi_soumissions.php` |
| UC24 | `htdocs/etudiant_projets.php` |
| UC30 | `htdocs/index.php` |
| UC31–32 | `htdocs/visitor_projects.php` |

---

**Auteur :** Khalil — Projet SEIC LBS, Lomé Business School
