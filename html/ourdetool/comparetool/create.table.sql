CREATE TABLE `log` (
  `idLog` bigint(20) NOT NULL auto_increment,
  `id` int(11) NOT NULL,
  `Zeit` datetime NOT NULL,
  `IP` varchar(20) collate latin1_general_ci NOT NULL,
  `Datei` varchar(80) collate latin1_general_ci NOT NULL,
  `Get` text collate latin1_general_ci NOT NULL,
  `Post` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`idLog`),
  KEY `id` (`id`,`Zeit`,`IP`,`Datei`)
) ENGINE=MyISAM;