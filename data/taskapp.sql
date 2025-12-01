-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 01 déc. 2025 à 10:58
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `taskapp`
--

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `task_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `task_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `task_status_id` int NOT NULL DEFAULT '1',
  `task_due_date` date DEFAULT NULL,
  `task_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status_id` (`task_status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task_title`, `task_desc`, `task_status_id`, `task_due_date`, `task_created_at`) VALUES
(1, 1, 'Faire les courses', 'Acheter du lait, des œufs et du pain', 1, '2024-12-20', '2025-10-26 10:59:38'),
(2, 1, 'Rédiger le rapport', 'Terminer le document pour le client XYZ', 2, '2024-12-18', '2025-10-26 10:59:38'),
(8, 1, 'Finir mon CRUD', 'papapapapa', 1, '2025-11-20', '2025-11-14 23:44:00'),
(4, 1, 'Finir mon projet', 'erfstfrtgtrhy', 1, '2025-11-04', '2025-11-03 20:55:27'),
(5, 1, 'Finir mon projet', 'htrytuyurtu', 1, '2025-11-04', '2025-11-03 20:57:41'),
(9, 1, 'loliilo', 'tolili lelele', 1, '2025-11-21', '2025-11-14 23:44:36'),
(10, 1, 'Première tâche', 'szaszqdzqd', 1, '2025-11-26', '2025-11-14 23:45:25'),
(11, 1, 'ezrezrzer', 'ezrezrzet', 1, '2025-12-17', '2025-11-14 23:45:45'),
(21, 16, 'Sport', 'Organiser un match de football entre amis', 3, '2025-11-29', '2025-11-26 15:06:39'),
(19, 16, 'Projet TaskFlow', 'Finir mon CRUD', 3, '2025-12-01', '2025-11-26 15:03:33'),
(20, 16, 'Projet Symfony', 'Finir la gestion de routes', 1, '2025-12-02', '2025-11-26 15:05:37'),
(22, 16, 'Rapport travail', 'Rédiger un rapport et l\'envoyer au chargé de communication', 3, '2025-11-26', '2025-11-26 15:09:37');

-- --------------------------------------------------------

--
-- Structure de la table `task_statuses`
--

DROP TABLE IF EXISTS `task_statuses`;
CREATE TABLE IF NOT EXISTS `task_statuses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `task_statuses`
--

INSERT INTO `task_statuses` (`id`, `name`) VALUES
(1, 'À faire'),
(2, 'En cours'),
(3, 'Terminée'),
(4, 'En retard');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_pwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_token` int NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`user_name`),
  UNIQUE KEY `email` (`user_email`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `user_name`, `user_email`, `user_pwd`, `user_token`, `is_verified`, `created_at`) VALUES
(1, 'user', 'test@email.com', '1234', 0, 1, '2025-10-26 10:57:35'),
(16, 'toto', 'massineabgar@hotmail.com', '$2y$10$Fa27b.OAS5gLcRCus.FlpeGXiVzNnQ5n0oqCjpvnOsYqvJRb.HiEi', 0, 1, '2025-11-21 10:51:47');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
