SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Create `chat` database.
--
CREATE DATABASE `chat`;

USE `chat`;

--
-- Table structure for table `example`.
--

CREATE TABLE IF NOT EXISTS `example` (
  `id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `users`.
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` char(128) DEFAULT NULL,
  `login` char(128) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump data from table `users`
--

INSERT INTO `users` (`id`, `name`, `login`, `created`, `created_by`)
VALUES
  (1, 'Vasia', 'Vasia', '2022-05-16 09:55:17', 1),
  (2, 'Petia', 'Petia', '2022-05-16 09:55:17', 2);

--
-- Table structure for table `messages`.
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `message` text,
  `user_from` int(11) DEFAULT NULL,
  `user_to` int(11) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump data from table `messages`
--

INSERT INTO `messages` (`id`, `message`, `user_from`, `user_to`, `created`, `created_by`)
VALUES
  (1, 'Message from 1 to 2', 1, 2, '2022-05-16 12:35:00', 1),
  (2, 'Message from 2 to 1', 2, 1, '2022-05-16 12:35:00', 2);

--
-- Table structure for table `my_friends`.
--

CREATE TABLE IF NOT EXISTS `my_friends` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `friend_id` int(11) DEFAULT NULL,
  `friend date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump data from table `my_friends`
--

INSERT INTO `my_friends` (`id`, `user_id`, `friend_id`, `friend date`, `created`, `created_by`)
VALUES
  (1, 1, 2, '2022-05-16 12:35:00', '2022-05-16 12:35:00', 1),
  (2, 2, 1, '2022-05-16 12:35:00', '2022-05-16 12:35:00', 2);

-- --------------------------------------------------------
