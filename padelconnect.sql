-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 09 juin 2026 à 17:52
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `padelconnect`
--

-- --------------------------------------------------------

--
-- Structure de la table `is_registered`
--

CREATE TABLE `is_registered` (
  `id_user` int(11) NOT NULL,
  `id_timeslot` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id_notification` int(11) NOT NULL,
  `message` text NOT NULL,
  `level` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `notification_read`
--

CREATE TABLE `notification_read` (
  `id_user` int(11) NOT NULL,
  `id_notification` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `timeslot`
--

CREATE TABLE `timeslot` (
  `id_timeslot` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `price` decimal(5,2) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `level` varchar(30) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `user_removed_match`
--

CREATE TABLE `user_removed_match` (
  `id_removed_match` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_timeslot` int(11) NOT NULL,
  `removed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `is_registered`
--
ALTER TABLE `is_registered`
  ADD PRIMARY KEY (`id_user`,`id_timeslot`),
  ADD KEY `id_timeslot` (`id_timeslot`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notification`);

--
-- Index pour la table `notification_read`
--
ALTER TABLE `notification_read`
  ADD PRIMARY KEY (`id_user`,`id_notification`);

--
-- Index pour la table `timeslot`
--
ALTER TABLE `timeslot`
  ADD PRIMARY KEY (`id_timeslot`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Index pour la table `user_removed_match`
--
ALTER TABLE `user_removed_match`
  ADD PRIMARY KEY (`id_removed_match`),
  ADD UNIQUE KEY `uniq_user_timeslot` (`id_user`,`id_timeslot`),
  ADD KEY `idx_user_id` (`id_user`),
  ADD KEY `idx_timeslot_id` (`id_timeslot`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `id_timeslot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `user_removed_match`
--
ALTER TABLE `user_removed_match`
  MODIFY `id_removed_match` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `is_registered`
--
ALTER TABLE `is_registered`
  ADD CONSTRAINT `is_registered_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `is_registered_ibfk_2` FOREIGN KEY (`id_timeslot`) REFERENCES `timeslot` (`id_timeslot`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
