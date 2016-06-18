-- phpMyAdmin SQL Dump
-- version 4.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 18, 2016 at 06:45 PM
-- Server version: 5.6.22-cll-lve
-- PHP Version: 5.4.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `insthync_runewar`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements_true`
--

CREATE TABLE IF NOT EXISTS `achievements_true` (
  `achievement_id` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `unlock_status` int(11) NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `achievements_true`
--

INSERT INTO `achievements_true` (`achievement_id`, `unlock_status`, `name`, `description`, `image_url`) VALUES
('100051', 1, 'Player certification', 'Defined character''s name', '100051.png'),
('100052', 1, 'Beginner certification', 'Passed tutorial', '100052.png'),
('100053', 1, 'Defender Lv.1', 'Won 20 time', '100053.png'),
('100054', 2, 'Defender Lv.2', 'Won 80 time', '100054.png'),
('100055', 3, 'Defender Lv.3', 'Won 180 time', '100055.png'),
('100056', 4, 'Defender Lv.4', 'Won 300 time', '100056.png'),
('100057', 1, 'Killer Lv.1', 'Killed 100 soldiers', '100057.png'),
('100058', 2, 'Killer Lv.2', 'Killed 300 soldiers', '100058.png'),
('100059', 3, 'Killer Lv.3', 'Killed 600 soldiers', '100059.png'),
('100060', 4, 'Killer Lv.4', 'Killed 1000 soldiers', '100060.png'),
('100067', 1, 'Gold Spender Lv.1', 'Spent 5000 gold', '100067.png'),
('100068', 2, 'Gold Spender Lv.2', 'Spent 10000 gold', '100068.png'),
('100069', 3, 'Gold Spender Lv.3', 'Spent 20000 gold', '100069.png'),
('100070', 4, 'Gold Spender Lv.4', 'Spent 50000 gold', '100070.png'),
('100071', 1, 'Crystal Spender Lv.1', 'Spent 100 crystal', '100071.png'),
('100072', 2, 'Crystal Spender Lv.2', 'Spent 400 crystal', '100072.png'),
('100073', 3, 'Crystal Spender Lv.3', 'Spent 900 crystal', '100073.png'),
('100074', 4, 'Crystal Spender Lv.4', 'Spent 1600 crystal', '100074.png');

-- --------------------------------------------------------

--
-- Table structure for table `battle_match`
--

CREATE TABLE IF NOT EXISTS `battle_match` (
  `battleid` int(11) NOT NULL,
  `attackerid` int(11) NOT NULL,
  `defenderid` int(11) NOT NULL,
  `token` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_end` tinyint(1) NOT NULL COMMENT '0 = no, 1 = yes',
  `date_done` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `battle_result`
--

CREATE TABLE IF NOT EXISTS `battle_result` (
  `resultid` int(11) NOT NULL,
  `battleid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `result_flag` tinyint(1) NOT NULL COMMENT '0 = win, 1 = lose, 2 = draw',
  `is_seen` tinyint(1) NOT NULL COMMENT '0 = no, 1 = yes',
  `date_done` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `battle_reward`
--

CREATE TABLE IF NOT EXISTS `battle_reward` (
  `rewardid` int(11) NOT NULL,
  `battleid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_gold` int(11) NOT NULL,
  `reward_crystal` int(11) NOT NULL,
  `reward_itemid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `friendid` int(11) NOT NULL,
  `userid1` int(11) NOT NULL,
  `userid2` int(11) NOT NULL,
  `is_seen` tinyint(1) NOT NULL COMMENT '0 = no, 1 = yes',
  `date_done` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `friends_request`
--

CREATE TABLE IF NOT EXISTS `friends_request` (
  `requestid` int(11) NOT NULL,
  `userid1` int(11) NOT NULL,
  `userid2` int(11) NOT NULL,
  `is_seen` tinyint(1) NOT NULL COMMENT '0 = no, 1 = yes',
  `date_done` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_avatar`
--

CREATE TABLE IF NOT EXISTS `inventory_avatar` (
  `inventoryid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `char_index` tinyint(1) NOT NULL,
  `avatarid` int(11) NOT NULL,
  `used_gold` int(11) NOT NULL,
  `used_crystal` int(11) NOT NULL,
  `date_done` datetime NOT NULL,
  `date_expire` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_skill`
--

CREATE TABLE IF NOT EXISTS `inventory_skill` (
  `inventoryid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `char_index` tinyint(1) NOT NULL,
  `skillid` int(11) NOT NULL,
  `used_gold` int(11) NOT NULL,
  `used_crystal` int(11) NOT NULL,
  `date_done` datetime NOT NULL,
  `date_expire` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usage_avatar`
--

CREATE TABLE IF NOT EXISTS `usage_avatar` (
  `userid` int(11) NOT NULL,
  `char_archer` int(11) NOT NULL,
  `char_assasin` int(11) NOT NULL,
  `char_fighter` int(11) NOT NULL,
  `char_knight` int(11) NOT NULL,
  `char_hermit` int(11) NOT NULL,
  `char_mage` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usage_skill`
--

CREATE TABLE IF NOT EXISTS `usage_skill` (
  `userid` int(11) NOT NULL,
  `char_archer` int(11) NOT NULL,
  `char_assasin` int(11) NOT NULL,
  `char_fighter` int(11) NOT NULL,
  `char_knight` int(11) NOT NULL,
  `char_hermit` int(11) NOT NULL,
  `char_mage` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL,
  `username` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `trueid` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebookid` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `token` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL COMMENT '0 = normal, 1 = admin',
  `heartnum` int(11) NOT NULL,
  `used_achievement_id` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_done` datetime NOT NULL,
  `date_login` datetime NOT NULL,
  `date_heart_refill` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_achievements_true`
--

CREATE TABLE IF NOT EXISTS `users_achievements_true` (
  `userid` int(11) NOT NULL,
  `achievement_id` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_seen` tinyint(1) NOT NULL,
  `date_done` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_info`
--

CREATE TABLE IF NOT EXISTS `users_info` (
  `userid` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  `gold` int(11) NOT NULL,
  `crystal` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_stats`
--

CREATE TABLE IF NOT EXISTS `users_stats` (
  `userid` int(11) NOT NULL,
  `spent_gold` int(11) NOT NULL,
  `spent_crystal` int(11) NOT NULL,
  `fight_killed` int(11) NOT NULL,
  `fight_won` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements_true`
--
ALTER TABLE `achievements_true`
  ADD KEY `achievement_id` (`achievement_id`);

--
-- Indexes for table `battle_match`
--
ALTER TABLE `battle_match`
  ADD PRIMARY KEY (`battleid`);

--
-- Indexes for table `battle_result`
--
ALTER TABLE `battle_result`
  ADD PRIMARY KEY (`resultid`);

--
-- Indexes for table `battle_reward`
--
ALTER TABLE `battle_reward`
  ADD PRIMARY KEY (`rewardid`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`friendid`);

--
-- Indexes for table `friends_request`
--
ALTER TABLE `friends_request`
  ADD PRIMARY KEY (`requestid`);

--
-- Indexes for table `inventory_avatar`
--
ALTER TABLE `inventory_avatar`
  ADD PRIMARY KEY (`inventoryid`);

--
-- Indexes for table `inventory_skill`
--
ALTER TABLE `inventory_skill`
  ADD PRIMARY KEY (`inventoryid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `battle_match`
--
ALTER TABLE `battle_match`
  MODIFY `battleid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `battle_result`
--
ALTER TABLE `battle_result`
  MODIFY `resultid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `battle_reward`
--
ALTER TABLE `battle_reward`
  MODIFY `rewardid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `friendid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `friends_request`
--
ALTER TABLE `friends_request`
  MODIFY `requestid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inventory_avatar`
--
ALTER TABLE `inventory_avatar`
  MODIFY `inventoryid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inventory_skill`
--
ALTER TABLE `inventory_skill`
  MODIFY `inventoryid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
