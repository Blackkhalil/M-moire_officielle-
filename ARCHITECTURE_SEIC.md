# 🏗️ Architecture Client-Serveur - Plateforme SEIC LBS

## 📋 Table des Matières
1. [Architecture Globale](#architecture-globale)
2. [Flux de Données - Authentification](#flux-dauthentification)
3. [Flux de Données - Upload de Fichiers](#flux-upload-fichiers)
4. [Flux de Données - Notifications Temps Réel](#flux-notifications)
5. [Structure des Modules Backend](#structure-modules)
6. [Diagramme de Séquence - Connexion](#sequence-connexion)
7. [Diagramme de Séquence - Soumission Rapport](#sequence-soumission)

---

## 🎯 Architecture Globale

```mermaid
graph TB
    subgraph "CLIENT (Navigateur)"
        Browser[🌐 Navigateur Web<br/>Chrome, Firefox, Edge]
        HTML[📄 HTML5<br/>Structure des pages]
        CSS[🎨 CSS3 + Bootstrap 5.3<br/>Design responsive]
        JS[JavaScript<br/>Interactions & AJAX]
        FontAwesome[Font Awesome 6.4<br/>Icônes]
    end

    subgraph "SERVEUR WEB (Apache/WAMP)"
        Apache[🌐 Apache Server<br/>Port 80/8080]
        PHP[⚙️ PHP 8.0+<br/>Interpréteur]
    end

    subgraph "FRONTEND (htdocs/)"
        Pages[📄 Pages PHP<br/>- index.php<br/>- login.php<br/>- dashboard.php<br/>- soumettre_*.php]
        API[🔌 API REST<br/>api/notifications.php]
        Static[📁 Ressources Statiques<br/>- css/<br/>- images/<br/>- js/]
    end

    subgraph "BACKEND (app/)"
        Auth[🔐 auth.php<br/>Authentification & Sessions]
        Business[💼 Logique Métier<br/>- soumission_*.php<br/>- rapports_admin.php<br/>- gestion_*.php]
        Notif[🔔 notification_manager.php<br/>Système de notifications]
    end

    subgraph "CONFIGURATION"
        Config[⚙️ config/config.php<br/>Configuration BDD & Chemins]
        UploadConfig[📤 upload_config.php<br/>Limites uploads]
    end

    subgraph "BASE DE DONNÉES"
        MySQL[(🗄️ MySQL/MariaDB<br/>Base de données)]
        
        subgraph "Tables Principales"
            Users[(👥 utilisateurs)]
            Projets[(📊 projets)]
            Rapports[(📄 rapports)]
            Presentations[(📽️ presentations)]
            Notifications[(🔔 notifications)]
            Editions[(📅 editions)]
            Groupes[(👨‍👩‍👧‍👦 groupes)]
            Salles[(🏢 salles)]
        end
    end

    subgraph "STOCKAGE"
        Uploads[📁 uploads/<br/>- rapports/<br/>- presentations/]
    end

    Browser -->|HTTP/HTTPS| Apache
    Browser -->|Requêtes| Pages
    Browser -->|AJAX/Fetch| API
    
    HTML --> Browser
    CSS --> Browser
    JS --> Browser
    FontAwesome --> Browser
    
    Apache --> PHP
    PHP --> Pages
    Pages -->|require_once| Auth
    Pages -->|require_once| Business
    Pages -->|require_once| Config
    
    API -->|require_once| Auth
    API -->|require_once| Notif
    API -->|require_once| Config
    
    Business -->|PDO| Config
    Auth -->|PDO| Config
    Notif -->|PDO| Config
    
    Config -->|Connexion PDO| MySQL
    MySQL --> Users
    MySQL --> Projets
    MySQL --> Rapports
    MySQL --> Presentations
    MySQL --> Notifications
    MySQL --> Editions
    MySQL --> Groupes
    MySQL --> Salles
    
    Business -->|Upload| Uploads
    Pages -->|Affichage| Static
    
    style Browser fill:#e1f5ff
    style Apache fill:#fff4e1
    style PHP fill:#ffe1f5
    style MySQL fill:#e1ffe1
    style Uploads fill:#f5e1ff
```

---

## 🔐 Flux d'Authentification

```mermaid
sequenceDiagram
    participant Client as 👤 Client (Navigateur)
    participant Frontend as 📄 login.php
    participant Auth as 🔐 app/auth.php
    participant Session as 📦 Session PHP
    participant DB as 🗄️ MySQL (utilisateurs)

    Client->>Frontend: GET /login.php
    Frontend->>Client: Affiche formulaire de connexion
    
    Client->>Frontend: POST email + mot_de_passe
    Frontend->>Auth: login(email, mot_de_passe)
    
    Auth->>DB: SELECT * FROM utilisateurs WHERE email = ?
    DB-->>Auth: Résultat utilisateur
    
    alt Identifiants valides
        Auth->>Auth: password_verify(mot_de_passe, hash)
        Auth->>Session: Créer session utilisateur
        Session->>Session: $_SESSION['user_id'] = id<br/>$_SESSION['user_role'] = role
        Auth-->>Frontend: return true
        Frontend->>Client: Redirect → dashboard.php
    else Identifiants invalides
        Auth-->>Frontend: return false
        Frontend->>Client: Affiche erreur "Email ou mot de passe incorrect"
    end
    
    Note over Client,DB: Session maintenue pour toutes les requêtes suivantes
```

---

## 📤 Flux Upload de Fichiers (Rapport PDF)

```mermaid
sequenceDiagram
    participant Client as 👤 Étudiant (Navigateur)
    participant Frontend as 📄 soumettre_rapport.php
    participant Backend as 💼 app/soumission_rapport.php
    participant Config as ⚙️ config/upload_config.php
    participant FileSystem as 📁 uploads/rapports/
    participant DB as 🗄️ MySQL (rapports)

    Client->>Frontend: GET /soumettre_rapport.php
    Frontend->>Auth: Vérifier authentification
    Frontend->>Client: Affiche formulaire upload
    
    Client->>Frontend: POST fichier PDF + id_projet
    Frontend->>Backend: Traiter soumission
    
    Backend->>Config: Vérifier limites upload
    Config-->>Backend: upload_max_filesize: 50M
    
    Backend->>Backend: Valider fichier:<br/>- Extension .pdf<br/>- Type MIME<br/>- Taille < 50MB
    
    alt Fichier valide
        Backend->>FileSystem: Générer nom unique<br/>rapport_[id]_[timestamp].pdf
        Backend->>FileSystem: move_uploaded_file()
        FileSystem-->>Backend: Fichier sauvegardé
        
        Backend->>DB: INSERT INTO rapports<br/>(id_projet, fichier, date_soumission, etat_validation)
        DB-->>Backend: ID rapport créé
        
        Backend->>Notif: Créer notification pour organisateur
        Backend-->>Frontend: Succès
        Frontend->>Client: Message "Rapport soumis avec succès"
    else Fichier invalide
        Backend-->>Frontend: Erreur
        Frontend->>Client: Message d'erreur
    end
```

---

## 🔔 Flux Notifications Temps Réel

```mermaid
sequenceDiagram
    participant Client as 👤 Étudiant (Navigateur)
    participant Dashboard as 📄 dashboard.php
    participant JS as ⚡ JavaScript (setInterval)
    participant API as 🔌 api/notifications.php
    participant NotifManager as 🔔 app/notification_manager.php
    participant DB as 🗄️ MySQL (notifications)

    Client->>Dashboard: Charger dashboard.php
    Dashboard->>Client: HTML + JavaScript
    
    loop Toutes les 30 secondes
        JS->>API: GET /api/notifications.php?action=get_unread
        API->>Auth: Vérifier session utilisateur
        API->>NotifManager: getUnreadNotifications(user_id, 5)
        NotifManager->>DB: SELECT * FROM notifications<br/>WHERE user_id = ? AND lu = 0<br/>ORDER BY created_at DESC LIMIT 5
        DB-->>NotifManager: Liste notifications
        NotifManager-->>API: JSON notifications
        API-->>JS: {notifications: [...], unread_count: 3}
        
        JS->>JS: Mettre à jour badge notification
        JS->>JS: Afficher liste notifications
    end
    
    Client->>API: POST /api/notifications.php?action=mark_read<br/>notification_id=123
    API->>NotifManager: markAsRead(123, user_id)
    NotifManager->>DB: UPDATE notifications SET lu = 1<br/>WHERE id = 123 AND user_id = ?
    DB-->>NotifManager: Succès
    NotifManager-->>API: {success: true}
    API-->>Client: {success: true}
```

---

## 🏛️ Structure des Modules Backend

```mermaid
graph LR
    subgraph "MODULE AUTHENTIFICATION"
        Auth[🔐 auth.php]
        Session[📦 session_manager.php]
    end

    subgraph "MODULE SOUMISSIONS"
        SubProjet[📝 soumission_projet.php]
        SubRapport[📄 soumission_rapport.php]
        SubPPT[📽️ soumission_powerpoint.php]
        Suivi[📊 suivi_soumissions.php]
    end

    subgraph "MODULE ADMINISTRATION"
        AdminRapports[✅ rapports_admin.php]
        AdminPPT[📽️ gestion_powerpoint.php]
        AdminProjets[📊 projets.php]
        AdminEditions[📅 editions.php]
        AdminGroupes[👥 groupes_salles.php]
    end

    subgraph "MODULE VISUALISATION"
        Visitor[👁️ visitor_projects.php]
        ProjetsEdition[📅 projets_edition.php]
    end

    subgraph "MODULE NOTIFICATIONS"
        NotifManager[🔔 notification_manager.php]
    end

    subgraph "CONFIGURATION"
        Config[⚙️ config/config.php]
        UploadConfig[📤 upload_config.php]
    end

    Auth --> Session
    SubProjet --> Auth
    SubRapport --> Auth
    SubPPT --> Auth
    Suivi --> Auth
    
    AdminRapports --> Auth
    AdminPPT --> Auth
    AdminProjets --> Auth
    AdminEditions --> Auth
    AdminGroupes --> Auth
    
    Visitor --> Auth
    ProjetsEdition --> Auth
    
    NotifManager --> Config
    Auth --> Config
    SubRapport --> UploadConfig
    SubPPT --> UploadConfig
    
    style Auth fill:#ff6b6b
    style Config fill:#4ecdc4
    style NotifManager fill:#ffe66d
```

---

## 🔄 Diagramme de Séquence - Connexion Complète

```mermaid
sequenceDiagram
    participant U as 👤 Utilisateur
    participant B as 🌐 Navigateur
    participant L as 📄 login.php
    participant A as 🔐 auth.php
    participant S as 📦 Session
    participant D as 🗄️ MySQL
    participant Dash as 📊 dashboard.php

    U->>B: Accède à login.php
    B->>L: GET /login.php
    L->>A: require_once auth.php
    A->>S: session_start()
    A->>S: Vérifier si session existe
    S-->>A: Session vide
    A-->>L: is_logged_in() = false
    L->>B: Affiche formulaire HTML
    B->>U: Formulaire de connexion
    
    U->>B: Saisit email + mot_de_passe
    B->>L: POST /login.php
    L->>A: login(email, mot_de_passe)
    A->>D: SELECT * FROM utilisateurs WHERE email = ?
    D-->>A: {id, nom, email, hash, role}
    A->>A: password_verify(mot_de_passe, hash)
    
    alt Mot de passe correct
        A->>S: $_SESSION['user_id'] = id
        A->>S: $_SESSION['user_nom'] = nom
        A->>S: $_SESSION['user_role'] = role
        A-->>L: return true
        L->>B: HTTP 302 → dashboard.php
        B->>Dash: GET /dashboard.php
        Dash->>A: require_once auth.php
        A->>S: Vérifier session
        S-->>A: Session valide
        A-->>Dash: is_logged_in() = true
        A-->>Dash: current_user() = {id, nom, role}
        Dash->>D: SELECT * FROM projets WHERE membres LIKE ?
        D-->>Dash: Liste projets
        Dash->>B: HTML avec dashboard personnalisé
        B->>U: Affiche dashboard
    else Mot de passe incorrect
        A-->>L: return false
        L->>B: Affiche erreur
        B->>U: "Email ou mot de passe incorrect"
    end
```

---

## 📊 Diagramme de Séquence - Soumission Rapport

```mermaid
sequenceDiagram
    participant E as 👨‍🎓 Étudiant
    participant B as 🌐 Navigateur
    participant F as 📄 soumettre_rapport.php
    participant A as 🔐 auth.php
    participant Back as 💼 soumission_rapport.php
    participant C as ⚙️ upload_config.php
    participant FS as 📁 uploads/rapports/
    participant DB as 🗄️ MySQL
    participant N as 🔔 notification_manager.php

    E->>B: Accède à soumettre_rapport.php
    B->>F: GET /soumettre_rapport.php
    F->>A: Vérifier authentification
    A-->>F: Utilisateur connecté
    F->>DB: SELECT * FROM projets WHERE membres LIKE ?
    DB-->>F: Liste projets de l'étudiant
    F->>B: Affiche formulaire avec projets
    B->>E: Formulaire upload
    
    E->>B: Sélectionne projet + fichier PDF
    B->>F: POST /soumettre_rapport.php (multipart/form-data)
    F->>Back: Traiter soumission
    Back->>C: Vérifier limites upload
    C-->>Back: upload_max_filesize: 50M
    
    Back->>Back: Valider fichier:<br/>- $_FILES['fichier']['type'] == 'application/pdf'<br/>- Extension .pdf<br/>- Taille < 50MB<br/>- Erreur upload == 0
    
    alt Fichier valide
        Back->>FS: Générer nom: rapport_[projet_id]_[timestamp].pdf
        Back->>FS: move_uploaded_file(tmp, chemin_final)
        FS-->>Back: Fichier sauvegardé
        
        Back->>DB: INSERT INTO rapports<br/>(id_projet, fichier, date_soumission, etat_validation='en_attente')
        DB-->>Back: ID rapport créé
        
        Back->>DB: SELECT id FROM utilisateurs WHERE role = 'organisateur'
        DB-->>Back: Liste organisateurs
        
        loop Pour chaque organisateur
            Back->>N: createNotification(org_id, 'Nouveau rapport soumis', ...)
            N->>DB: INSERT INTO notifications (user_id, title, message, type)
            DB-->>N: Notification créée
        end
        
        Back-->>F: Succès
        F->>B: HTTP 302 → suivi_soumissions.php
        B->>E: Affiche confirmation
    else Fichier invalide
        Back-->>F: Erreur (format, taille, etc.)
        F->>B: Affiche message d'erreur
        B->>E: Erreur visible
    end
```

---

## 🔗 Interactions Frontend-Backend

```mermaid
graph TB
    subgraph "FRONTEND - Pages Interactives"
        Login[login.php<br/>📝 Formulaire]
        Dashboard[dashboard.php<br/>📊 Vue d'ensemble]
        Submit[soumettre_rapport.php<br/>📤 Upload]
        Admin[rapports_admin.php<br/>✅ Validation]
        Suivi[suivi_soumissions.php<br/>📈 Suivi]
    end

    subgraph "FRONTEND - API REST"
        API[api/notifications.php<br/>🔌 Endpoint JSON]
    end

    subgraph "BACKEND - Logique Métier"
        Auth[app/auth.php<br/>🔐 Authentification]
        SubmitLogic[app/soumission_rapport.php<br/>💼 Traitement upload]
        AdminLogic[app/rapports_admin.php<br/>✅ Validation logique]
        Notif[app/notification_manager.php<br/>🔔 Gestion notifications]
    end

    subgraph "COMMUNICATION"
        HTTP[HTTP/HTTPS<br/>Requêtes GET/POST]
        AJAX[AJAX/Fetch API<br/>Requêtes asynchrones]
        Session[Session PHP<br/>$_SESSION]
        PDO[PDO MySQL<br/>Requêtes SQL]
    end

    Login -->|POST| HTTP
    HTTP -->|Appelle| Auth
    Auth -->|Stocke| Session
    Auth -->|Query| PDO

    Dashboard -->|AJAX| AJAX
    AJAX -->|Fetch| API
    API -->|Utilise| Notif
    Notif -->|Query| PDO

    Submit -->|POST multipart| HTTP
    HTTP -->|Appelle| SubmitLogic
    SubmitLogic -->|Utilise| Auth
    SubmitLogic -->|Query| PDO
    SubmitLogic -->|Crée| Notif

    Admin -->|POST| HTTP
    HTTP -->|Appelle| AdminLogic
    AdminLogic -->|Utilise| Auth
    AdminLogic -->|Query| PDO
    AdminLogic -->|Crée| Notif

    Suivi -->|GET| HTTP
    HTTP -->|Appelle| Auth
    Auth -->|Vérifie| Session

    style HTTP fill:#4ecdc4
    style AJAX fill:#ffe66d
    style Session fill:#ff6b6b
    style PDO fill:#95e1d3
```

---

## 📦 Structure des Données

```mermaid
erDiagram
    utilisateurs ||--o{ projets : "crée"
    utilisateurs ||--o{ rapports : "soumet"
    utilisateurs ||--o{ presentations : "soumet"
    utilisateurs ||--o{ notifications : "reçoit"
    
    editions ||--o{ projets : "contient"
    editions ||--o{ groupes : "contient"
    
    projets ||--o{ rapports : "a"
    projets ||--o{ presentations : "a"
    projets }o--|| groupes : "appartient à"
    
    groupes }o--|| salles : "utilise"

    utilisateurs {
        int id PK
        string nom
        string email UK
        string mot_de_passe
        enum role
    }
    
    projets {
        int id PK
        string nom
        text description
        string categorie
        text membres
        int id_edition FK
    }
    
    rapports {
        int id PK
        int id_projet FK
        datetime date_soumission
        string fichier
        enum etat_validation
        text commentaire
    }
    
    presentations {
        int id PK
        int id_projet FK
        datetime date_soumission
        string fichier
        enum etat_validation
        text commentaire
    }
    
    notifications {
        int id PK
        int user_id FK
        string title
        text message
        enum type
        boolean lu
        datetime created_at
    }
    
    editions {
        int id PK
        year annee
        text description
        date date_debut
        date date_fin
    }
    
    groupes {
        int id PK
        string nom
        int id_edition FK
        int id_salle FK
        int nombre_membres
    }
    
    salles {
        int id PK
        string nom
        string localisation
    }
```

---

## 🎨 Stack Technologique

```mermaid
graph TB
    subgraph "CLIENT-SIDE"
        HTML5[HTML5<br/>Structure]
        CSS3[CSS3 + Bootstrap 5.3<br/>Design responsive]
        JS[Vanilla JavaScript<br/>Interactions]
        FA[Font Awesome 6.4<br/>Icônes]
    end

    subgraph "SERVER-SIDE"
        PHP8[PHP 8.0+<br/>Backend]
        Apache[Apache Server<br/>HTTP Server]
        PDO[PDO Extension<br/>Database Access]
    end

    subgraph "DATABASE"
        MySQL[MySQL/MariaDB<br/>RDBMS]
    end

    subgraph "ARCHITECTURE"
        MVC[Pattern MVC<br/>Séparation des responsabilités]
        REST[REST API<br/>api/notifications.php]
        Session[Session Management<br/>Authentification]
    end

    HTML5 --> CSS3
    CSS3 --> JS
    JS --> FA
    
    Apache --> PHP8
    PHP8 --> PDO
    PDO --> MySQL
    
    PHP8 --> MVC
    PHP8 --> REST
    PHP8 --> Session

    style PHP8 fill:#4F5D95
    style MySQL fill:#4479A1
    style Apache fill:#D22128
    style Bootstrap fill:#7952B3
```

---

## 📝 Notes Importantes

### 🔒 Sécurité
- **Sessions sécurisées** : Régénération d'ID de session
- **Requêtes préparées** : Protection contre injection SQL
- **Validation des fichiers** : Vérification extension + type MIME
- **Authentification requise** : Vérification sur chaque page protégée

### ⚡ Performance
- **PDO avec requêtes préparées** : Optimisation des requêtes SQL
- **Index sur les tables** : Amélioration des performances
- **CDN pour Bootstrap/Font Awesome** : Chargement rapide des ressources

### 📱 Responsive Design
- **Bootstrap 5.3** : Grille responsive
- **Media queries** : Adaptation mobile/tablette/desktop
- **Design mobile-first** : Optimisé pour petits écrans

---

**Auteur** : Khalil  
**Version** : 1.0  
**Date** : 2025

