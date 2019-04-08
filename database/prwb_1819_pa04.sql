-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 08 Avril 2019 à 16:55
-- Version du serveur :  5.7.11
-- Version de PHP :  5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `prwb_1819_pa04`
--
CREATE DATABASE IF NOT EXISTS `prwb_1819_pa04` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `prwb_1819_pa04`;

-- --------------------------------------------------------

--
-- Structure de la table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `isbn` char(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `editor` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `nbCopies` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `book`
--

INSERT INTO `book` (`id`, `isbn`, `title`, `author`, `editor`, `picture`, `nbCopies`) VALUES
(5, '2266130692005', 'La Bible de Jérusalem', 'Jesus', 'POCKET', 'picture/default_picture.jpg', 10),
(6, '2070408507003', 'Le Petit Prince', 'Saint-Exupéry', 'GALLIMARD', 'picture/default_picture.jpg', 20),
(7, '2266154117004', 'Le Seigneur des Anneaux, Tome 1 : La Communauté de l\'Anneau', 'Tolkien', 'POCKET', 'picture/default_picture.jpg', 15),
(8, '2210758815004', 'Vingt mille lieues sous les mers', 'Jules Verne', 'MAGNARD', 'picture/default_picture.jpg', 5),
(9, '2253001279908', 'Journal d\'Anne Frank', 'Isabelle Rosselin', 'LE LIVRE DE POCHE', 'picture/default_picture.jpg', 3),
(10, '2812902795001', 'Fables de Jean de la Fontaine', 'Jean de La Fontaine', 'EDITIONS DE BORÉE', 'picture/default_picture.jpg', 25),
(11, '2709650185003', 'Le Fléau : Intégrale', 'Stephen King', 'J.-C. LATTÈS', 'picture/default_picture.jpg', 30),
(12, '1032402628', 'La pire soirée de ta vie', 'Andy', '404 Editions', NULL, 35),
(13, '2702165486', 'La vie secrète des écrivains ', 'Guillaume Musso', 'Calmann-Levy', NULL, 30),
(14, '2226439412', 'La Dernière Chasse ', 'Jean-Christophe Grangé', 'Albin Michel', NULL, 20);

-- --------------------------------------------------------

--
-- Structure de la table `rental`
--

CREATE TABLE `rental` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `book` int(11) NOT NULL,
  `rentaldate` datetime DEFAULT NULL,
  `returndate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `rental`
--

INSERT INTO `rental` (`id`, `user`, `book`, `rentaldate`, `returndate`) VALUES
(14, 2, 7, '2019-02-12 08:06:10', NULL),
(15, 6, 6, '2016-08-19 06:26:20', NULL),
(20, 2, 5, '2019-04-08 18:48:03', NULL),
(21, 2, 6, '2019-02-06 18:48:03', NULL),
(22, 2, 8, '2019-04-08 18:48:03', NULL),
(23, 2, 7, '2019-04-08 18:48:03', NULL),
(29, 3, 5, '2019-02-06 18:48:37', NULL),
(30, 3, 7, '2019-04-08 18:48:37', NULL),
(31, 3, 6, '2019-04-08 18:48:37', NULL),
(35, 5, 14, '2018-12-12 18:48:50', NULL),
(36, 5, 13, '2018-12-12 18:48:50', NULL),
(37, 5, 12, '2019-04-08 18:48:50', NULL),
(42, 6, 14, '2019-04-08 18:49:07', NULL),
(43, 6, 12, '2019-04-08 18:49:07', NULL),
(44, 6, 11, '2019-01-17 18:49:07', NULL),
(45, 6, 10, '2019-04-08 18:49:07', NULL),
(49, 8, 8, '2019-04-08 18:49:23', NULL),
(50, 8, 9, '2019-01-16 18:49:23', NULL),
(51, 8, 10, '2019-04-08 18:49:23', NULL),
(55, 9, 7, '2019-04-08 18:49:34', NULL),
(56, 9, 10, '2019-04-08 18:49:34', NULL),
(57, 9, 12, '2019-02-12 18:49:34', NULL),
(61, 10, 11, '2019-04-08 18:49:44', NULL),
(62, 10, 10, '2019-01-15 18:49:44', NULL),
(63, 10, 14, '2019-01-21 18:49:44', NULL),
(67, 4, 6, '2019-04-08 18:50:00', NULL),
(68, 4, 11, '2019-04-08 18:50:00', NULL),
(69, 4, 12, '2019-04-08 18:50:00', NULL),
(73, 7, 7, '2019-04-08 18:50:15', NULL),
(74, 7, 10, '2019-04-08 18:50:15', NULL),
(75, 7, 13, '2019-04-08 18:50:15', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `role` enum('admin','manager','member') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `fullname`, `email`, `birthdate`, `role`) VALUES
(2, 'admin1', '71fd54e5a8f78f1ead61fd0e5ca8da9f', 'admin1', 'admin1@epfc.eu', NULL, 'admin'),
(3, 'admin2', '5361f7e82cb9f51902a62b5c31cf572e', 'admin2', 'admin2@epfc.eu', NULL, 'admin'),
(4, 'admin3', 'f0be62dd73a93d71ef0d0a207f08000a', 'admin3', 'admin3@epfc.eu', NULL, 'admin'),
(5, 'manager1', 'ece2896fcaccd9ab9f824c96219fd039', 'manager1', 'manager1@epfc.eu', NULL, 'manager'),
(6, 'manager2', '44d8f7726dd74cd64bf92514305823b5', 'manager2', 'manager2@epfc.eu', NULL, 'manager'),
(7, 'manager3', '9d570905d1261ddbfac7cf93e16991a1', 'manager3', 'manager3@epfc.eu', NULL, 'manager'),
(8, 'member1', '770c256b485c4b243a44fe8881c16e46', 'member1', 'member1@epfc.eu', NULL, 'member'),
(9, 'member2', '9c3656758b3cc89f5380c7ac525a5b67', 'member2', 'member2@epfc.eu', NULL, 'member'),
(10, 'member3', '7ccc9da7be2202de29ef3428808c2c56', 'member3', 'member3@epfc.eu', NULL, 'member');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn_UNIQUE` (`isbn`);

--
-- Index pour la table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rentalitem_book1_idx` (`book`),
  ADD KEY `fk_rentalitem_user1_idx` (`user`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_unique` (`username`) USING BTREE,
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `rental`
--
ALTER TABLE `rental`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `fk_rentalitem_book` FOREIGN KEY (`book`) REFERENCES `book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rentalitem_user1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
