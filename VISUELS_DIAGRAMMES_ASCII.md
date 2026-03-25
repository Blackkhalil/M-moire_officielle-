# 📊 Visualisations des Diagrammes UML - Plateforme SEIC LBS

Ce document contient les représentations visuelles de tous les diagrammes en format texte/ASCII pour faciliter leur reproduction dans Draw.io.

---

## 📊 1. Diagramme de Classes

```
┌─────────────────────┐
│    Utilisateur      │
├─────────────────────┤
│ + id : int          │
│ + nom : string      │
│ + email : string    │
│ + mot_de_passe : str│
│ + role : string     │
└─────────────────────┘
         │
         │ soumet (1 → 0..*)
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌─────────┐ ┌──────────────┐
│ Rapport │ │ Presentation │
├─────────┤ ├──────────────┤
│ + id    │ │ + id         │
│ + id_   │ │ + id_projet  │
│   projet│ │ + id_        │
│ + id_   │ │   utilisateur│
│   utilis│ │ + date_      │
│   ateur │ │   soumission │
│ + date_ │ │ + fichier    │
│   soum  │ │ + etat_      │
│ + fich  │ │   validation │
│ + etat_ │ │ + commentaire│
│   valid │ └──────────────┘
│ + comm  │
└─────────┘
    │
    │ possède (1 → 0..*)
    │
    ▼
┌─────────────┐
│   Projet    │
├─────────────┤
│ + id        │
│ + nom       │
│ + description│
│ + categorie │
│ + membres   │
│ + id_edition│
└─────────────┘
    │
    │ reçoit (1 → 0..*)
    │
    ▼
┌──────────────┐
│ Commentaire  │
├──────────────┤
│ + id         │
│ + id_projet  │
│ + auteur     │
│ + contenu    │
│ + date_      │
│   creation   │
└──────────────┘

┌─────────────┐
│   Edition   │
├─────────────┤
│ + id        │
│ + annee     │
│ + description│
│ + date_debut│
│ + date_fin  │
└─────────────┘
    │
    │ contient (1 → 0..*)
    │
    ▼
┌─────────────┐
│   Groupe    │
├─────────────┤
│ + id        │
│ + nom       │
│ + id_edition│
│ + id_salle  │
│ + nombre_   │
│   membres   │
└─────────────┘
    │
    │ se déroule dans (1 → 1)
    │
    ▼
┌─────────────┐
│    Salle    │
├─────────────┤
│ + id        │
│ + nom       │
│ + localisat │
│   ion       │
└─────────────┘
```

---

## 🔄 2. Diagramme de Séquence - Connexion Utilisateur

```
Utilisateur    Navigateur    login.php    auth.php    Session PHP    MySQL
    │              │             │            │            │           │
    │─────────────>│             │            │            │           │
    │  Accède à    │             │            │            │           │
    │  /login.php  │             │            │            │           │
    │              │────────────>│            │            │           │
    │              │ GET /login  │            │            │           │
    │              │             │───────────>│            │           │
    │              │             │require_once│            │           │
    │              │             │            │──────────>│           │
    │              │             │            │session_   │           │
    │              │             │            │start()     │           │
    │              │             │            │<───────────│           │
    │              │             │            │Session vide│           │
    │              │             │<───────────│            │           │
    │              │             │is_logged_  │            │           │
    │              │             │in()=false  │            │           │
    │              │<────────────│            │            │           │
    │              │Formulaire   │            │            │           │
    │              │HTML         │            │            │           │
    │<─────────────│             │            │            │           │
    │              │             │            │            │           │
    │─────────────>│             │            │            │           │
    │Saisir email  │             │            │            │           │
    │+ mot de passe│             │            │            │           │
    │              │────────────>│            │            │           │
    │              │POST /login  │            │            │           │
    │              │             │───────────>│            │           │
    │              │             │login(...)  │            │           │
    │              │             │            │───────────>│           │
    │              │             │            │SELECT *    │           │
    │              │             │            │FROM users  │           │
    │              │             │            │WHERE email │           │
    │              │             │            │<───────────│           │
    │              │             │            │{id, nom,   │           │
    │              │             │            │email, hash,│           │
    │              │             │            │role}       │           │
    │              │             │            │            │           │
    │              │             │            │[password_  │           │
    │              │             │            │verify()]   │           │
    │              │             │            │            │           │
    │              │             │            │┌───────────┐│           │
    │              │             │            ││Alt:       ││           │
    │              │             │            ││Identifiants│           │
    │              │             │            ││valides    ││           │
    │              │             │            │└───────────┘│           │
    │              │             │            │            │           │
    │              │             │            │───────────>│           │
    │              │             │            │Créer       │           │
    │              │             │            │$_SESSION   │           │
    │              │             │<───────────│            │           │
    │              │             │return true │            │           │
    │              │<────────────│            │            │           │
    │              │HTTP 302     │            │            │           │
    │              │dashboard.php│            │            │           │
    │              │             │            │            │           │
    │              │             │            │┌───────────┐│           │
    │              │             │            ││Alt:       ││           │
    │              │             │            ││Identifiants│           │
    │              │             │            ││invalides  ││           │
    │              │             │            │└───────────┘│           │
    │              │             │<───────────│            │           │
    │              │             │return false│            │           │
    │              │<────────────│            │            │           │
    │              │Affiche erreur│            │            │           │
    │<─────────────│             │            │            │           │
    │"Email ou mot │             │            │            │           │
    │de passe      │             │            │            │           │
    │incorrect"    │             │            │            │           │
```

---

## 🔄 3. Diagramme de Séquence - Soumission d'un Rapport

```
Étudiant    Navigateur    soumettre_    auth.php    soumission_    upload_      uploads/    MySQL
            │              rapport.php   │           rapport.php    config.php   rapports/   │
            │              │            │           │              │            │           │
───────────>│              │            │           │              │            │           │
Accède à    │              │            │           │              │            │           │
soumettre_  │              │            │           │              │            │           │
rapport.php │              │            │           │              │            │           │
            │─────────────>│            │           │              │            │           │
            │GET /soumettre│            │           │              │            │           │
            │_rapport.php  │            │           │              │            │           │
            │              │───────────>│           │              │            │           │
            │              │Vérifier    │           │              │            │           │
            │              │authentif.  │           │              │            │           │
            │              │<───────────│           │              │            │           │
            │              │Utilisateur │           │              │            │           │
            │              │connecté    │           │              │            │           │
            │              │───────────>│           │              │            │           │
            │              │SELECT      │           │              │            │           │
            │              │projets     │           │              │            │           │
            │              │WHERE membres│          │              │            │           │
            │              │<───────────│           │              │            │           │
            │              │Liste projets│          │              │            │           │
            │<─────────────│            │           │              │            │           │
            │Formulaire    │            │           │              │            │           │
            │avec projets  │            │           │              │            │           │
<───────────│              │            │           │              │            │           │
            │              │            │           │              │            │           │
───────────>│              │            │           │              │            │           │
Choisir     │              │            │           │              │            │           │
projet +    │              │            │           │              │            │           │
fichier PDF │              │            │           │              │            │           │
            │─────────────>│            │           │              │            │           │
            │POST multipart│           │           │              │            │           │
            │/form-data    │            │           │              │            │           │
            │              │───────────>│           │              │            │           │
            │              │Traiter     │           │              │            │           │
            │              │soumission  │           │              │            │           │
            │              │            │───────────>│              │            │           │
            │              │            │Vérifier   │              │            │           │
            │              │            │limites    │              │            │           │
            │              │            │upload     │              │            │           │
            │              │            │<──────────│              │            │           │
            │              │            │upload_max │              │            │           │
            │              │            │filesize:  │              │            │           │
            │              │            │50M        │              │            │           │
            │              │            │           │              │            │           │
            │              │            │[Valider   │              │            │           │
            │              │            │fichier]   │              │            │           │
            │              │            │           │              │            │           │
            │              │            │┌──────────┐│              │            │           │
            │              │            ││Alt:      ││              │            │           │
            │              │            ││Fichier   ││              │            │           │
            │              │            ││valide    ││              │            │           │
            │              │            │└──────────┘│              │            │           │
            │              │            │           │──────────────>│            │           │
            │              │            │           │move_uploaded_ │            │           │
            │              │            │           │file()         │            │           │
            │              │            │           │<──────────────│            │           │
            │              │            │           │Fichier        │            │           │
            │              │            │           │sauvegardé      │            │           │
            │              │            │           │───────────────>│           │           │
            │              │            │           │INSERT INTO    │           │           │
            │              │            │           │rapports(...)  │           │           │
            │              │            │           │<───────────────│           │           │
            │              │            │           │ID rapport     │           │           │
            │              │            │           │créé            │           │           │
            │              │            │<──────────│                │           │           │
            │              │            │Succès     │                │           │           │
            │              │<───────────│           │                │           │           │
            │              │Redirection │           │                │           │           │
            │              │suivi_      │           │                │           │           │
            │              │soumissions │           │                │           │           │
<───────────│              │            │           │                │           │           │
Message     │              │            │           │                │           │           │
"Rapport    │              │            │           │                │           │           │
soumis avec │              │            │           │                │           │           │
succès"     │              │            │           │                │           │           │
            │              │            │           │                │           │           │
            │              │            │┌──────────┐│              │            │           │
            │              │            ││Alt:      ││              │            │           │
            │              │            ││Fichier   ││              │            │           │
            │              │            ││invalide  ││              │            │           │
            │              │            │└──────────┘│              │            │           │
            │              │            │<───────────│              │            │           │
            │              │            │Erreur     │              │            │           │
            │              │<───────────│           │              │            │           │
            │              │Affiche     │           │              │            │           │
            │              │message     │           │              │            │           │
            │              │d'erreur    │           │              │            │           │
<───────────│              │            │           │              │            │           │
Erreur      │              │            │           │              │            │           │
visible     │              │            │           │              │            │           │
```

---

## 🔄 4. Diagramme de Séquence - Consultation Projets et Commentaires

```
Visiteur    Navigateur    visitor_      commentaires.php    MySQL
            │              projects.php │                    │
            │              │            │                    │
───────────>│              │            │                    │
Accède à    │              │            │                    │
visitor_    │              │            │                    │
projects.php│              │            │                    │
            │─────────────>│            │                    │
            │GET /visitor_ │            │                    │
            │projects.php  │            │                    │
            │              │───────────>│                    │
            │              │SELECT      │                    │
            │              │projets     │                    │
            │              │publiés     │                    │
            │              │<───────────│                    │
            │              │Liste projets│                   │
            │              │            │                    │
            │              │┌───────────┐│                    │
            │              ││Loop:      ││                    │
            │              ││Pour chaque││                    │
            │              ││projet     ││                    │
            │              │└───────────┘│                    │
            │              │            │                    │
            │              │───────────>│                    │
            │              │getComment  │                    │
            │              │airesProjet │                    │
            │              │(id_projet) │                    │
            │              │            │───────────>│        │
            │              │            │SELECT *    │        │
            │              │            │FROM        │        │
            │              │            │commentaires│        │
            │              │            │WHERE        │        │
            │              │            │id_projet = ?│        │
            │              │            │<────────────│        │
            │              │            │Liste        │        │
            │              │            │commentaires │        │
            │              │<───────────│            │        │
            │              │Commentaires│            │        │
            │              │du projet   │            │        │
            │              │            │            │        │
            │<─────────────│            │            │        │
            │Afficher      │            │            │        │
            │projets +     │            │            │        │
            │commentaires  │            │            │        │
            │              │            │            │        │
───────────>│              │            │            │        │
Saisir un   │              │            │            │        │
nouveau     │              │            │            │        │
commentaire│              │            │            │        │
            │─────────────>│            │            │        │
            │POST          │            │            │        │
            │commentaire   │            │            │        │
            │              │───────────>│            │        │
            │              │addComment  │            │        │
            │              │aire(...)   │            │        │
            │              │            │───────────>│        │
            │              │            │INSERT INTO │        │
            │              │            │commentaires│        │
            │              │            │(...)       │        │
            │              │            │<───────────│        │
            │              │            │Succès      │        │
            │              │<───────────│            │        │
            │              │OK          │            │        │
            │<─────────────│            │            │        │
            │Rechargement /│            │            │        │
            │mise à jour   │            │            │        │
            │liste comment │            │            │        │
```

---

## 🔄 5. Diagramme d'Activités - Connexion Utilisateur

```
                    ┌─────────────┐
                    │   Début    │
                    └──────┬──────┘
                           │
                           ▼
            ┌──────────────────────────┐
            │ Ouvrir page login.php    │
            └──────┬───────────────────┘
                   │
                   ▼
    ┌──────────────────────────────┐
    │ Saisir email et mot de passe  │
    └──────┬────────────────────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Cliquer sur          │
    │ 'Se connecter'       │
    └──────┬───────────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Contrôler format des  │
    │ champs               │
    └──────┬───────────────┘
           │
           ▼
    ┌──────────────┐
    │ Format       │
    │ valide ?     │
    └──┬────────┬──┘
       │        │
    Non│        │Oui
       │        │
       ▼        │
┌──────────────┐│
│ Afficher     ││
│ message      ││
│ d'erreur     ││
│ formulaire   ││
└──────┬───────┘│
       │        │
       └────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Rechercher utilisateur│
    │ par email en base     │
    └──────┬────────────────┘
           │
           ▼
    ┌──────────────┐
    │ Utilisateur  │
    │ trouvé ?     │
    └──┬────────┬──┘
       │        │
    Non│        │Oui
       │        │
       ▼        │
┌──────────────┐│
│ Afficher     ││
│ 'Email ou    ││
│ mot de passe ││
│ incorrect'   ││
└──────┬───────┘│
       │        │
       └────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Comparer mot de passe│
    │ avec hash            │
    └──────┬───────────────┘
           │
           ▼
    ┌──────────────┐
    │ Mot de passe │
    │ correct ?    │
    └──┬────────┬──┘
       │        │
    Non│        │Oui
       │        │
       ▼        │
┌──────────────┐│
│ Afficher     ││
│ 'Email ou    ││
│ mot de passe ││
│ incorrect'   ││
└──────┬───────┘│
       │        │
       └────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Créer session        │
    │ utilisateur          │
    └──────┬───────────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Redirection vers     │
    │ dashboard.php        │
    └──────┬───────────────┘
           │
           ▼
    ┌──────────────────────┐
    │ Afficher tableau     │
    │ de bord              │
    └──────┬───────────────┘
           │
           ▼
           ┌─────────────┐
           │    Fin      │
           └─────────────┘
```

---

## 🔄 6. Diagramme d'Activités - Soumission d'un Rapport

```
                    ┌─────────────┐
                    │   Début    │
                    └──────┬──────┘
                           │
                           ▼
            ┌──────────────────────────┐
            │ Étudiant connecté        │
            └──────┬───────────────────┘
                   │
                   ▼
            ┌──────────────────────────┐
            │ Ouvrir page               │
            │ soumettre_rapport.php    │
            └──────┬───────────────────┘
                   │
                   ▼
            ┌──────────────────────────┐
            │ Sélectionner projet       │
            └──────┬───────────────────┘
                   │
                   ▼
            ┌──────────────────────────┐
            │ Choisir fichier PDF      │
            └──────┬───────────────────┘
                   │
                   ▼
            ┌──────────────────────────┐
            │ Cliquer sur 'Soumettre'  │
            └──────┬───────────────────┘
                   │
                   ▼
            ┌──────────────────────────┐
            │ Contrôler extension,     │
            │ taille, type MIME       │
            └──────┬───────────────────┘
                   │
                   ▼
            ┌──────────────┐
            │ Fichier      │
            │ valide ?     │
            └──┬────────┬──┘
               │        │
            Non│        │Oui
               │        │
               ▼        │
    ┌──────────────────┐│
    │ Afficher erreur  ││
    │ de validation    ││
    └──────┬───────────┘│
           │            │
           └────────────┘
               │
               ▼
    ┌──────────────────────────┐
    │ Enregistrer fichier dans │
    │ uploads/rapports         │
    └──────┬───────────────────┘
           │
           ▼
    ┌──────────────────────────┐
    │ Insérer enregistrement    │
    │ dans table rapports       │
    └──────┬───────────────────┘
           │
           ▼
    ┌──────────────────────────┐
    │ Afficher message de      │
    │ succès                   │
    └──────┬───────────────────┘
           │
           ▼
    ┌──────────────────────────┐
    │ Redirection vers          │
    │ suivi_soumissions.php     │
    └──────┬───────────────────┘
           │
           ▼
           ┌─────────────┐
           │    Fin      │
           └─────────────┘
```

---

## 🏗️ 7. Diagramme de Déploiement

```
┌─────────────────────────────────────────────────────────────┐
│                         CLIENT                               │
│                                                              │
│  ┌────────────────────────────────────────────────────┐     │
│  │         Navigateur Web                             │     │
│  │                                                     │     │
│  │  Chrome / Firefox / Edge                           │     │
│  └────────────────────────────────────────────────────┘     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ HTTP/HTTPS
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              SERVEUR WEB (WAMP / Apache)                     │
│                                                              │
│  ┌────────────────────────────────────────────────────┐     │
│  │         Apache HTTP Server                          │     │
│  └────────────────────────────────────────────────────┘     │
│                            │                                 │
│                            ▼                                 │
│  ┌────────────────────────────────────────────────────┐     │
│  │         PHP 8.x                                    │     │
│  └────────────────────────────────────────────────────┘     │
│                            │                                 │
│                            ▼                                 │
│  ┌────────────────────────────────────────────────────┐     │
│  │         Application SEIC LBS                      │     │
│  │                                                     │     │
│  │         (htdocs + app)                             │     │
│  └────────────────────────────────────────────────────┘     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ PDO MySQL
                            │ Opérations fichiers
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    SERVEUR DE DONNÉES                        │
│                                                              │
│  ┌────────────────────────────────────────────────────┐     │
│  │         MySQL / MariaDB                            │     │
│  │                                                     │     │
│  │  Base de données relationnelle                    │     │
│  └────────────────────────────────────────────────────┘     │
│                                                              │
│  ┌────────────────────────────────────────────────────┐     │
│  │         Système de fichiers                       │     │
│  │                                                     │     │
│  │  uploads/rapports                                  │     │
│  │  uploads/presentations                             │     │
│  └────────────────────────────────────────────────────┘     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 📝 Légende des Symboles

### Pour les Diagrammes de Séquence :
- `───>` : Message synchrone (flèche pleine)
- `<───` : Retour (flèche pointillée)
- `│` : Ligne de vie (verticale)
- `┌───┐` : Boîte Alt/Loop (rectangle avec coins arrondis)
- `│   │` : Rectangle d'activation

### Pour les Diagrammes d'Activités :
- `┌───┐` : Activité (rectangle avec coins arrondis)
- `┌───┐` : Décision (losange)
- `●` : Nœud initial (cercle plein)
- `○` : Nœud final (cercle avec bordure épaisse)
- `───>` : Flux de contrôle (flèche)

### Pour les Diagrammes de Classes :
- `┌───┐` : Classe (rectangle avec 3 sections)
- `───>` : Relation (flèche avec label)
- `1`, `0..*` : Multiplicités

### Pour le Diagramme de Déploiement :
- `┌───┐` : Nœud (rectangle avec sous-composants)
- `───>` : Connexion (flèche avec label)

---

**Auteur** : Khalil  
**Version** : 1.0  
**Date** : 2025
