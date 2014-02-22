-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 22, 2014 at 03:36 PM
-- Server version: 5.5.33-31.1
-- PHP Version: 5.3.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nchavez_qubmessenger`
--
CREATE DATABASE `nchavez_qubmessenger` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `nchavez_qubmessenger`;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE IF NOT EXISTS `api_keys` (
  `public_api_key` varchar(100) NOT NULL,
  `private_api_key_hash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`public_api_key`, `private_api_key_hash`) VALUES
('primary_app', 'O3tuZVKRhalS/yJbkbiyg/VakAOaI1ri');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `first_user_id` int(10) unsigned NOT NULL,
  `second_user_id` int(10) unsigned NOT NULL,
  `status` enum('1REQ','2REQ','CONF') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`first_user_id`, `second_user_id`, `status`) VALUES
(1, 2, 'CONF'),
(1, 3, 'CONF'),
(1, 4, 'CONF'),
(2, 3, 'CONF'),
(2, 4, 'CONF'),
(2, 5, '2REQ'),
(3, 4, 'CONF'),
(4, 5, '2REQ'),
(1, 5, 'CONF'),
(1, 6, 'CONF'),
(2, 6, 'CONF'),
(1, 7, 'CONF');

-- --------------------------------------------------------

--
-- Table structure for table `image_collections`
--

CREATE TABLE IF NOT EXISTS `image_collections` (
  `image_collection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cover_photo_path` varchar(100) DEFAULT NULL,
  `profile_pic_path_1` varchar(100) DEFAULT NULL,
  `profile_pic_path_2` varchar(100) DEFAULT NULL,
  `profile_pic_path_3` varchar(100) DEFAULT NULL,
  `profile_pic_path_4` varchar(100) DEFAULT NULL,
  `profile_pic_path_5` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`image_collection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `image_collections`
--

INSERT INTO `image_collections` (`image_collection_id`, `cover_photo_path`, `profile_pic_path_1`, `profile_pic_path_2`, `profile_pic_path_3`, `profile_pic_path_4`, `profile_pic_path_5`) VALUES
(1, 'uploads/cover_photos/nickCover.jpg', 'uploads/profile_pics/nickPic1.jpg', 'uploads/profile_pics/nickPic2.jpg', 'uploads/profile_pics/nickPic3.jpg', NULL, NULL),
(2, NULL, 'uploads/profile_pics/chrisPic1.jpg', NULL, NULL, NULL, NULL),
(3, 'uploads/cover_photos/angieCover.jpg', 'uploads/profile_pics/angiePic1.jpg', 'uploads/profile_pics/angiePic2.jpg', NULL, NULL, NULL),
(4, 'uploads/cover_photos/carolyneCover.jpg', 'uploads/profile_pics/carolynePic1.jpg', NULL, NULL, NULL, NULL),
(5, NULL, '/uploads/profile_pics/apuPic1.jpg', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `timestamp` int(10) unsigned NOT NULL,
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL,
  `content` tinytext NOT NULL,
  `has_image` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('S','R','D') NOT NULL DEFAULT 'S'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`timestamp`, `from_user_id`, `to_user_id`, `content`, `has_image`, `status`) VALUES
(1376060437, 1, 2, 'Ayoo bruh whas good?', 0, 'R'),
(1376060526, 2, 1, 'HAha nm dude hbu? I''m just here at 172.!', 0, 'R'),
(1376060566, 1, 2, 'Ok! meet me there o_0', 0, 'D'),
(1376060654, 3, 4, 'Yo Carolyne theres a Wanted concert tom! *)', 0, 'R'),
(1376060682, 4, 3, 'Angela stfu I don''t care or like the watned', 0, 'S'),
(1376060650, 1, 2, 'Check this out! :D', 1, 'S'),
(1376060700, 3, 1, 'Yo we is gone already halfway to new York city by now I think.', 0, 'S'),
(1376060750, 4, 1, 'Hi :)', 0, 'R');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `name` varchar(25) NOT NULL,
  `age` tinyint(4) NOT NULL,
  `sex` enum('M','F','O') NOT NULL DEFAULT 'O',
  `seeking` enum('M','F','O') NOT NULL DEFAULT 'O',
  `location` varchar(20) NOT NULL,
  `username_visible` tinyint(1) NOT NULL DEFAULT '0',
  `name_visible` tinyint(1) NOT NULL DEFAULT '0',
  `age_visible` tinyint(1) NOT NULL DEFAULT '1',
  `sex_visible` tinyint(1) NOT NULL DEFAULT '1',
  `seeking_visible` tinyint(1) NOT NULL DEFAULT '1',
  `location_visible` tinyint(1) NOT NULL DEFAULT '0',
  `password_hash` varchar(100) NOT NULL,
  `image_collection_id` int(10) unsigned DEFAULT NULL,
  `selected_image` tinyint(4) DEFAULT NULL,
  `num_profile_pics` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `name`, `age`, `sex`, `seeking`, `location`, `username_visible`, `name_visible`, `age_visible`, `sex_visible`, `seeking_visible`, `location_visible`, `password_hash`, `image_collection_id`, `selected_image`, `num_profile_pics`) VALUES
(1, 'nick95xD', 'Nick Chavez', 18, 'M', 'F', 'Queens NY', 0, 1, 1, 1, 1, 1, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', 1, 1, 3),
(2, 'YoSoy_Chris', 'Chris Romero', 18, 'M', 'F', 'New York City', 1, 1, 1, 1, 1, 1, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', 2, 1, 1),
(3, 'angiecapangie', 'Angie', 19, 'F', 'M', 'NY', 0, 0, 1, 1, 1, 0, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', 3, 1, 2),
(4, 'haroldsheeran', 'Carolyne Sheeran', 16, 'F', 'M', 'New York', 0, 1, 1, 1, 1, 1, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', 4, 1, 1),
(5, 'darkgardenia', 'Monica Toloza', 15, 'F', 'M', 'Queens', 0, 1, 1, 1, 1, 0, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', NULL, NULL, 0),
(6, 'apu22', 'Apu Nahasapeemapetilon', 42, 'M', 'F', 'Springfield', 1, 1, 0, 1, 1, 1, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', 5, 1, 1),
(7, 'abesimpson', 'Abe Simpson', 78, 'M', 'O', 'Retierment Castle', 0, 0, 1, 1, 1, 0, 'O/itZY/SdkeL1F/I0IuK9DO1Op1f3jza', NULL, NULL, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
