-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : dim. 27 août 2023 à 10:22
-- Version du serveur : 5.7.34
-- Version de PHP : 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `myband` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `myband`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `myband`
--

-- --------------------------------------------------------

--
-- Structure de la table `members`
--

CREATE TABLE `members` (
  `login` varchar(100) NOT NULL,
  `password` char(64) NOT NULL,
  `salt` char(64) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Comptes de démonstration du TP (SHA-256 du sel suivi du mot de passe)
--

INSERT INTO `members` (`login`, `password`, `salt`, `email`, `contact`, `admin`) VALUES
('admin', '1c981810367f247191c993cd157e5f78d3e4323e31faf6238a947678af994ea2', '99ede3f5dc37615361052676261407980becb88ebd71d6280cf58aad60708cce', 'admin@myband.com', 1, 1),
('Van.Halen', '5dea133b5ed7619d364892c18cc47c4b01cdd992fb89e39770a04f53391dea74', '7f569e6e6fbbbc856b1d1d31c02b41d5f3bbf290a3042c3bcf52cf1e1d7206fc', 'eddy@myband.com', 0, 0),
('Freddy.Mercury', 'eb21eb45ac027f441df498b471ea72c9dd4d4b3e04da4d8e9e4846b49ae3cf67', 'ef3dcfa4399764a8fac7368c58f526bf6f48e2569200a54347f5abb6253af09b', 'freddy@myband.com', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `setlist`
--

CREATE TABLE `setlist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `style` varchar(255) NOT NULL,
  `lyrics` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `setlist`
--

INSERT INTO `setlist` (`id`, `title`, `artist`, `style`) VALUES
(1, 'Pause', 'Compo', 'Apéro Concert'),
(2, 'Lettre à France', 'M. Polnareff', 'Apéro Concert'),
(3, 'Hymne à l\'amour', 'E. Piaf', 'Apéro Concert'),
(4, 'Let it be', 'Beatles', 'Apéro Concert'),
(5, 'Hallelujah', 'J. Buckey', 'Apéro Concert'),
(6, 'Your Song', 'E. John', 'Apéro Concert'),
(7, 'Lucie', 'P. Obispo', 'Apéro Concert'),
(8, 'Toi + moi', 'Grégoire', 'Apéro Concert'),
(9, 'Shape of my heart', 'Sting', 'Apéro Concert'),
(10, 'SOS', 'D. Balavoine', 'Apéro Concert'),
(11, 'Colorblind', 'Counting Crows', 'Apéro Concert'),
(12, 'J\'ai oublié de l\'oublier', 'E. Mitchell', 'Apéro Concert'),
(13, 'Kissing you', 'Dees\'re', 'Apéro Concert'),
(14, 'C\'est bon pour le moral', 'C. Créole', 'Tour du Monde - Créole'),
(15, 'Le douanier rousseau', 'C. Créole', 'Tour du Monde - Créole'),
(16, 'Ca fait rire les oiseaux', 'C. Créole', 'Tour du Monde - Créole'),
(17, 'Ai se tu e pego', 'M. Telo', 'Tour du Monde - Tube été'),
(18, 'Lambada', 'Kaoma', 'Tour du Monde - Tube été'),
(19, 'Samba de janeiro', 'Bellini', 'Tour du Monde - Tube été'),
(20, 'La Bamba', 'Los Lobos', 'Tour du Monde - Tube été'),
(21, 'No woman no cry', 'B. Marley', 'Tour du Monde - Reggae'),
(22, 'Reggae Night', 'J. Cliff', 'Tour du Monde - Reggae'),
(23, 'Ville de lumière', 'Gold', '80s, Disco, Funk - Gold'),
(24, 'Plus près des étoiles', 'Gold', '80s, Disco, Funk - Gold'),
(25, 'Capitaine abandonné', 'Gold', '80s, Disco, Funk - Gold'),
(26, 'Nuit de folie', 'Début de soirée', '80s, Disco, Funk - Medley 80'),
(27, 'Macumba', 'J.P Mader', '80s, Disco, Funk - Medley 80'),
(28, 'Le Jerk', 'T. Hazard', '80s, Disco, Funk - Medley 80'),
(29, 'Les démons de minuits', 'Image', '80s, Disco, Funk - Medley 80'),
(30, 'Gimme Gimme Gimme', 'ABBA', '80s, Disco, Funk - Disco'),
(31, 'Hot Stuff', 'D. Sommer', '80s, Disco, Funk - Disco'),
(32, 'Call Me', 'Blondie', '80s, Disco, Funk - Disco'),
(33, 'I Will Survive', 'G. Glaynor', '80s, Disco, Funk - Disco'),
(34, 'Magnolia forever', 'C. François', '80s, Disco, Funk - Cloclo'),
(35, 'Cette année là', 'C. François', '80s, Disco, Funk - Cloclo'),
(36, 'Staying alive', 'Beegees', '80s, Disco, Funk - Medley Funk'),
(37, 'Long train running', 'Dobbie Brothers', '80s, Disco, Funk - Medley Funk'),
(38, 'Le Freak', 'Chic', '80s, Disco, Funk - Medley Funk'),
(39, 'Je marche seul', 'J.J Goldman', 'Goldman & Cie'),
(40, 'Quand la musique est bonne', 'J.J Goldman', 'Goldman & Cie'),
(41, 'Il suffira d\'un signe', 'J.J Goldman', 'Goldman & Cie'),
(42, 'Encore un matin', 'J.J Goldman', 'Goldman & Cie'),
(43, 'Envole moi', 'J.J Goldman', 'Goldman & Cie'),
(44, 'J\'irai ou tu iras', 'JJ. Goldman / C. Dion', 'Goldman & Cie'),
(45, 'L\'aventurier', 'Indochine', '80s, Disco, Funk'),
(46, 'Sunlight des tropiques', 'G. Montagné', '80s, Disco, Funk - Montagné'),
(47, 'On va s\'aimer', 'G. Montagné', '80s, Disco, Funk - Montagné'),
(48, 'A Whiter Shade Of Pale', 'Procol Harum', 'Love & Slows'),
(49, 'Si (Zaz) : 113', 'Zaz', 'Love & Slows'),
(50, 'Knocking heaven\'s door : 110', 'Guns & Roses', 'Love & Slows'),
(51, 'La derniere danse : 111', 'Kyo', 'Love & Slows'),
(52, 'No woman no cry (copie dans reggae)', 'B. Marley', 'Love & Slows'),
(53, 'Stil got the blues', 'G. Moore', 'Love & Slows'),
(54, 'Still loving you : 120', 'Scorpion', 'Love & Slows'),
(55, 'Nothing else matter : 121', 'Metallica', 'Love & Slows'),
(56, 'I Will always love you : 122', 'W. Houston', 'Love & Slows'),
(57, 'Vivo per lei', 'A. Boccelli & H. Ségara', 'Love & Slows'),
(58, 'La bas', 'J.J. Goldman', 'Love & Slows'),
(59, 'Heros', 'M. Carey', 'Love & Slows'),
(60, 'Glory Box', 'Portishead', 'Love & Slows'),
(61, 'Shallow (A Star is Born) - à venir', 'B. Cooper / Lady Gaga', 'Love & Slows'),
(62, 'My Immortal', 'Evanescence', 'Rock - Slow'),
(63, 'Belles, belles, belles', 'C. François', 'Sixties - Twist'),
(64, 'Twist again', 'C. Checker', 'Sixties - Twist'),
(65, 'Johnny be good', 'C. Berry', 'Sixties - Rock\'n roll'),
(66, 'Blues suedes shoes', 'E. Presley', 'Sixties - Rock\'n roll'),
(67, 'The One That I Want', 'Grease', 'Sixties - Rock\'n roll'),
(68, 'Last night (impro)', '', 'Sixties - Madison'),
(69, 'Champs Elysée', 'J. Dassin', 'Sixties - Madison'),
(70, 'Wonderwall', 'Oasis', 'Pop Music'),
(71, 'A ma place', 'A. Bauer & Zazie', 'Pop Music'),
(72, 'Hotel California', 'Eagles', 'Pop Music'),
(73, 'Cendrillon', 'Téléphone', 'Rock - Téléphone'),
(74, 'I Love Rock\'n roll', 'J. Jett', 'Rock - Téléphone'),
(75, 'Bohemian Rhapsody', 'Queen', 'Rock - Queen'),
(76, 'We are the Champion', 'Queen', 'Rock - Queen'),
(77, 'Show must go on', 'Queen', 'Rock - Queen'),
(78, 'Rock you', 'Queen', 'Rock - Queen'),
(79, 'Eye of the tiger', 'Survivor', 'BO'),
(80, 'Ghostbuster', 'R. Parker Jr', 'BO'),
(81, 'The power of love', 'Frankie Goes to Hollywood', 'BO'),
(82, 'Bring me to life', 'Evanescence', 'BO'),
(83, 'Shallow', 'B. Cooper, L. Gaga', 'BO'),
(84, 'Starlight', 'Muse', 'Rock around the world'),
(85, 'Zombie', 'Cranberries', 'Rock around the world'),
(86, 'Seven nation', 'White stripes', 'Rock around the world'),
(87, 'Feeling good', 'Muse', 'Rock around the world'),
(88, 'Beat it', 'M. Jackson', 'Rock around the world'),
(89, 'Gotta get away', 'The Offspring', 'Rock around the world'),
(90, 'Ca c\'est vraiment toi', 'Téléphone', 'Rock around the world'),
(91, 'Highway to Hell', 'ACDC', 'Rock around the world'),
(92, 'Smoke on the water', 'Deep Purple', 'Rock around the world'),
(93, 'Hold the line', 'Toto', 'Rock around the world'),
(94, 'I was made for loving you', 'Kiss', 'Rock around the world'),
(95, 'Que je t\'aime', 'J. Halliday', 'Rock - Johnny'),
(96, 'Allumer le feu', 'J. Halliday', 'Rock - Johnny'),
(97, 'L\'envie', 'J. Halliday', 'Rock - Johnny'),
(98, 'Je ne suis pas un héros', 'D. Balavoine', 'Rock frenchies'),
(99, 'Mourir Demain', 'P. Obispo / N. StPier', 'Rock frenchies'),
(100, 'Sunday bloody Sunday', 'U2', 'Rock - U2'),
(101, 'Where the streets have no name', 'U2', 'Rock - U2'),
(102, 'New year\'s day', 'U2', 'Rock - U2'),
(103, 'The kids aren\'t alright', 'The Offspring', 'Rock - Punk'),
(104, 'Pretty fly (for a white guy)', 'The Offspring', 'Rock - Punk'),
(105, 'Un jour en France', 'Noir Désir', 'Rock - Noir Désir'),
(106, 'L\'homme pressé', 'Noir Désir', 'Rock - Noir Désir'),
(107, 'Emma', 'Matmatah', 'Rock - Matmatah'),
(108, 'Lambé', 'Matmatah', 'Rock - Matmatah'),
(109, 'C\'est toi que je t\'aime', 'Inconnus', 'Rock - Matmatah'),
(110, 'Marcia Baila', 'Rita mitsouko', 'Ambiance / Fête'),
(111, 'La salsa du démon', 'Le Splendide', 'Ambiance / Fête'),
(112, 'Tourner les serviettes', 'P. Sebastien', 'Ambiance / Fête - P Sébastien'),
(113, 'Les sardines', 'P. Sebastien', 'Ambiance / Fête - P Sébastien'),
(114, 'Les lacs du Connemara', 'M. Sardou', 'Ambiance / Fête - Danse Folk'),
(115, 'Rolling in the deep', 'Adèle', 'Ambiance / Fête - Adèle'),
(116, 'Someone like you', 'Adèle', 'Ambiance / Fête - Adèle'),
(117, 'Get lucky', 'Daft Punk', 'Actualté'),
(118, 'Everybody', 'M. Solveig', 'Actualté'),
(119, 'Locked out of heaven', 'B. Mars', 'Actualté'),
(120, 'I Gotta Feeling', 'D. Getta', 'Actualté'),
(121, 'Chandelier', 'Sia', 'Actualté'),
(126, 'Lose Yourself', 'Eminem', 'Rap'),
(130, 'Requiem pour un fou', 'J. Hallyday', 'Pop - Johnny');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
