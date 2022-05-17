SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Create `chat` database.
--
CREATE DATABASE `chat`;

USE `chat`;

--
-- Table structure `example`
--

CREATE TABLE `example` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `messages`
--

CREATE TABLE `messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `message` text,
  `user_from` int(11) UNSIGNED NOT NULL,
  `user_to` int(11) UNSIGNED NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) UNSIGNED NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table data dump `messages`
--

INSERT INTO `messages` (`id`, `message`, `user_from`, `user_to`, `created`, `created_by`) VALUES
  (1, 'Message from 1 to 2', 1, 2, '2022-05-17 10:05:08', 1),
  (2, 'Message from 2 to 1', 2, 1, '2022-05-17 10:05:56', 2),
  (3, 'Message from 1 to 3', 1, 3, '2022-05-17 10:07:04', 1),
  (4, 'Message from 3 to 1', 3, 1, '2022-05-17 10:07:35', 3);

-- --------------------------------------------------------

--
-- Table structure `my_friends`
--

CREATE TABLE `my_friends` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `friend_id` int(11) UNSIGNED NOT NULL,
  `friend date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table data dump `my_friends`
--

INSERT INTO `my_friends` (`id`, `user_id`, `friend_id`, `friend date`, `created`, `created_by`) VALUES
(1, 1, 2, '2022-05-17 12:59:33', '2022-05-17 12:59:33', 1),
(2, 2, 1, '2022-05-17 12:59:33', '2022-05-17 12:59:33', 2),
(3, 1, 3, '2022-05-17 13:01:01', '2022-05-17 13:01:01', 1);

-- --------------------------------------------------------

--
-- Table structure `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` char(100) NOT NULL,
  `login` char(100) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table data dump `users`
--

INSERT INTO `users` (`id`, `name`, `login`, `created`, `created_by`) VALUES
(1, 'Vasia', 'vasia', '2022-05-17 09:55:50', 1),
(2, 'Petia', 'petia', '2022-05-17 09:55:50', 2),
(3, 'Ivan', 'ivan', '2022-05-17 13:00:36', 3);

--
-- Stored Table Indexes
--

--
-- Table indexes `example`
--
ALTER TABLE `example`
  ADD PRIMARY KEY (`id`);

--
-- Table indexes `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_to_user` (`user_to`),
  ADD KEY `messages_from_user` (`user_from`);

--
-- Table indexes `my_friends`
--
ALTER TABLE `my_friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `my_friends_to_user` (`user_id`),
  ADD KEY `my_friends_from_user` (`friend_id`);

--
-- Table indexes `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Foreign key constraints on stored tables
--

--
-- Table Foreign Key Constraints `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_from_user` FOREIGN KEY (`user_from`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_to_user` FOREIGN KEY (`user_to`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Table Foreign Key Constraints `my_friends`
--
ALTER TABLE `my_friends`
  ADD CONSTRAINT `my_friends_from_user` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `my_friends_to_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

-- --------------------------------------------------------
