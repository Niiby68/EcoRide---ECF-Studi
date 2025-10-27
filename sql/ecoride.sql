-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 27 oct. 2025 à 22:26
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `participation_id` int NOT NULL,
  `note` int DEFAULT NULL,
  `commentaire` mediumtext COLLATE utf8mb4_unicode_ci,
  `valide` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `participation_id` (`participation_id`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `employes`
--

DROP TABLE IF EXISTS `employes`;
CREATE TABLE IF NOT EXISTS `employes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varbinary(16) NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `last_attempt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ip` (`ip`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participations`
--

DROP TABLE IF EXISTS `participations`;
CREATE TABLE IF NOT EXISTS `participations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `trajet_id` int NOT NULL,
  `date_participation` datetime DEFAULT CURRENT_TIMESTAMP,
  `valide` tinyint(1) DEFAULT '0',
  `probleme_signale` tinyint(1) DEFAULT '0',
  `commentaire` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_utilisateur_trajet` (`utilisateur_id`,`trajet_id`),
  KEY `trajet_id` (`trajet_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `participations`
--

INSERT INTO `participations` (`id`, `utilisateur_id`, `trajet_id`, `date_participation`, `valide`, `probleme_signale`, `commentaire`) VALUES
(7, 25, 4, '2025-09-25 15:21:46', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `trajets`
--

DROP TABLE IF EXISTS `trajets`;
CREATE TABLE IF NOT EXISTS `trajets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chauffeur_id` int NOT NULL,
  `vehicule_id` int NOT NULL,
  `adresse_depart` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_arrivee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_depart` datetime DEFAULT NULL,
  `duree` time DEFAULT NULL,
  `prix` int NOT NULL,
  `nb_places_total` int DEFAULT NULL,
  `nb_places_restantes` int DEFAULT NULL,
  `statut` enum('à_venir','en_cours','termine','annulé') COLLATE utf8mb4_unicode_ci DEFAULT 'à_venir',
  PRIMARY KEY (`id`),
  KEY `chauffeur_id` (`chauffeur_id`),
  KEY `vehicule_id` (`vehicule_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trajets`
--

INSERT INTO `trajets` (`id`, `chauffeur_id`, `vehicule_id`, `adresse_depart`, `adresse_arrivee`, `date_depart`, `duree`, `prix`, `nb_places_total`, `nb_places_restantes`, `statut`) VALUES
(1, 14, 1, '10 Rue des Lilas, 75019 PARIS', '5 Avenue de Lyon, 13003 MARSEILLE', '2025-10-21 08:30:00', '08:19:00', 30, 4, 2, 'en_cours'),
(2, 15, 2, '50 Rue Nationale, 33000 BORDEAUX', '8 Place Bellecour, 69002 LYON', '2025-07-15 08:30:00', '04:35:00', 28, 3, 2, 'à_venir'),
(3, 16, 3, '11 Avenue Alsace Lorraine, 38000 GRENOBLE', '2 Promenade des Anglais, 06000 NICE', '2025-07-15 08:30:00', '03:19:00', 40, 4, 4, 'à_venir'),
(4, 17, 4, '3 Rue Oberkampf, 75011 PARIS', '45 Boulevard Rabatau, 13008 MARSEILLE', '2025-10-28 08:30:00', '08:42:00', 35, 3, 1, 'en_cours'),
(5, 18, 5, '129 Avenue des Champs-Élysées, 75008 PARIS', '20 Quai du Port, 13002 MARSEILLE', '2025-10-30 08:53:22', '08:22:00', 33, 4, 3, 'en_cours');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'defaut.png',
  `credits` int DEFAULT '20',
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `email`, `mot_de_passe`, `photo`, `credits`, `date_inscription`, `actif`) VALUES
(15, 'sophie33', 'sophie@example.com', 'motdepassehashé', 'defaut.png', 20, '2025-06-30 17:07:57', 1),
(14, 'jean77', 'jean@example.com', 'motdepassehashé', 'defaut.png', 20, '2025-06-30 16:16:42', 1),
(16, 'amine69', 'amine@example.com', 'motdepassehashé', 'defaut.png', 20, '2025-06-30 17:07:57', 1),
(17, 'lea_paris', 'lea@example.com', 'motdepassehashé', 'defaut.png', 20, '2025-06-30 17:11:03', 1),
(18, 'marc_paris', 'marc@example.com', 'motdepassehashé', 'defaut.png', 20, '2025-06-30 17:11:03', 1),
(23, 'Niiby', 'iby.nicolas@neptune-concept.com', '$2y$10$Q.wqpx4o8OX9rzYRx46w.ep96JWjDRhNvUN9JiIl8EPRSUUIFNQAS', 'defaut.png', 100, '2025-08-25 15:37:25', 1),
(25, 'Essai', 'essai@essai.fr', '$2y$10$nnXD6.Qkd8/Rqq.kbBuEbONga7asrqaWwqzRLKyAr49ljkJ4p.fpG', 'defaut.png', 0, '2025-09-23 16:13:53', 1);

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

DROP TABLE IF EXISTS `vehicules`;
CREATE TABLE IF NOT EXISTS `vehicules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `immatriculation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_immatriculation` date NOT NULL,
  `marque` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modele` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `energie` enum('essence','diesel','hybride','électrique','GPL','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'électrique',
  `couleur` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nb_places` int DEFAULT '1',
  `fumeur` tinyint(1) DEFAULT '0',
  `animaux` tinyint(1) DEFAULT '0',
  `preferences` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `immatriculation` (`immatriculation`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `utilisateur_id`, `immatriculation`, `date_immatriculation`, `marque`, `modele`, `energie`, `couleur`, `nb_places`, `fumeur`, `animaux`, `preferences`) VALUES
(1, 1, 'AB-123-CD', '2022-05-10', 'Renault', 'Clio', 'diesel', 'Bleu', 4, 0, 1, 'Pas de musique forte'),
(2, 2, 'CD-456-EF', '2021-03-20', 'Peugeot', '208', 'électrique', 'Rouge', 3, 0, 0, 'Climatisation disponible'),
(3, 3, 'GH-789-IJ', '2023-06-01', 'Volkswagen', 'Golf', 'électrique', 'Gris', 4, 1, 1, 'Musique douce acceptée'),
(4, 4, 'JK-101-LM', '2022-09-15', 'Citroën', 'C3', 'électrique', 'Vert', 3, 0, 0, 'Pause toutes les 2h'),
(5, 5, 'NO-112-PQ', '2020-04-08', 'Toyota', 'Corolla', 'électrique', 'Noir', 4, 1, 0, 'Conduite souple, café offert');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
