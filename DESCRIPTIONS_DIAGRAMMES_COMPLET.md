# 📊 Descriptions Complètes des Diagrammes UML - Plateforme SEIC LBS

Ce document présente une description détaillée de tous les diagrammes UML utilisés pour documenter la plateforme SEIC LBS, incluant les diagrammes de séquence, de classes, d'activités, de déploiement, d'architecture et de flux de données.

---

## 📋 Table des Matières

1. [Diagrammes de Séquence](#diagrammes-de-séquence)
2. [Diagramme de Classes](#diagramme-de-classes)
3. [Diagrammes d'Activités](#diagrammes-dactivités)
4. [Diagramme de Déploiement](#diagramme-de-déploiement)
5. [Diagrammes d'Architecture](#diagrammes-darchitecture)
6. [Diagrammes de Flux de Données](#diagrammes-de-flux-de-données)
7. [Diagramme Entité-Relation](#diagramme-entité-relation)

---

## 🔄 Diagrammes de Séquence

### 1. Diagramme de Séquence - Connexion Utilisateur (Simplifié)

**Description :**  
Ce diagramme de séquence illustre le scénario d'authentification simplifié d'un client sur la plateforme SEIC LBS. L'acteur principal (Client) est positionné à gauche, tandis que les composants du système (login.php, auth.php, Session PHP, MySQL) sont représentés à droite. Le flux temporel vertical montre l'enchaînement chronologique des interactions : de la requête GET initiale pour afficher le formulaire de connexion, jusqu'à la création de la session utilisateur et la redirection vers le tableau de bord en cas de succès, ou l'affichage d'un message d'erreur en cas d'échec d'authentification.

**Acteurs et objets :**
- **Acteur principal** : Client (Navigateur)
- **Objets système** : Frontend (login.php), Auth (app/auth.php), Session PHP, Base de données MySQL

**Flux principal :**
1. Le client accède à la page de connexion via une requête GET
2. Le frontend affiche le formulaire de connexion
3. Le client saisit ses identifiants et soumet le formulaire (POST)
4. Le frontend appelle le module d'authentification
5. Le module auth interroge la base de données pour vérifier l'utilisateur
6. Si les identifiants sont valides : création de la session et redirection vers le dashboard
7. Si les identifiants sont invalides : affichage d'un message d'erreur

**Points clés :**
- Utilisation de requêtes préparées pour la sécurité
- Vérification du mot de passe avec `password_verify()`
- Création de session PHP pour maintenir l'état de connexion
- Gestion des deux cas : succès et échec d'authentification

---

### 2. Diagramme de Séquence - Connexion Complète

**Description :**  
Ce diagramme de séquence illustre le scénario complet de connexion d'un utilisateur à la plateforme SEIC LBS. Contrairement au diagramme simplifié, celui-ci inclut toutes les étapes depuis l'accès initial jusqu'à l'affichage du tableau de bord personnalisé, incluant la vérification de session, la récupération des projets de l'utilisateur et l'affichage du dashboard.

**Acteurs et objets :**
- **Acteur principal** : Utilisateur
- **Objets système** : Navigateur, login.php, auth.php, Session, MySQL, dashboard.php

**Flux principal :**
1. L'utilisateur accède à login.php via le navigateur
2. Le système vérifie l'existence d'une session (session_start())
3. Si aucune session n'existe, affichage du formulaire de connexion
4. L'utilisateur saisit email et mot de passe
5. Vérification en base de données avec requête préparée
6. Comparaison du mot de passe avec le hash stocké
7. **Si correct** :
   - Création de la session avec user_id, user_nom, user_role
   - Redirection HTTP 302 vers dashboard.php
   - Vérification de la session sur dashboard.php
   - Récupération des projets de l'utilisateur depuis la base de données
   - Affichage du dashboard personnalisé
8. **Si incorrect** : Affichage du message d'erreur

**Points clés :**
- Gestion complète du cycle de vie de la session
- Intégration avec le dashboard pour afficher les données utilisateur
- Séparation claire entre authentification et affichage du contenu
- Redirection HTTP 302 pour éviter la resoumission du formulaire

---

### 3. Diagramme de Séquence - Soumission d'un Rapport (Simplifié)

**Description :**  
Ce diagramme de séquence décrit le scénario simplifié d'upload d'un fichier PDF (rapport) par un étudiant. L'acteur principal (Étudiant) est positionné à gauche, tandis que les composants du système (soumettre_rapport.php, soumission_rapport.php, upload_config.php, uploads/rapports/, MySQL) sont représentés à droite. Le flux temporel vertical illustre l'enchaînement chronologique des opérations : de l'accès au formulaire d'upload jusqu'à la validation du fichier, sa sauvegarde sur le système de fichiers, l'enregistrement en base de données, la création de notifications, et enfin l'affichage d'un message de succès ou d'erreur selon le résultat de la validation.

**Acteurs et objets :**
- **Acteur principal** : Étudiant (Navigateur)
- **Objets système** : Frontend (soumettre_rapport.php), Backend (soumission_rapport.php), Config (upload_config.php), FileSystem (uploads/rapports/), Base de données MySQL

**Flux principal :**
1. L'étudiant accède au formulaire de soumission (GET)
2. Vérification de l'authentification
3. Affichage du formulaire avec liste des projets
4. L'étudiant sélectionne un projet et un fichier PDF
5. Soumission via POST multipart/form-data
6. Vérification des limites d'upload (taille, extension)
7. Validation du fichier (extension, type MIME, taille)
8. **Si valide** :
   - Génération d'un nom de fichier unique
   - Sauvegarde sur le système de fichiers
   - Insertion de l'enregistrement en base de données
   - Création de notifications pour les organisateurs
   - Message de succès et redirection
9. **Si invalide** : Affichage d'un message d'erreur

**Points clés :**
- Validation stricte des fichiers (extension, type MIME, taille)
- Génération de noms de fichiers uniques pour éviter les collisions
- Séparation entre stockage fichier et enregistrement base de données
- Création automatique de notifications pour les administrateurs

---

### 4. Diagramme de Séquence - Soumission d'un Rapport (Complet)

**Description :**  
Ce diagramme de séquence décrit le scénario complet de soumission d'un rapport PDF par un étudiant. Il inclut toutes les étapes détaillées : la récupération de la liste des projets de l'étudiant, la validation complète du fichier avec tous les critères, la génération du nom de fichier avec timestamp, l'enregistrement en base avec l'état de validation initialisé à "en_attente", et la création de notifications pour chaque organisateur via une boucle.

**Acteurs et objets :**
- **Acteur principal** : Étudiant
- **Objets système** : Navigateur, soumettre_rapport.php, auth.php, soumission_rapport.php, upload_config.php, uploads/rapports/, MySQL, notification_manager.php

**Flux principal :**
1. Accès au formulaire de soumission
2. Vérification de l'authentification
3. Récupération des projets de l'étudiant depuis la base de données (requête LIKE sur le champ membres)
4. Affichage du formulaire avec liste des projets
5. Sélection du projet et du fichier PDF
6. Traitement de la soumission
7. Vérification des limites d'upload (50MB max)
8. Validation complète :
   - Type MIME : application/pdf
   - Extension : .pdf
   - Taille < 50MB
   - Aucune erreur d'upload
9. **Si valide** :
   - Génération du nom : rapport_[projet_id]_[timestamp].pdf
   - Sauvegarde avec move_uploaded_file()
   - INSERT dans la table rapports avec état "en_attente"
   - Récupération de la liste des organisateurs
   - **Boucle** : Création d'une notification pour chaque organisateur
   - Redirection vers suivi_soumissions.php
10. **Si invalide** : Affichage du message d'erreur

**Points clés :**
- Récupération dynamique des projets de l'étudiant
- Validation multi-critères du fichier
- Génération de noms de fichiers avec timestamp pour l'unicité
- Création de notifications multiples via une boucle
- Gestion de l'état initial "en_attente" pour le workflow de validation

---

### 5. Diagramme de Séquence - Consultation des Projets et Commentaires (Visiteur)

**Description :**  
Ce diagramme de séquence représente la consultation des projets par un visiteur ainsi que l'ajout de commentaires. Il met en évidence l'interaction entre l'acteur « Visiteur » et les composants `visitor_projects.php`, le module de commentaires et la base de données pour l'affichage et l'enregistrement des commentaires.

**Acteurs et objets :**
- **Acteur principal** : Visiteur
- **Objets système** : Navigateur, visitor_projects.php, commentaires.php, Base de données MySQL

**Flux principal :**
1. Le visiteur accède à la page des projets publics
2. Récupération de la liste des projets publiés depuis la base de données
3. **Boucle** : Pour chaque projet, récupération des commentaires associés
   - Appel à la fonction getCommentairesProjet(id_projet)
   - Requête SQL pour récupérer les commentaires du projet
   - Retour de la liste des commentaires
4. Affichage des projets avec leurs commentaires respectifs
5. Le visiteur saisit un nouveau commentaire
6. Soumission du commentaire via POST
7. Appel à addCommentaire() avec les paramètres (id_projet, auteur, contenu)
8. INSERT dans la table commentaires
9. Rechargement ou mise à jour de la liste des commentaires

**Points clés :**
- Accès public sans authentification requise
- Récupération en boucle des commentaires pour chaque projet
- Séparation entre affichage et ajout de commentaires
- Mise à jour dynamique de l'interface après ajout

---

### 6. Diagramme de Séquence - Notifications Temps Réel

**Description :**  
Ce diagramme de séquence illustre le scénario de récupération et de gestion des notifications en temps réel pour un étudiant. L'acteur principal (Étudiant) est positionné à gauche, tandis que les composants du système (dashboard.php, JavaScript, api/notifications.php, notification_manager.php, MySQL) sont représentés à droite. Le flux temporel vertical montre l'enchaînement chronologique des interactions : du chargement initial du tableau de bord jusqu'à la mise à jour périodique (toutes les 30 secondes) des notifications non lues via des requêtes AJAX asynchrones, ainsi que le marquage d'une notification comme lue lorsque l'utilisateur interagit avec elle.

**Acteurs et objets :**
- **Acteur principal** : Étudiant (Navigateur)
- **Objets système** : Dashboard (dashboard.php), JavaScript (setInterval), API (api/notifications.php), NotifManager (notification_manager.php), Base de données MySQL

**Flux principal :**
1. Chargement initial du dashboard avec HTML et JavaScript
2. **Boucle périodique** (toutes les 30 secondes) :
   - Requête AJAX GET vers l'API de notifications
   - Vérification de la session utilisateur
   - Appel à getUnreadNotifications() avec limite de 5
   - Requête SQL pour récupérer les notifications non lues (lu = 0)
   - Retour JSON avec la liste des notifications et le compteur
   - Mise à jour du badge de notification
   - Affichage de la liste des notifications
3. **Action utilisateur** : Marquage d'une notification comme lue
   - Requête POST vers l'API avec l'ID de la notification
   - Appel à markAsRead()
   - UPDATE SQL pour marquer la notification comme lue
   - Retour de confirmation

**Points clés :**
- Mise à jour automatique périodique via setInterval JavaScript
- Communication asynchrone AJAX pour ne pas bloquer l'interface
- Limitation à 5 notifications pour optimiser les performances
- Séparation entre récupération et marquage des notifications
- Format JSON pour l'échange de données

---

## 📊 Diagramme de Classes

**Description :**  
Le diagramme de classes ci-dessous présente la structure statique du système SEIC LBS. Il met en évidence les principales entités (utilisateurs, projets, rapports, présentations, commentaires, etc.), leurs attributs ainsi que les relations entre elles (soumission, appartenance à une édition, regroupement en groupes, utilisation de salles).

**Classes principales :**

1. **Utilisateur**
   - Attributs : id (PK), nom, email (unique), mot_de_passe (hashé), role (enum : étudiant, visiteur, organisateur)
   - Relations : peut soumettre plusieurs rapports et présentations

2. **Projet**
   - Attributs : id (PK), nom, description, categorie, membres (liste des membres), id_edition (FK)
   - Relations : appartient à une édition, peut avoir plusieurs rapports et présentations, reçoit des commentaires

3. **Rapport**
   - Attributs : id (PK), id_projet (FK), id_utilisateur (FK), date_soumission, fichier (chemin), etat_validation, commentaire_admin
   - Relations : appartient à un projet, soumis par un utilisateur

4. **Presentation**
   - Attributs : id (PK), id_projet (FK), id_utilisateur (FK), date_soumission, fichier (chemin), etat_validation, commentaire_admin
   - Relations : appartient à un projet, soumise par un utilisateur

5. **Commentaire**
   - Attributs : id (PK), id_projet (FK), auteur, contenu, date_creation
   - Relations : associé à un projet

6. **Edition**
   - Attributs : id (PK), annee, description, date_debut, date_fin
   - Relations : contient plusieurs projets et groupes

7. **Groupe**
   - Attributs : id (PK), nom, id_edition (FK), id_salle (FK), nombre_membres
   - Relations : appartient à une édition, utilise une salle, regroupe des projets

8. **Salle**
   - Attributs : id (PK), nom, localisation
   - Relations : utilisée par des groupes

**Relations principales :**
- Un utilisateur peut soumettre plusieurs rapports et présentations (1 → 0..*)
- Un projet possède plusieurs rapports et présentations (1 → 0..*)
- Un projet reçoit plusieurs commentaires (1 → 0..*)
- Une édition contient plusieurs projets et groupes (1 → 0..*)
- Un groupe regroupe plusieurs projets (1 → 0..*)
- Un groupe se déroule dans une salle (1 → 1)

**Points clés :**
- Modélisation complète de la structure de données
- Relations clairement définies avec cardinalités
- Clés primaires (PK) et étrangères (FK) identifiées
- Support des différents rôles utilisateurs
- Gestion des états de validation pour les soumissions

---

## 🔄 Diagrammes d'Activités

### 1. Diagramme d'Activités - Connexion d'un Utilisateur

**Description :**  
Le diagramme d'activités suivant modélise le déroulement du processus de connexion d'un utilisateur. Il décrit les différentes étapes : saisie des identifiants, contrôle de validité, vérification en base de données, création de la session et redirection vers le tableau de bord en cas de succès.

**Étapes principales :**
1. **Début** : Point d'entrée du processus
2. **Ouvrir page login.php** : Accès à la page de connexion
3. **Saisir email et mot de passe** : Saisie des identifiants par l'utilisateur
4. **Cliquer sur 'Se connecter'** : Déclenchement du processus d'authentification
5. **Contrôler format des champs** : Validation côté client/serveur
   - **Si invalide** : Retour à la saisie avec message d'erreur
6. **Rechercher utilisateur par email en base** : Requête SQL
   - **Si non trouvé** : Affichage du message d'erreur et retour à la saisie
7. **Comparer mot de passe avec hash** : Vérification avec password_verify()
   - **Si incorrect** : Affichage du message d'erreur et retour à la saisie
   - **Si correct** : Création de la session utilisateur
8. **Redirection vers dashboard.php** : Navigation vers le tableau de bord
9. **Afficher tableau de bord** : Affichage du contenu personnalisé
10. **Fin** : Point de sortie du processus

**Points clés :**
- Gestion des cas d'erreur avec retours vers la saisie
- Validation en plusieurs étapes (format, existence, mot de passe)
- Création de session uniquement en cas de succès
- Redirection automatique vers le dashboard

---

### 2. Diagramme d'Activités - Soumission d'un Rapport

**Description :**  
Ce diagramme d'activités représente le processus de soumission d'un rapport par un étudiant. Il montre les principales étapes du flux : sélection du projet, choix du fichier, vérifications techniques (taille, format), enregistrement du fichier et insertion de l'enregistrement dans la table des rapports.

**Étapes principales :**
1. **Début** : Point d'entrée du processus
2. **Étudiant connecté** : Prérequis d'authentification
3. **Ouvrir page soumettre_rapport.php** : Accès au formulaire de soumission
4. **Sélectionner projet** : Choix du projet associé au rapport
5. **Choisir fichier PDF** : Sélection du fichier à uploader
6. **Cliquer sur 'Soumettre'** : Déclenchement du processus d'upload
7. **Contrôler extension, taille, type MIME** : Validation technique du fichier
   - **Si invalide** : Affichage de l'erreur et retour à la sélection du fichier
8. **Enregistrer fichier dans uploads/rapports** : Sauvegarde sur le système de fichiers
9. **Insérer enregistrement dans table rapports** : Enregistrement en base de données
10. **Afficher message de succès** : Confirmation à l'utilisateur
11. **Redirection vers suivi_soumissions.php** : Navigation vers la page de suivi
12. **Fin** : Point de sortie du processus

**Points clés :**
- Validation stricte du fichier avant sauvegarde
- Séparation entre stockage fichier et enregistrement base de données
- Gestion des erreurs avec possibilité de réessayer
- Confirmation visuelle du succès de l'opération

---

## 🏗️ Diagramme de Déploiement

**Description :**  
Le diagramme de déploiement ci-dessous présente l'architecture physique de la solution SEIC LBS. Il distingue le poste client (navigateur web), le serveur web (Apache/PHP hébergeant l'application) et le serveur de données (MySQL/MariaDB et système de fichiers pour le stockage des rapports et présentations).

**Composants principaux :**

1. **Client**
   - **Navigateur Web** : Chrome, Firefox, Edge
   - Communication via HTTP/HTTPS avec le serveur web

2. **Serveur Web (WAMP / Apache)**
   - **Apache HTTP Server** : Serveur web gérant les requêtes HTTP
   - **PHP 8.x** : Interpréteur PHP pour l'exécution du code backend
   - **Application SEIC LBS** : Code source de l'application (htdocs + app)
   - Communication avec le serveur de données via PDO

3. **Serveur de Données**
   - **MySQL / MariaDB** : Base de données relationnelle stockant toutes les données structurées
   - **Système de fichiers** : Stockage physique des fichiers uploadés (rapports, présentations)

**Flux de communication :**
- Client → Serveur Web : Requêtes HTTP/HTTPS
- Serveur Web → Serveur de Données : Requêtes SQL via PDO
- Serveur Web → Système de fichiers : Opérations d'écriture/lecture de fichiers

**Points clés :**
- Architecture client-serveur classique
- Séparation entre traitement (PHP) et stockage (MySQL + fichiers)
- Communication sécurisée via HTTPS possible
- Utilisation de PDO pour l'accès à la base de données

---

## 🏛️ Diagrammes d'Architecture

### 1. Architecture Globale

**Description :**  
Ce diagramme présente l'architecture globale de la plateforme SEIC LBS, montrant l'organisation complète des composants depuis le client jusqu'au stockage des données. Il illustre la séparation entre frontend et backend, ainsi que les différentes couches technologiques utilisées.

**Couches principales :**

1. **CLIENT (Navigateur)**
   - Navigateur Web (Chrome, Firefox, Edge)
   - HTML5 pour la structure des pages
   - CSS3 + Bootstrap 5.3 pour le design responsive
   - JavaScript pour les interactions et AJAX
   - Font Awesome 6.4 pour les icônes

2. **SERVEUR WEB (Apache/WAMP)**
   - Apache Server sur port 80/8080
   - PHP 8.0+ comme interpréteur

3. **FRONTEND (htdocs/)**
   - Pages PHP (index.php, login.php, dashboard.php, soumettre_*.php)
   - API REST (api/notifications.php)
   - Ressources statiques (css/, images/, js/)

4. **BACKEND (app/)**
   - auth.php : Authentification & Sessions
   - Logique Métier : soumission_*.php, rapports_admin.php, gestion_*.php
   - notification_manager.php : Système de notifications

5. **CONFIGURATION**
   - config/config.php : Configuration BDD & Chemins
   - upload_config.php : Limites uploads

6. **BASE DE DONNÉES**
   - MySQL/MariaDB avec tables principales :
     - utilisateurs, projets, rapports, presentations
     - notifications, editions, groupes, salles

7. **STOCKAGE**
   - uploads/ : Dossiers pour rapports et presentations

**Flux de données :**
- Navigateur → Apache → PHP → Pages
- Pages → Modules Backend → Configuration → Base de données
- Backend → Système de fichiers (uploads)

**Points clés :**
- Séparation claire frontend/backend
- Utilisation de PDO pour l'accès à la base de données
- API REST pour les communications asynchrones
- Configuration centralisée

---

### 2. Structure des Modules Backend

**Description :**  
Ce diagramme illustre l'organisation modulaire du backend de la plateforme SEIC LBS, montrant les différents modules et leurs dépendances.

**Modules principaux :**

1. **MODULE AUTHENTIFICATION**
   - auth.php : Gestion de l'authentification
   - session_manager.php : Gestion des sessions

2. **MODULE SOUMISSIONS**
   - soumission_projet.php : Soumission de projets
   - soumission_rapport.php : Soumission de rapports
   - soumission_powerpoint.php : Soumission de présentations
   - suivi_soumissions.php : Suivi des soumissions

3. **MODULE ADMINISTRATION**
   - rapports_admin.php : Gestion des rapports
   - gestion_powerpoint.php : Gestion des présentations
   - projets.php : Gestion des projets
   - editions.php : Gestion des éditions
   - groupes_salles.php : Gestion des groupes et salles

4. **MODULE VISUALISATION**
   - visitor_projects.php : Visualisation pour les visiteurs
   - projets_edition.php : Projets par édition

5. **MODULE NOTIFICATIONS**
   - notification_manager.php : Gestion des notifications

6. **CONFIGURATION**
   - config/config.php : Configuration générale
   - upload_config.php : Configuration des uploads

**Dépendances :**
- Tous les modules de soumission et d'administration dépendent du module d'authentification
- Le module de notifications dépend de la configuration
- Les modules de soumission dépendent de la configuration d'upload

**Points clés :**
- Architecture modulaire facilitant la maintenance
- Réutilisation du module d'authentification
- Séparation des responsabilités par module

---

### 3. Interactions Frontend-Backend

**Description :**  
Ce diagramme montre les interactions entre les composants frontend et backend, ainsi que les mécanismes de communication utilisés (HTTP, AJAX, Sessions, PDO).

**Composants Frontend :**
- Pages interactives : login.php, dashboard.php, soumettre_rapport.php, rapports_admin.php, suivi_soumissions.php
- API REST : api/notifications.php

**Composants Backend :**
- Authentification : app/auth.php
- Logique métier : app/soumission_rapport.php, app/rapports_admin.php
- Notifications : app/notification_manager.php

**Mécanismes de communication :**
- **HTTP/HTTPS** : Requêtes GET/POST pour les pages classiques
- **AJAX/Fetch API** : Requêtes asynchrones pour les notifications
- **Session PHP** : Maintien de l'état utilisateur ($_SESSION)
- **PDO MySQL** : Requêtes SQL sécurisées

**Flux d'interaction :**
- Login → HTTP POST → Auth → Session + PDO
- Dashboard → AJAX → API → Notif → PDO
- Submit → HTTP POST multipart → SubmitLogic → Auth + PDO + Notif
- Admin → HTTP POST → AdminLogic → Auth + PDO + Notif
- Suivi → HTTP GET → Auth → Session

**Points clés :**
- Utilisation de différents protocoles selon le besoin
- Sécurité via sessions et requêtes préparées
- Communication asynchrone pour améliorer l'expérience utilisateur

---

### 4. Stack Technologique

**Description :**  
Ce diagramme présente la pile technologique complète utilisée dans la plateforme SEIC LBS, organisée par couches (client, serveur, base de données, architecture).

**CLIENT-SIDE :**
- HTML5 : Structure des pages
- CSS3 + Bootstrap 5.3 : Design responsive
- Vanilla JavaScript : Interactions côté client
- Font Awesome 6.4 : Bibliothèque d'icônes

**SERVER-SIDE :**
- PHP 8.0+ : Langage de programmation backend
- Apache Server : Serveur HTTP
- PDO Extension : Accès à la base de données

**DATABASE :**
- MySQL/MariaDB : Système de gestion de base de données relationnelle

**ARCHITECTURE :**
- Pattern MVC : Séparation des responsabilités (Modèle-Vue-Contrôleur)
- REST API : Architecture API pour les communications asynchrones
- Session Management : Gestion de l'authentification

**Points clés :**
- Stack technologique moderne et standard
- Utilisation de frameworks et bibliothèques éprouvés
- Architecture respectant les bonnes pratiques

---

## 📦 Diagramme Entité-Relation

**Description :**  
Ce diagramme entité-relation (ERD) modélise la structure complète de la base de données de la plateforme SEIC LBS, montrant toutes les entités, leurs attributs et les relations entre elles.

**Entités principales :**

1. **utilisateurs**
   - Clé primaire : id
   - Attributs : nom, email (unique), mot_de_passe, role (enum)
   - Relations : crée des projets, soumet des rapports et présentations, reçoit des notifications

2. **projets**
   - Clé primaire : id
   - Attributs : nom, description, categorie, membres, id_edition (FK)
   - Relations : appartient à une édition, a des rapports et présentations, appartient à un groupe

3. **rapports**
   - Clé primaire : id
   - Attributs : id_projet (FK), date_soumission, fichier, etat_validation, commentaire
   - Relations : appartient à un projet

4. **presentations**
   - Clé primaire : id
   - Attributs : id_projet (FK), date_soumission, fichier, etat_validation, commentaire
   - Relations : appartient à un projet

5. **notifications**
   - Clé primaire : id
   - Attributs : user_id (FK), title, message, type (enum), lu (boolean), created_at
   - Relations : reçue par un utilisateur

6. **editions**
   - Clé primaire : id
   - Attributs : annee, description, date_debut, date_fin
   - Relations : contient des projets et groupes

7. **groupes**
   - Clé primaire : id
   - Attributs : nom, id_edition (FK), id_salle (FK), nombre_membres
   - Relations : appartient à une édition, utilise une salle

8. **salles**
   - Clé primaire : id
   - Attributs : nom, localisation
   - Relations : utilisée par des groupes

**Relations principales :**
- utilisateurs ||--o{ projets : "crée"
- utilisateurs ||--o{ rapports : "soumet"
- utilisateurs ||--o{ presentations : "soumet"
- utilisateurs ||--o{ notifications : "reçoit"
- editions ||--o{ projets : "contient"
- editions ||--o{ groupes : "contient"
- projets ||--o{ rapports : "a"
- projets ||--o{ presentations : "a"
- projets }o--|| groupes : "appartient à"
- groupes }o--|| salles : "utilise"

**Points clés :**
- Modélisation complète de toutes les relations
- Cardinalités clairement définies
- Clés primaires et étrangères identifiées
- Support des différents types de données (enum, boolean, datetime)

---

## 📝 Conclusion

Ce document présente une vue complète de tous les diagrammes UML utilisés pour documenter la plateforme SEIC LBS. Ces diagrammes couvrent :

- **Les interactions dynamiques** : Diagrammes de séquence et d'activités
- **La structure statique** : Diagramme de classes et entité-relation
- **L'architecture système** : Diagrammes d'architecture globale, modules, et déploiement
- **Les flux de données** : Diagrammes de flux et interactions frontend-backend

Ces diagrammes permettent une compréhension complète du système, de sa conception à son déploiement, facilitant ainsi la maintenance, l'évolution et la documentation du projet.

---

**Auteur** : Khalil  
**Version** : 1.0  
**Date** : 2025
