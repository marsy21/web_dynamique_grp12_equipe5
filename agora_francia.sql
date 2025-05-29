-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 29, 2025 at 07:05 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agora_francia`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrateurs`
--

DROP TABLE IF EXISTS `administrateurs`;
CREATE TABLE IF NOT EXISTS `administrateurs` (
  `id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_vendeur` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `prix_initial` decimal(10,2) DEFAULT NULL,
  `categorie` ENUM('Meubles et objets d’art', 'Accessoire VIP', 'Materiels scolaires') DEFAULT NULL,
  `rarete` ENUM('Rares', 'Haut de gamme', 'Réguliers') DEFAULT NULL,
  `type_vente` enum('meilleure offre','negociation','immediate') DEFAULT 'immediate',
  `qualite` varchar(255) DEFAULT NULL,
  `defaut` varchar(255) DEFAULT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `vendu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_vendeur` (`id_vendeur`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `articles`
--
INSERT INTO `articles` (`id`, `id_vendeur`, `nom`, `description`, `prix_initial`, `categorie`, `rarete`, `type_vente`, `qualite`, `defaut`, `date_publication`, `vendu`) VALUES
(1, 1, 'Appareil photo', 'Appareil photo ancien de collection', 120.00, 'Accessoire VIP', 'Haut de gamme', 'negociation', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(2, 1, 'Commode blanche', 'Commode blanche style vintage', 150.00, 'Meubles et objets d’art', 'Rares', 'meilleure offre', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(3, 1, 'Commode bois', 'Commode en bois massif', 160.00, 'Meubles et objets d’art', 'Rares', 'immediate', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(4, 1, 'Commode verte', 'Commode verte originale', 145.00, 'Meubles et objets d’art', 'Rares', 'negociation', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(5, 1, 'Disque vinyle', 'Tourne-disque rétro', 80.00, 'Accessoire VIP', 'Haut de gamme', 'meilleure offre', 'Fonctionnelle', NULL, '2025-05-29 09:16:52', 0),
(6, 1, 'Fauteuil blanc', 'Fauteuil blanc chic et confortable', 95.00, 'Meubles et objets d’art', 'Rares', 'negociation', 'Neuf', NULL, '2025-05-29 09:16:52', 0),
(7, 1, 'Machine à écrire', 'Machine à écrire vintage', 110.00, 'Accessoire VIP', 'Haut de gamme', 'immediate', 'Fonctionnelle', NULL, '2025-05-29 09:16:52', 0),
(8, 1, 'Machine à coudre', 'Machine à coudre ancienne', 130.00, 'Meubles et objets d’art', 'Haut de gamme', 'meilleure offre', 'Fonctionnelle', NULL, '2025-05-29 09:16:52', 0),
(9, 1, 'Micro', 'Microphone ancien', 75.00, 'Accessoire VIP', 'Haut de gamme', 'negociation', 'Correcte', NULL, '2025-05-29 09:16:52', 0),
(10, 1, 'Montre cuir', 'Montre à bracelet cuir', 60.00, 'Accessoire VIP', 'Réguliers', 'immediate', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(11, 1, 'Montre or', 'Montre à bracelet doré', 85.00, 'Accessoire VIP', 'Réguliers', 'meilleure offre', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(12, 1, 'Montre bleue', 'Montre moderne bleue', 55.00, 'Accessoire VIP', 'Réguliers', 'negociation', 'Neuf', NULL, '2025-05-29 09:16:52', 0),
(13, 1, 'Peinture 1', 'Peinture ancienne encadrée', 200.00, 'Meubles et objets d’art', 'Réguliers', 'immediate', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(14, 1, 'Peinture 2', 'Peinture paysage ancienne', 210.00, 'Meubles et objets d’art', 'Réguliers', 'meilleure offre', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(15, 1, 'Peinture 3', 'Peinture bord de lac', 220.00, 'Meubles et objets d’art', 'Réguliers', 'immediate', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(16, 1, 'Pièce ancienne 1', 'Pièce de monnaie ancienne', 300.00, 'Accessoire VIP', 'Réguliers', 'negociation', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(17, 1, 'Pièce ancienne 2', 'Pièce rare de collection', 310.00, 'Accessoire VIP', 'Réguliers', 'immediate', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(18, 1, 'Pièce ancienne 3', 'Pièce historique', 320.00, 'Accessoire VIP', 'Réguliers', 'meilleure offre', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(19, 1, 'Radio ancienne', 'Radio vintage fonctionnelle', 90.00, 'Accessoire VIP', 'Haut de gamme', 'immediate', 'Fonctionnelle', NULL, '2025-05-29 09:16:52', 0),
(20, 1, 'Tableau 1', 'Tableau coloré bord de mer', 180.00, 'Meubles et objets d’art', 'Réguliers', 'negociation', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(21, 1, 'Tableau 2', 'La nuit étoilée reproduction', 190.00, 'Meubles et objets d’art', 'Réguliers', 'immediate', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(22, 1, 'Tableau 3', 'Portrait de Van Gogh', 195.00, 'Meubles et objets d’art', 'Réguliers', 'negociation', 'Très bonne', NULL, '2025-05-29 09:16:52', 0),
(23, 1, 'Table basse bois', 'Table basse en bois', 100.00, 'Meubles et objets d’art', 'Rares', 'meilleure offre', 'Bonne', NULL, '2025-05-29 09:16:52', 0),
(24, 1, 'Téléphone', 'Téléphone ancien en bakélite', 70.00, 'Accessoire VIP', 'Haut de gamme', 'negociation', 'Fonctionnel', NULL, '2025-05-29 09:16:52', 0);


-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL,
  `adresse1` varchar(255) DEFAULT NULL,
  `adresse2` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `article_id` int DEFAULT NULL,
  `prix_final` decimal(10,2) DEFAULT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `type_commande` enum('achat_immediat','negociation','enchere') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `article_id` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `encheres`
--

DROP TABLE IF EXISTS `encheres`;
CREATE TABLE IF NOT EXISTS `encheres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `prix_max` decimal(10,2) DEFAULT NULL,
  `date_enchere` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `negociations`
--

DROP TABLE IF EXISTS `negociations`;
CREATE TABLE IF NOT EXISTS `negociations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `vendeur_id` int DEFAULT NULL,
  `prix_propose` decimal(10,2) DEFAULT NULL,
  `date_negociation` datetime DEFAULT CURRENT_TIMESTAMP,
  `tour` int DEFAULT NULL,
  `statut` enum('en_cours','accepte','refuse') DEFAULT 'en_cours',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `client_id` (`client_id`),
  KEY `vendeur_id` (`vendeur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `mot_cle` varchar(100) DEFAULT NULL,
  `categorie_id` int DEFAULT NULL,
  `prix_max` decimal(10,2) DEFAULT NULL,
  `type_vente` enum('achat_immediat','negociation','enchere') DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int DEFAULT NULL,
  `type_carte` enum('Visa','MasterCard','AmericanExpress','PayPal') DEFAULT NULL,
  `numero_carte` varchar(20) DEFAULT NULL,
  `nom_carte` varchar(100) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `code_securite` varchar(4) DEFAULT NULL,
  `valide` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `article_id` int DEFAULT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `article_id` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `article_id`, `url`) VALUES
(1, 1, 'appareil.png'),
(2, 2, 'commodeblanche.png'),
(3, 3, 'commodebois.png'),
(4, 4, 'commodeverte.png'),
(5, 5, 'disque.png'),
(6, 6, 'fauteuilblanc.png'),
(7, 7, 'machine1.png'),
(8, 8, 'machineacoudre.png'),
(9, 9, 'micro.png'),
(10, 10, 'montre.png'),
(11, 11, 'montre2.png'),
(12, 12, 'montre3.png'),
(13, 13, 'peinture1.png'),
(14, 14, 'peinture2.png'),
(15, 15, 'peinture3.png'),
(16, 16, 'piece1.png'),
(17, 17, 'piece2.png'),
(18, 18, 'piece3.png'),
(19, 19, 'radio1.png'),
(20, 20, 'tableau1.png'),
(21, 21, 'tableau2.png'),
(22, 22, 'tableau3.png'),
(23, 23, 'tablebassebois.png'),
(24, 24, 'telephone.png');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','utilisateur') NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendeurs`
--

DROP TABLE IF EXISTS `vendeurs`;
CREATE TABLE IF NOT EXISTS `vendeurs` (
  `id` int NOT NULL,
  `pseudo` varchar(100) DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `image_fond` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
