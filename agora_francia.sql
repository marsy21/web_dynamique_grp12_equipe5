-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 01, 2025 at 11:15 AM
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
  `categorie` enum('Meubles et objets d’art','Accessoire VIP','Materiels scolaires') DEFAULT NULL,
  `rarete` enum('Rares','Haut de gamme','Réguliers') DEFAULT NULL,
  `type_vente` enum('meilleure offre','negociation','immediate') DEFAULT 'immediate',
  `qualite` varchar(255) DEFAULT NULL,
  `defaut` varchar(255) DEFAULT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `vendu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_vendeur` (`id_vendeur`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(24, 1, 'Téléphone', 'Téléphone ancien en bakélite', 70.00, 'Accessoire VIP', 'Haut de gamme', 'negociation', 'Fonctionnel', NULL, '2025-05-29 09:16:52', 0),
(26, 4, 'ok', 'ccc', 45.00, NULL, NULL, 'negociation', NULL, NULL, '2025-05-31 16:23:40', 0),
(27, 4, 'test', 'test', 5.00, 'Meubles et objets d’art', 'Haut de gamme', 'meilleure offre', 'sale', 'sale', '2025-05-31 16:34:39', 0),
(29, 2, 'okayyy', 'okayy', 4.00, 'Materiels scolaires', 'Rares', 'negociation', 'sale', 'sale', '2025-06-01 10:56:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cartesreelles`
--

DROP TABLE IF EXISTS `cartesreelles`;
CREATE TABLE IF NOT EXISTS `cartesreelles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_carte` enum('Visa','MasterCard','AmericanExpress','PayPal') DEFAULT NULL,
  `numero_carte` varchar(20) DEFAULT NULL,
  `nom_carte` varchar(100) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `code_securite` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cartesreelles`
--

INSERT INTO `cartesreelles` (`id`, `type_carte`, `numero_carte`, `nom_carte`, `expiration`, `code_securite`) VALUES
(1, 'MasterCard', 'mmm', 'nnnnnn', '2025-05-31', '222'),
(2, 'AmericanExpress', 'a', 'a', '2025-05-31', 'a'),
(3, 'Visa', 'b', 'b', '2025-05-29', 'b'),
(4, 'MasterCard', '5fewe', 'dgre', '2025-05-28', 'dddd'),
(5, 'MasterCard', '88888', 'hjhh', '2025-05-23', '888'),
(6, 'Visa', '65554', 'vbvvv', '2025-05-23', '5555');

-- --------------------------------------------------------

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

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `adresse1`, `adresse2`, `ville`, `code_postal`, `pays`, `telephone`) VALUES
(1, 'ddd', 'ddd', 'ddd', 'ddd', 'France', '999999'),
(2, 's', 's', 's', 's', 's', 's'),
(3, 'hg', 'cyc', 'ighb', '558464', 'France', '987897987'),
(4, '', '', '', '', '', ''),
(5, 'ddd', 'ddd', 'ddd', 'ddd', 'France', 'nn'),
(6, 'aA', 'a', 'a', 'a', 'a', 'a'),
(7, 'b', 'b', 'b', 'b', 'b', 'b'),
(8, 'ddd', 'ddd', 'ddd', 'ddd', 'France', '999999'),
(11, 'ddd', 'ddd', 'ddd', 'ddd', 'France', '444454'),
(12, 'ddd', 'ddd', 'ddd', 'ddd', 'France', '4544');

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
  `type_vente` enum('meilleure offre','negociation','immediate') DEFAULT 'immediate',
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `encheres`
--

INSERT INTO `encheres` (`id`, `article_id`, `client_id`, `prix_max`, `date_enchere`) VALUES
(1, 2, 4, 160.00, '2025-06-01 10:21:48'),
(2, 14, 2, 500.00, '2025-06-01 10:36:51'),
(3, 27, 2, 7.00, '2025-06-01 10:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--
DROP TABLE IF EXISTS `notifications`;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `mot_cle` varchar(100) DEFAULT NULL,
  `categorie` ENUM('Meubles et objets d’art', 'Accessoire VIP', 'Materiels scolaires') DEFAULT NULL,
  `prix_max` decimal(10,2) DEFAULT NULL,
  `type_vente` enum('meilleure offre','negociation','immediate') DEFAULT 'immediate',
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
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panier`
--

INSERT INTO `panier` (`id`, `client_id`, `article_id`, `date_ajout`) VALUES
(2, 1, 1, '2025-05-29 21:44:35'),
(3, 1, 2, '2025-05-29 21:44:40'),
(4, 1, 3, '2025-05-29 21:48:38'),
(5, 2, 21, '2025-05-29 21:56:27'),
(6, 2, 14, '2025-05-29 22:02:47'),
(7, 2, 22, '2025-05-29 22:02:54'),
(15, 3, 1, '2025-05-29 22:59:32'),
(9, 2, 4, '2025-05-29 22:03:16'),
(11, 3, 9, '2025-05-29 22:09:14'),
(12, 3, 14, '2025-05-29 22:09:28'),
(13, 3, 8, '2025-05-29 22:09:34'),
(18, 3, 2, '2025-05-29 23:38:43'),
(17, 3, 10, '2025-05-29 23:29:02'),
(19, 3, 18, '2025-05-29 23:44:57'),
(20, 8, 7, '2025-05-30 00:51:28'),
(21, 4, 2, '2025-05-31 16:19:14'),
(22, 4, 7, '2025-05-31 16:19:30'),
(23, 12, 3, '2025-05-31 16:40:25'),
(24, 12, 13, '2025-05-31 16:40:31'),
(26, 12, 28, '2025-05-31 16:41:17'),
(27, 4, 4, '2025-06-01 10:22:50'),
(28, 4, 22, '2025-06-01 10:37:25'),
(29, 4, 14, '2025-06-01 10:37:49'),
(31, 2, 27, '2025-06-01 10:53:58'),
(32, 4, 29, '2025-06-01 11:01:43');

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
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(18, 18, 'piece3.jpg'),
(19, 19, 'radio1.png'),
(20, 20, 'tableau1.png'),
(21, 21, 'tableau2.png'),
(22, 22, 'tableau3.png'),
(23, 23, 'tablebassebois.png'),
(24, 24, 'telephone.png'),
(25, 18, 'peinture3.png'),
(26, 27, '683b218f6bda7.png'),
(27, 28, '683b2317d3213.png'),
(28, 29, '683c23c802097.png');

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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'm', 'm', 'malo@malo.com', '$2y$10$k1V8qA.zwHdyB0sxShNf3enJgBqXWwq8iGRL7Gkmt/eWwQzQg2kp2', '', '2025-05-29 21:53:54'),
(2, 'ok', 'ok', 'ok@ok.com', '$2y$10$ACWm13cMY/tyv4LMdZWmwOe04.AcQzjuTM7hB3pyvCXG9O9YBsORi', '', '2025-05-29 21:54:26'),
(3, 'e', 'e', 'e@ok.com', '$2y$10$KfbUHsahXHUq2yTWUjmWO.aJHvaLjNsKl3ItyEEXfbrv8sopS.fHS', '', '2025-05-29 22:08:34'),
(4, 'test', 'test', 'test@ok.com', '$2y$10$6iwMGMxaNUbiVi6QJypkXOjhDzkdDSPaAHQ38hvjgiVYjO1p8IOrG', '', '2025-05-30 00:09:57'),
(5, 'c', 'c', 'test2@ok.com', '$2y$10$l69vU1e8X4XnhUp.yI/KOejJ9juunU3KsgsjBwFqvxo1umwD0MVYq', '', '2025-05-30 00:16:12'),
(6, 'c', 'c', 'test22@ok.com', '$2y$10$0LfJZjUS.hEJ0dx2S32ZRuE0tFC3yfcrkdCCDNbcHPfvTvfBk/mIm', '', '2025-05-30 00:18:29'),
(7, 'c', 'c', 'test3@ok.com', '$2y$10$jCPL7Cpm2ULO5zIpr.BXd.Shw4XktafpYENvk5AGn6cv720jXTd06', '', '2025-05-30 00:19:48'),
(8, 'c', 'c', 'test5@ok.com', '$2y$10$CXGzOGRym3zOolso.1teiuSWtisUK7J/mjxNpnRed8RFb/zXPxB/2', '', '2025-05-30 00:22:02'),
(9, 'c', 'c', 'tesfdt@ok.com', '$2y$10$C10iJ1atXgBUMPbjShhhiuZvDiKJ8gQe2VUTL1t/qknb3/LGClIHu', '', '2025-05-31 15:30:53'),
(10, 'd', 'd', 'tesdt@ok.com', '$2y$10$YbvO.BtvCj0RBWI09bFrOe5XQUnS/JO8rGVjIpI2qvUMTy53BiEo.', '', '2025-05-31 16:17:21'),
(11, 'c', 'c', 'testj@ok.com', '$2y$10$nr7x2iRpu1su2ll3gz8twOBtikXOQespWSwgxlOr.reFaBz8eSsia', '', '2025-05-31 16:36:01'),
(12, 'n', 'j', 'test8@ok.com', '$2y$10$4LK1NSVTA8.aOaOspiv6BOOepaIrNYJd35ZExbv.TDUfH/qtVXQ0K', '', '2025-05-31 16:39:15');

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

--
-- Dumping data for table `vendeurs`
--

INSERT INTO `vendeurs` (`id`, `pseudo`, `photo_profil`, `image_fond`) VALUES
(2, 'hyhhhhh', NULL, NULL),
(5, 'xx', NULL, NULL),
(8, 'd', NULL, NULL),
(4, 'hgggg', NULL, NULL),
(11, 'kkk', NULL, NULL),
(12, 'hhhh', NULL, NULL);

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
