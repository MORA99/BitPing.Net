--
-- Database: `simple_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `s1_addresses`
--

CREATE TABLE IF NOT EXISTS `s1_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `s1_payment`
--

CREATE TABLE IF NOT EXISTS `s1_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(100) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `confirmations` int(10) unsigned NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

