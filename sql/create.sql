-- Base de données EcoRide (relationnelle)

-- Suppression des tables si elles existent déjà (ordre inverse des dépendances)
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS participations;
DROP TABLE IF EXISTS trajets;
DROP TABLE IF EXISTS vehicules;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS employes;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    photo VARCHAR(100) NOT NULL DEFAULT 'defaut.png',
    credits INT DEFAULT 20,
    role ENUM('passager', 'chauffeur', 'les_deux') DEFAULT 'passager',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE
);

-- Table des véhicules
CREATE TABLE vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    immatriculation VARCHAR(20) NOT NULL UNIQUE,
    date_immatriculation DATE NOT NULL,
    marque VARCHAR(50),
    modele VARCHAR(50),
    energie ENUM('essence', 'diesel', 'hybride', 'électrique', 'GPL', 'autre') DEFAULT 'électrique',
    couleur VARCHAR(30),
    nb_places INT DEFAULT 1,
    fumeur BOOLEAN DEFAULT FALSE,
    animaux BOOLEAN DEFAULT FALSE,
    preferences TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Table des trajets
CREATE TABLE trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    adresse_depart VARCHAR(255),
    adresse_arrivee VARCHAR(255),
    date_depart DATETIME,
    duree TIME,
    prix INT NOT NULL,
    nb_places_total INT,
    nb_places_restantes INT,
    statut ENUM('à_venir', 'en_cours', 'termine', 'annulé') DEFAULT 'à_venir',
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
);

-- Table des participations (réservations)
CREATE TABLE participations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    trajet_id INT NOT NULL,
    date_participation DATETIME DEFAULT CURRENT_TIMESTAMP,
    valide BOOLEAN DEFAULT FALSE,
    probleme_signale BOOLEAN DEFAULT FALSE,
    commentaire TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (trajet_id) REFERENCES trajets(id) ON DELETE CASCADE
);

-- Table des avis laissés par les passagers
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participation_id INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    valide BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (participation_id) REFERENCES participations(id) ON DELETE CASCADE
);

-- Table des employés pour la modération
CREATE TABLE employes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE
);
