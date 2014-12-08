CREATE TABLE IF NOT EXISTS `#__getbible_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `books_nr` int(11) DEFAULT NULL,
  `chapter_nr` int(11) DEFAULT NULL,
  `verse_nr` int(11) DEFAULT NULL,
  `note` text NOT NULL,
  `access` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `books_nr` (`books_nr`),
  KEY `access` (`access`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;