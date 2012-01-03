-- phpMyAdmin SQL Dump
-- version 3.3.7deb6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2012 at 09:19 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-7+squeeze3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bpn_git`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_monitors`
--

CREATE TABLE IF NOT EXISTS `active_monitors` (
  `order_id` int(10) unsigned NOT NULL,
  `tx_id` int(10) unsigned NOT NULL,
  `address` varchar(100) NOT NULL COMMENT 'Needed when sending notifications',
  `value` bigint(20) NOT NULL COMMENT 'in satoshi (100 million satoshi make a bitcoin)',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `failures` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`,`tx_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `active_monitors`
--


-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(40) NOT NULL,
  `value` varchar(20) NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `last_update`) VALUES
('BBE', '160231', '2012-01-02 09:15:01');

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `success` tinyint(1) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logins`
--


-- --------------------------------------------------------

--
-- Table structure for table `notifications_sent`
--

CREATE TABLE IF NOT EXISTS `notifications_sent` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `tx` varchar(255) NOT NULL COMMENT 'Hash (to survive a rebuild of the DB)',
  `address` varchar(100) NOT NULL,
  `value` bigint(20) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `notifications_sent`
--


-- --------------------------------------------------------

--
-- Table structure for table `notify_options`
--

CREATE TABLE IF NOT EXISTS `notify_options` (
  `notify_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `desc` text NOT NULL,
  `price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'in satoshi',
  PRIMARY KEY (`notify_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `notify_options`
--

INSERT INTO `notify_options` (`notify_id`, `name`, `desc`, `price`) VALUES
(1, 'Email', '', 0),
(2, 'HTTP POST', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` int(1) unsigned NOT NULL DEFAULT '1',
  `uid` int(10) unsigned NOT NULL,
  `confirmations` int(10) unsigned NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `orders`
--


-- --------------------------------------------------------

--
-- Table structure for table `order_address`
--

CREATE TABLE IF NOT EXISTS `order_address` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(100) NOT NULL,
  PRIMARY KEY (`order_id`,`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `order_address`
--


-- --------------------------------------------------------

--
-- Table structure for table `order_notify`
--

CREATE TABLE IF NOT EXISTS `order_notify` (
  `order_id` int(10) unsigned NOT NULL,
  `notify_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`order_id`,`notify_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_notify`
--


-- --------------------------------------------------------

--
-- Table structure for table `reset_pass_requests`
--

CREATE TABLE IF NOT EXISTS `reset_pass_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(32) NOT NULL,
  `requested_from` varchar(60) NOT NULL,
  `email` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `reset_pass_requests`
--


-- --------------------------------------------------------

--
-- Table structure for table `sequence`
--

CREATE TABLE IF NOT EXISTS `sequence` (
  `key` varchar(20) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sequence`
--

INSERT INTO `sequence` (`key`, `value`) VALUES
('last_tx', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `url` varchar(500) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `BTC` varchar(100) NOT NULL COMMENT 'Address to send funds to, to pay for notifycations that are not free',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `email`, `url`, `secret`, `BTC`) VALUES
(1, 'super', '22a081adb20f93c191603d4904ddfa11bf5d85687d34b3e98529574e5f52298208928f350d58f3108d4d82d4276913250fc33c143b17f48c0812f83b86089bfe', 'root@localhost', '', 'W5HDrY22R8F4ETXpPyQBdjQuaF9p2KjB', '');

INSERT INTO `notify_options` (`notify_id`, `name`, `desc`, `price`) VALUES ('3', 'Pubnub', '', '0');
