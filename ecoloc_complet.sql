-- ============================================================
--  ECOLOC — Fichier SQL complet
--  À retenir pour le DS : création des tables, insertions,
--  et toutes les requêtes utilisées dans les DAO
-- ============================================================


-- ============================================================
-- 1. CRÉATION DE LA BASE DE DONNÉES
-- ============================================================

CREATE DATABASE IF NOT EXISTS ecoloc
    CHARACTER SET utf8mb4       -- supporte les accents, émojis
    COLLATE utf8mb4_unicode_ci; -- tri/comparaison insensible à la casse

USE ecoloc;


-- ============================================================
-- 2. CRÉATION DES TABLES
-- ============================================================

-- ---- Table CATEGORIE ----------------------------------------
-- Stocke les catégories de matériel (Outillage, Jardinage…)
CREATE TABLE categorie (
    id_categorie  INT          NOT NULL AUTO_INCREMENT, -- clé primaire auto-incrémentée
    nom_categorie VARCHAR(100) NOT NULL,                -- nom de la catégorie
    PRIMARY KEY (id_categorie)
);

-- ---- Table UTILISATEUR --------------------------------------
-- Stocke tous les utilisateurs (visiteurs, abonnés, admins)
CREATE TABLE utilisateur (
    id_utilisateur INT          NOT NULL AUTO_INCREMENT,
    nom            VARCHAR(100) NOT NULL,
    prenom         VARCHAR(100) NOT NULL,
    telephone      VARCHAR(20)  DEFAULT NULL,
    adresse        TEXT         DEFAULT NULL,
    email          VARCHAR(150) NOT NULL UNIQUE,        -- UNIQUE : pas deux fois le même email
    mot_de_passe   VARCHAR(255) NOT NULL,               -- haché avec bcrypt (jamais en clair)
    role           ENUM('visiteur', 'abonne', 'admin') NOT NULL DEFAULT 'visiteur',
    statut_compte  ENUM('en_attente', 'valide')        NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (id_utilisateur)
);

-- ---- Table MATERIEL -----------------------------------------
-- Stocke tout le matériel disponible à l'emprunt
CREATE TABLE materiel (
    id_materiel    INT          NOT NULL AUTO_INCREMENT,
    nom_materiel   VARCHAR(200) NOT NULL,
    caracteristique TEXT        DEFAULT NULL,           -- description détaillée
    duree_pret_max INT          NOT NULL DEFAULT 7,     -- durée max en jours
    disponible     TINYINT(1)   NOT NULL DEFAULT 1,     -- 1 = disponible, 0 = non disponible
    etat           ENUM('neuf', 'bon_etat', 'etat_correct', 'abime') NOT NULL DEFAULT 'neuf',
    id_proprietaire INT         NOT NULL,               -- clé étrangère → utilisateur
    id_categorie   INT          NOT NULL,               -- clé étrangère → categorie
    image          VARCHAR(255) DEFAULT NULL,           -- nom du fichier image (ex: perceuse.png)
    PRIMARY KEY (id_materiel),
    FOREIGN KEY (id_proprietaire) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_categorie)    REFERENCES categorie(id_categorie)
);

-- ---- Table EMPRUNT ------------------------------------------
-- Enregistre chaque demande d'emprunt d'un abonné
CREATE TABLE emprunt (
    id_emprunt          INT  NOT NULL AUTO_INCREMENT,
    date_demande        DATE NOT NULL,                  -- date où l'emprunt a été demandé
    date_debut          DATE DEFAULT NULL,              -- date de début réelle (après validation)
    date_fin_prevue     DATE DEFAULT NULL,              -- date de retour prévue
    date_retour_effectif DATE DEFAULT NULL,             -- date de retour réelle
    statut              ENUM('en_attente', 'accepte', 'en_cours', 'refuse', 'termine')
                            NOT NULL DEFAULT 'en_attente',
    id_emprunteur       INT NOT NULL,                   -- clé étrangère → utilisateur
    id_materiel         INT NOT NULL,                   -- clé étrangère → materiel
    PRIMARY KEY (id_emprunt),
    FOREIGN KEY (id_emprunteur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_materiel)   REFERENCES materiel(id_materiel)
);


-- ============================================================
-- 3. INSERTION DES DONNÉES
-- ============================================================

-- ---- Catégories ---------------------------------------------
INSERT INTO categorie (nom_categorie) VALUES
    ('Outillage'),
    ('Jardinage'),
    ('Informatique'),
    ('Sport'),
    ('Bricolage');

-- ---- Utilisateurs -------------------------------------------
-- Note : mot_de_passe haché avec password_hash($mdp, PASSWORD_BCRYPT) en PHP
-- Le hash ci-dessous correspond au mot de passe "motdepasse123"
INSERT INTO utilisateur (nom, prenom, telephone, adresse, email, mot_de_passe, role, statut_compte) VALUES
    ('Dupont',  'Jean',   '0601010101', '1 rue de la Paix, Paris',      'jean.dupont@mail.com',    '$2y$10$examplehashAdminJean...', 'admin',   'valide'),
    ('Martin',  'Sophie', '0602020202', '5 avenue des Fleurs, Lyon',    'sophie.martin@mail.com',  '$2y$10$examplehashSophie....',   'abonne',  'valide'),
    ('Bernard', 'Lucas',  '0603030303', '12 bd Voltaire, Marseille',    'lucas.bernard@mail.com',  '$2y$10$examplehashLucas.....',   'abonne',  'valide'),
    ('Petit',   'Emma',   '0604040404', '3 rue du Moulin, Bordeaux',    'emma.petit@mail.com',     '$2y$10$examplehashEmma......',   'abonne',  'valide'),
    ('Durand',  'Paul',   NULL,         NULL,                            'paul.durand@mail.com',    '$2y$10$examplehashPaul......',   'visiteur','valide'),
    ('ECO',     'LOC',    NULL,         'Siège social EcoLoc',          'admin@ecoloc.fr',         '$2y$10$examplehashAdmin....',    'admin',   'valide');

-- ---- Matériels ----------------------------------------------
INSERT INTO materiel (nom_materiel, caracteristique, duree_pret_max, disponible, etat, id_proprietaire, id_categorie, image) VALUES
    -- Catégorie Outillage (id=1)
    ('Perceuse Bosch',               'Perceuse visseuse 18V sans fil',                                                                 7,  1, 'bon_etat',    2, 1, 'perceuse.png'),
    ('Scie circulaire',              'Scie 1200W lame 190mm',                                                                          5,  1, 'bon_etat',    2, 5, 'scie.png'),
    ('Marteau perforateur Makita',   'Perforateur SDS+ 800W, 3 modes : perçage, percussion et burin. Livré avec 3 forets béton et mallette.', 5, 1, 'neuf',  3, 1, 'marteau_perforateur.jpg'),
    ('Meuleuse d\'angle Bosch 125mm','Meuleuse 1400W diamètre 125mm. Coupe carrelage, métal, pierre. Disques de coupe et ponçage inclus.',    3, 1, 'bon_etat', 2, 1, 'meuleuse.png'),
    ('Visseuse sans fil DeWalt 18V', 'Visseuse-perceuse 18V, 2 batteries Li-Ion, couple max 65 Nm. Chargeur rapide et coffret inclus.',       7, 1, 'bon_etat', 4, 1, 'visseuse_dewalt.jpg'),
    ('Sableuse orbitale Makita',     'Ponceuse orbitale 300W, semelle 125mm. Livrée avec 10 abrasifs grain 80 à 240.',                        5, 1, 'bon_etat', 3, 5, 'sableuse.jpg'),

    -- Catégorie Jardinage (id=2)
    ('Tondeuse électrique',          'Tondeuse 1800W coupe 42cm',                                                                      5,  0, 'etat_correct', 2, 2, 'tondeuse_electrique.png'),
    ('Aspirateur souffleur',         'Souffleur feuilles 3000W',                                                                       3,  0, 'neuf',         3, 2, 'aspirateur_souffleur.png'),
    ('Taille-haie électrique Bosch', 'Taille-haie 500W, lame 50cm, coupe jusqu\'à 20mm d\'épaisseur. Idéal haies et buissons.',       7,  1, 'bon_etat',     4, 2, 'taille_haie.jpg'),
    ('Nettoyeur haute pression Kärcher K4', 'Nettoyeur 1800W, pression max 130 bars. Parfait terrasses, voitures, façades.',          5,  1, 'etat_correct', 3, 2, 'karcher_k4.jpg'),
    ('Débroussailleuse thermique',   'Débroussailleuse 25cc à fil et lame. Légère (5kg), harnais inclus.',                            5,  1, 'bon_etat',     8, 2, 'debroussailleuse.jpg'),

    -- Catégorie Informatique (id=3)
    ('Imprimante laser HP LaserJet', 'Imprimante A4 noir & blanc, 30 ppm, WiFi direct. Compatible Mac/Windows/Linux.',                7,  1, 'bon_etat',     4, 3, 'imprimante_laser.jpg'),

    -- Catégorie Sport (id=4)
    ('Vélo de route 28 pouces',      'Cadre aluminium 21 vitesses, freins à disque, taille 54cm. Adapté adulte 170-185cm.',           14, 1, 'bon_etat',     8, 4, 'velo_route.jpg'),
    ('Tente camping 4 places Coleman','Tente igloo 4 personnes, montage rapide 10 min, imperméable 3000mm HH.',                       10, 1, 'neuf',          2, 4, 'tente_camping.jpg');


-- ============================================================
-- 4. REQUÊTES SELECT — UtilisateurDAO
-- ============================================================

-- Récupérer TOUS les utilisateurs
SELECT * FROM utilisateur;

-- Récupérer un utilisateur par son ID
SELECT * FROM utilisateur WHERE id_utilisateur = 1;

-- Récupérer un utilisateur par son email (utilisé lors de la connexion)
SELECT * FROM utilisateur WHERE email = 'jean.dupont@mail.com';


-- ============================================================
-- 5. REQUÊTES SELECT — MaterielDAO
-- ============================================================

-- Récupérer TOUS les matériels
SELECT * FROM materiel;

-- Récupérer un matériel par son ID
SELECT * FROM materiel WHERE id_materiel = 1;

-- Récupérer les matériels d'un propriétaire donné
SELECT * FROM materiel WHERE id_proprietaire = 2;

-- Recherche par mot-clé dans le nom (LIKE avec %)
-- Le % veut dire "n'importe quoi avant ou après"
SELECT * FROM materiel WHERE nom_materiel LIKE '%perceuse%';

-- Recherche par mot-clé ET par catégorie
SELECT * FROM materiel
WHERE nom_materiel LIKE '%perceuse%'
  AND id_categorie = 1;

-- Vérifier si un matériel est disponible
SELECT disponible FROM materiel WHERE id_materiel = 1;


-- ============================================================
-- 6. REQUÊTES SELECT — EmpruntDAO
-- ============================================================

-- Récupérer tous les emprunts (du plus récent au plus ancien)
SELECT * FROM emprunt ORDER BY date_demande DESC;

-- Récupérer un emprunt par son ID
SELECT * FROM emprunt WHERE id_emprunt = 1;

-- Récupérer les emprunts d'un abonné donné
SELECT * FROM emprunt WHERE id_emprunteur = 3 ORDER BY date_demande DESC;

-- Récupérer les emprunts d'un matériel donné
SELECT * FROM emprunt WHERE id_materiel = 1;

-- Vérifier si un matériel est déjà emprunté (en cours ou en attente)
-- Si COUNT = 0, le matériel est disponible à l'emprunt
SELECT COUNT(*) AS nb FROM emprunt
WHERE id_materiel = 1
  AND statut IN ('en_attente', 'accepte', 'en_cours');


-- ============================================================
-- 7. REQUÊTES INSERT
-- ============================================================

-- Créer un nouvel emprunt (statut par défaut = en_attente)
INSERT INTO emprunt (date_demande, statut, id_emprunteur, id_materiel)
VALUES ('2026-05-06', 'en_attente', 3, 1);


-- ============================================================
-- 8. REQUÊTES UPDATE
-- ============================================================

-- ---- Gestion des utilisateurs ----

-- Valider un compte (passe de 'en_attente' à 'valide')
UPDATE utilisateur
SET statut_compte = 'valide'
WHERE id_utilisateur = 5;

-- Modifier les infos d'un utilisateur
UPDATE utilisateur
SET nom = 'Dupont', prenom = 'Jean', telephone = '0601010101',
    adresse = '1 rue de la Paix', email = 'jean@mail.com',
    statut_compte = 'valide'
WHERE id_utilisateur = 1;

-- ---- Gestion des matériels ----

-- Modifier un matériel (infos + disponibilité)
UPDATE materiel
SET nom_materiel = 'Perceuse Bosch Pro', caracteristique = 'Nouvelle description',
    duree_pret_max = 10, disponible = 1, etat = 'neuf',
    id_categorie = 1, image = 'perceuse.png'
WHERE id_materiel = 1;

-- Changer uniquement la disponibilité d'un matériel
UPDATE materiel SET disponible = 0 WHERE id_materiel = 1; -- marquer comme indisponible
UPDATE materiel SET disponible = 1 WHERE id_materiel = 1; -- marquer comme disponible

-- Ajouter/modifier l'image d'un matériel
UPDATE materiel SET image = 'marteau_perforateur.jpg' WHERE id_materiel = 12;

-- ---- Gestion des emprunts ----

-- Valider un emprunt : il passe en 'en_cours', on enregistre la date de début
UPDATE emprunt
SET statut = 'en_cours', date_debut = '2026-05-06'
WHERE id_emprunt = 1;

-- Refuser un emprunt
UPDATE emprunt
SET statut = 'refuse'
WHERE id_emprunt = 1;

-- Enregistrer le retour d'un matériel : statut 'termine' + date de retour réelle
UPDATE emprunt
SET statut = 'termine', date_retour_effectif = '2026-05-06'
WHERE id_emprunt = 1;


-- ============================================================
-- 9. REQUÊTES DELETE
-- ============================================================

-- Supprimer un utilisateur par son ID
DELETE FROM utilisateur WHERE id_utilisateur = 5;

-- Supprimer un matériel par son ID
DELETE FROM materiel WHERE id_materiel = 1;


-- ============================================================
-- 10. RÉCAPITULATIF DES STATUTS (à retenir pour le DS)
-- ============================================================

-- statut_compte (utilisateur) :
--   en_attente → l'admin n'a pas encore validé le compte
--   valide      → l'utilisateur peut se connecter

-- statut (emprunt) :
--   en_attente → demande faite, pas encore traitée par l'admin
--   accepte    → accepté mais pas encore récupéré (peu utilisé ici)
--   en_cours   → matériel parti chez l'emprunteur
--   refuse     → demande refusée par l'admin
--   termine    → matériel rendu, emprunt clôturé

-- etat (materiel) :
--   neuf          → jamais utilisé
--   bon_etat      → légères traces d'utilisation
--   etat_correct  → usé mais fonctionnel
--   abime         → endommagé

-- role (utilisateur) :
--   visiteur → peut voir le catalogue, ne peut pas emprunter
--   abonne   → peut emprunter du matériel
--   admin    → gère tout (utilisateurs, matériels, emprunts)
