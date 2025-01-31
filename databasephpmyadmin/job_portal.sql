-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2025 at 10:45 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `acceptedusers`
--

CREATE TABLE `acceptedusers` (
  `id` int NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `offre_name` varchar(100) NOT NULL,
  `accepted` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `acceptedusers`
--

INSERT INTO `acceptedusers` (`id`, `user_id`, `offre_name`, `accepted`) VALUES
(1, '1', 'Développeur Frontend', 'yes'),
(3, '1', 'Consultant en Cybersécurité', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
('679a3bb0ae743', 'taha'),
('abe2', 'Marketing'),
('efb2', 'Informatique');

-- --------------------------------------------------------

--
-- Table structure for table `domaines`
--

CREATE TABLE `domaines` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `domaines`
--

INSERT INTO `domaines` (`id`, `nom`, `categorie_id`) VALUES
(1, 'Développement Web', 'efb2'),
(2, 'Cybersécurité', 'efb2'),
(3, 'Marketing Digital', 'abe2'),
(4, 'Études de marché', 'abe2'),
(6, 'azeaze', 'efb2');

-- --------------------------------------------------------

--
-- Table structure for table `offres`
--

CREATE TABLE `offres` (
  `id` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `salaire` int NOT NULL,
  `domaine_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `offres`
--

INSERT INTO `offres` (`id`, `titre`, `description`, `salaire`, `domaine_id`) VALUES
(1, 'Développeur Frontend', 'Expérience en Vue.js', 3000, 1),
(2, 'Développeur Backend', 'Expérience en Node.js', 3500, 1),
(3, 'Développeur Fullstack', 'Compétences en React.js et Node.js', 4000, 1),
(4, 'Intégrateur Web', 'Maitrise de HTML/CSS et Bootstrap', 2500, 1),
(5, 'Analyste Sécurité', 'Gestion des incidents', 4000, 2),
(6, 'Pentester', 'Tests d\'intrusion et audits de sécurité', 4500, 2),
(7, 'Consultant en Cybersécurité', 'Conception de stratégies de sécurité', 5000, 2),
(8, 'Community Manager', 'Gestion des réseaux sociaux', 2500, 3),
(9, 'Spécialiste SEO', 'Optimisation des moteurs de recherche', 3000, 3),
(10, 'Responsable Marketing Digital', 'Planification des campagnes marketing', 3500, 3),
(11, 'Analyste de Marché', 'Études et analyses de données de marché', 320000000, 3),
(14, 'a', 'a', 330, 2),
(15, 'testagain', 'testagain', 2000, 4);

-- --------------------------------------------------------

--
-- Table structure for table `qcm`
--

CREATE TABLE `qcm` (
  `id` int NOT NULL,
  `offre_id` int DEFAULT NULL,
  `question` text NOT NULL,
  `reponseCorrecte` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `qcm`
--

INSERT INTO `qcm` (`id`, `offre_id`, `question`, `reponseCorrecte`) VALUES
(1, 1, 'Quel framework utilisez-vous pour le développement frontend ?', 'Vue.js'),
(2, 1, 'Que signifie \'two-way data binding\' ?', 'Lien entre le modèle et la vue'),
(3, 2, 'Quelle méthode est utilisée pour créer un serveur avec Node.js ?', 'http.createServer()'),
(4, 7, 'Quel est le rôle d\'un consultant en cybersécurité ?', 'Concevoir des solutions de sécurité pour les entreprises'),
(5, 9, 'Qu\'est-ce que le SEO ?', 'Search Engine Optimization'),
(6, 11, 'Quel est l\'objectif principal d\'un analyste de marché ?', 'Analyser les tendances et les comportements du marché'),
(20, 3, 'azeaze', 'e'),
(25, 8, 'azetaha', 'ztaha'),
(28, 15, 'taha', 'qsdazedaqs'),
(29, 15, 'aze', 'eaze');

-- --------------------------------------------------------

--
-- Table structure for table `qcm_options`
--

CREATE TABLE `qcm_options` (
  `id` int NOT NULL,
  `qcm_id` int DEFAULT NULL,
  `option_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `qcm_options`
--

INSERT INTO `qcm_options` (`id`, `qcm_id`, `option_text`) VALUES
(1, 1, 'Vue.js'),
(2, 1, 'React.jsss'),
(3, 1, 'Angular'),
(4, 1, 'Ember.js'),
(5, 2, 'Lien entre le modèle et la vue'),
(6, 2, 'Gestion des événements'),
(7, 2, 'Manipulation du DOM'),
(8, 2, 'Gestion de la navigation'),
(9, 3, 'app.listen()'),
(10, 3, 'server.create()'),
(11, 3, 'http.createServer()'),
(12, 3, 'express.create()'),
(13, 4, 'Gérer les incidents réseau'),
(14, 4, 'Concevoir des solutions de sécurité pour les entreprises'),
(15, 4, 'Analyser des performances'),
(16, 4, 'Vendre des logiciels de sécurité'),
(17, 5, 'Search Engine Optimization'),
(18, 5, 'Secure Email Optimization'),
(19, 5, 'Systematic Entry Operation'),
(20, 5, 'Simple Effective Online'),
(21, 6, 'Créer des campagnes publicitaires'),
(22, 6, 'Analyser les tendances et les comportements du marché'),
(23, 6, 'Concevoir des produits'),
(24, 6, 'Écrire des rapports financiers'),
(70, 20, 'e'),
(71, 20, 'z'),
(72, 20, 'a'),
(73, 20, 'r'),
(90, 25, 'etaha'),
(91, 25, 'ztaha'),
(92, 25, 'ataha'),
(93, 25, 'ztaha'),
(102, 28, 'tahaazea'),
(103, 28, 'azeazdasdqd'),
(104, 28, 'qsdazedaqs'),
(105, 28, 'dqsdqsdqsd'),
(106, 29, 'eazeazeaz'),
(107, 29, 'azea'),
(108, 29, 'zeazeaz'),
(109, 29, 'eaze');

-- --------------------------------------------------------

--
-- Table structure for table `useroffers`
--

CREATE TABLE `useroffers` (
  `id` int NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `offre_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `useroffers`
--

INSERT INTO `useroffers` (`id`, `user_id`, `offre_name`) VALUES
(1, '76e1', 'Développeur Frontend'),
(3, '76e1', 'Analyste Sécurité'),
(17, '1', 'Analyste de Marché'),
(18, '76e1', 'Analyste de Marché'),
(19, '76e1', 'Consultant en Cybersécurité'),
(20, '76e1', 'Développeur Backend'),
(21, '1', 'Développeur Backend'),
(22, '6796d8d341e61', 'Développeur Backend'),
(23, '6796d8d341e61', 'Développeur Frontend'),
(26, '76e1', 'testagain'),
(29, '1', 'Développeur Backend');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
('1', 't', 't@t', 't'),
('57.61331860833282', 'Admin', 'Admin@Admin.com', 'Admin'),
('6796d8d341e61', 'test', 'test@test', 'test'),
('6797e4201b627', 'ghassen', 'ghassen@gmail.com', 'ghassen'),
('679a6ab042642', 'istabrak', 'Daoud@gmail.com', '210692iS'),
('76e1', 'taha', 'taha@gmail.com', 'taha');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acceptedusers`
--
ALTER TABLE `acceptedusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domaines`
--
ALTER TABLE `domaines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `offres`
--
ALTER TABLE `offres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domaine_id` (`domaine_id`);

--
-- Indexes for table `qcm`
--
ALTER TABLE `qcm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offre_id` (`offre_id`);

--
-- Indexes for table `qcm_options`
--
ALTER TABLE `qcm_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qcm_options_ibfk_1` (`qcm_id`);

--
-- Indexes for table `useroffers`
--
ALTER TABLE `useroffers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `useroffers_ibfk_2` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acceptedusers`
--
ALTER TABLE `acceptedusers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `domaines`
--
ALTER TABLE `domaines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `offres`
--
ALTER TABLE `offres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `qcm`
--
ALTER TABLE `qcm`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `qcm_options`
--
ALTER TABLE `qcm_options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `useroffers`
--
ALTER TABLE `useroffers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acceptedusers`
--
ALTER TABLE `acceptedusers`
  ADD CONSTRAINT `acceptedusers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `domaines`
--
ALTER TABLE `domaines`
  ADD CONSTRAINT `domaines_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `offres`
--
ALTER TABLE `offres`
  ADD CONSTRAINT `offres_ibfk_1` FOREIGN KEY (`domaine_id`) REFERENCES `domaines` (`id`);

--
-- Constraints for table `qcm`
--
ALTER TABLE `qcm`
  ADD CONSTRAINT `qcm_ibfk_1` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`);

--
-- Constraints for table `qcm_options`
--
ALTER TABLE `qcm_options`
  ADD CONSTRAINT `qcm_options_ibfk_1` FOREIGN KEY (`qcm_id`) REFERENCES `qcm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `useroffers`
--
ALTER TABLE `useroffers`
  ADD CONSTRAINT `useroffers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `useroffers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
