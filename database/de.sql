-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 28. Mai 2021 um 18:16
-- Server-Version: 5.7.31-log
-- PHP-Version: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `de_ang_export`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_alliforum_posts`
--

CREATE TABLE `de_alliforum_posts` (
  `postid` int(11) UNSIGNED NOT NULL,
  `poster` varchar(20) NOT NULL DEFAULT '0',
  `post` text NOT NULL,
  `time` int(13) NOT NULL DEFAULT '0',
  `thread` int(11) NOT NULL DEFAULT '0',
  `title` varchar(25) NOT NULL DEFAULT '',
  `edit` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_alliforum_threads`
--

CREATE TABLE `de_alliforum_threads` (
  `id` int(11) UNSIGNED NOT NULL,
  `threadname` varchar(50) NOT NULL DEFAULT '',
  `creator` varchar(20) NOT NULL DEFAULT '0',
  `allytag` varchar(8) NOT NULL DEFAULT '0',
  `lastposter` varchar(20) NOT NULL DEFAULT '0',
  `lastactive` int(13) NOT NULL DEFAULT '0',
  `open` int(1) NOT NULL DEFAULT '1',
  `anzposts` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `gelesen` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `wichtig` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_allys`
--

CREATE TABLE `de_allys` (
  `id` int(10) UNSIGNED NOT NULL,
  `allyname` varchar(50) NOT NULL DEFAULT '',
  `allytag` varchar(8) NOT NULL DEFAULT '',
  `regierungsform` varchar(20) NOT NULL DEFAULT '',
  `allianzform` varchar(20) NOT NULL DEFAULT '',
  `ausrichtung` varchar(20) NOT NULL DEFAULT '',
  `homepage` varchar(50) NOT NULL DEFAULT '',
  `besonderheiten` text NOT NULL,
  `leaderid` int(12) NOT NULL DEFAULT '0',
  `coleaderid1` int(12) NOT NULL DEFAULT '-1',
  `coleaderid2` int(12) NOT NULL DEFAULT '-1',
  `coleaderid3` int(12) NOT NULL DEFAULT '-1',
  `fleetcommander1` int(12) NOT NULL DEFAULT '-1',
  `fleetcommander2` int(12) NOT NULL DEFAULT '-1',
  `tacticalofficer1` int(12) NOT NULL DEFAULT '-1',
  `tacticalofficer2` int(12) NOT NULL DEFAULT '-1',
  `memberofficer1` int(12) NOT NULL DEFAULT '-1',
  `memberofficer2` int(12) NOT NULL DEFAULT '-1',
  `leadername` varchar(30) NOT NULL DEFAULT 'Allianzleader',
  `coleadername1` varchar(30) NOT NULL DEFAULT 'Co-Leader',
  `coleadername2` varchar(30) NOT NULL DEFAULT 'Co-Leader',
  `coleadername3` varchar(30) NOT NULL DEFAULT 'Co-Leader',
  `fcname1` varchar(30) NOT NULL DEFAULT 'Fleet Commander',
  `fcname2` varchar(30) NOT NULL DEFAULT 'Fleet Commander',
  `toname1` varchar(30) NOT NULL DEFAULT 'Tactical Officer',
  `toname2` varchar(30) NOT NULL DEFAULT 'Tactical Officer',
  `moname1` varchar(30) NOT NULL DEFAULT 'Member Officer',
  `moname2` varchar(30) NOT NULL DEFAULT 'Member Officer',
  `hfn_forwarding` char(1) NOT NULL DEFAULT '0',
  `t_depot` int(10) NOT NULL DEFAULT '0',
  `memberlimit` smallint(2) UNSIGNED NOT NULL DEFAULT '1',
  `maxmembercount` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `maxcolcount` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `maxscorecount` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `openirc` varchar(50) NOT NULL DEFAULT '',
  `internirc` varchar(50) NOT NULL DEFAULT '',
  `metairc` varchar(50) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `leadermessage` text NOT NULL,
  `bewerberinfo` text NOT NULL,
  `public_activity` char(1) NOT NULL DEFAULT '',
  `colstolen` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `coldestroy` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `collost` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `colstolennpc` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `questpoints` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `questtyp` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `questreach` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `questgoal` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `questtime` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artefacts` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `bldg0` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg1` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg2` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg3` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg4` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg5` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg6` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg7` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg8` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `bldg9` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `tronic_zahlungsziel` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `eh_gestellt_anz` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `bgscore2` mediumint(9) NOT NULL DEFAULT '0',
  `mission_counter_1` int(11) NOT NULL DEFAULT '0',
  `mission_counter_2` int(11) NOT NULL DEFAULT '0',
  `discord_bot` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_antrag`
--

CREATE TABLE `de_ally_antrag` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ally_id` int(11) NOT NULL DEFAULT '0',
  `antrag` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_buendniss_antrag`
--

CREATE TABLE `de_ally_buendniss_antrag` (
  `ally_id_antragsteller` int(11) NOT NULL DEFAULT '0',
  `ally_id_partner` int(11) NOT NULL DEFAULT '0',
  `antrag` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_history`
--

CREATE TABLE `de_ally_history` (
  `id` int(11) NOT NULL,
  `allytag` varchar(15) NOT NULL DEFAULT '',
  `allyid` int(11) NOT NULL DEFAULT '0',
  `entry` varchar(255) NOT NULL DEFAULT '',
  `timestamp` int(10) NOT NULL DEFAULT '0',
  `displaydate` varchar(30) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_partner`
--

CREATE TABLE `de_ally_partner` (
  `ally_id_1` int(11) NOT NULL DEFAULT '0',
  `ally_id_2` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_scans`
--

CREATE TABLE `de_ally_scans` (
  `id` int(11) NOT NULL,
  `owner_allytag` varchar(20) NOT NULL DEFAULT '',
  `target_allytag` varchar(20) NOT NULL DEFAULT '',
  `target_allyname` varchar(255) NOT NULL DEFAULT '',
  `target_memberlist` text NOT NULL,
  `scandate` varchar(30) NOT NULL DEFAULT '',
  `timestamp` int(14) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_stat`
--

CREATE TABLE `de_ally_stat` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `datum` varchar(10) NOT NULL DEFAULT '',
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `col` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `platz` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `member` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_storage`
--

CREATE TABLE `de_ally_storage` (
  `ally_id` int(10) UNSIGNED NOT NULL,
  `item_id` mediumint(8) UNSIGNED NOT NULL,
  `item_amount` bigint(20) NOT NULL,
  `item_wt_change` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_ally_war`
--

CREATE TABLE `de_ally_war` (
  `ally_id_angreifer` int(11) NOT NULL DEFAULT '0',
  `ally_id_angegriffener` int(11) NOT NULL DEFAULT '0',
  `kriegsstart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `friedensangebot` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_artefakt`
--

CREATE TABLE `de_artefakt` (
  `id` int(10) UNSIGNED NOT NULL,
  `artname` text NOT NULL,
  `artdesc` text NOT NULL,
  `sector` smallint(6) NOT NULL DEFAULT '0',
  `color` varchar(6) NOT NULL DEFAULT '',
  `wm` mediumint(9) NOT NULL DEFAULT '0',
  `picid` smallint(5) UNSIGNED NOT NULL,
  `artname1` text NOT NULL,
  `artdesc1` text NOT NULL,
  `artname2` text NOT NULL,
  `artdesc2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_artefakt`
--

INSERT INTO `de_artefakt` (`id`, `artname`, `artdesc`, `sector`, `color`, `wm`, `picid`, `artname1`, `artdesc1`, `artname2`, `artdesc2`) VALUES
(1, 'Die Schale von Sabrulia', 'Die Schale von Sabrulia manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', -1, '3399FF', 0, 1, 'Die Schale von Sabrulia', 'Die Schale von Sabrulia manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', '', ''),
(2, 'Der Spiegel von Calderan', 'Der Spiegel von Calderan verursacht durch eine temporale Anomalie unter Umständen eine Verdoppelung eines Kollektors, da dieser auf einer ähnlichen Hyperfrequenz arbeitet. Die Chance dafür beträgt {WERT6}%.', -1, 'FF0000', 10, 2, 'Der Spiegel von Calderan', 'Der Spiegel von Calderan verursacht durch eine temporale Anomalie unter Umständen eine Verdoppelung eines Kollektors, da dieser auf einer ähnlichen Hyperfrequenz arbeitet. Die Chance dafür beträgt {WERT6}%.', '', ''),
(3, 'Der Spiegel von Coltassa', 'Der Spiegel von Coltassa verursacht durch eine temporale Anomalie unter Umständen eine Verdoppelung eines Kollektors, da dieser auf einer ähnlichen Hyperfrequenz arbeitet. Die Chance dafür beträgt {WERT6}%.', 4, 'FF0000', 10, 3, 'Der Spiegel von Coltassa', 'Der Spiegel von Coltassa verursacht durch eine temporale Anomalie unter Umständen eine Verdoppelung eines Kollektors, da dieser auf einer ähnlichen Hyperfrequenz arbeitet. Die Chance dafür beträgt {WERT6}%.', '', ''),
(4, 'Die Schale von Kesh-Ha', 'Die Schale von Kesh-Ha manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', -1, 'FAFC42', 0, 4, 'Die Schale von Kesh-Ha', 'Die Schale von Kesh-Ha manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', '', ''),
(5, 'Die Schale von Kesh-Na', 'Die Schale von Kesh-Na manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', 9, 'FAFC42', 0, 5, 'Die Schale von Kesh-Na', 'Die Schale von Kesh-Na manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', '', ''),
(6, 'Die Schale von Kesh-Za', 'Die Schale von Kesh-Za manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', -1, 'FAFC42', 0, 6, 'Die Schale von Kesh-Za', 'Die Schale von Kesh-Za manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren.', '', ''),
(7, 'Der Strom von Kiz-Murat', 'Der Strom von Kiz-Murat erhöht das Grundeinkommen um {WERT2} Multiplex.', -1, '1DFF87', 0, 7, 'Der Strom von Kiz-Murat', 'Der Strom von Kiz-Murat erhöht das Grundeinkommen um {WERT2} Multiplex.', '', ''),
(8, 'Der Strom von Kiz-Joar', 'Der Strom von Kiz-Joar erhöht das Grundeinkommen um {WERT3} Dyharra.', -1, '1DFF87', 0, 8, 'Der Strom von Kiz-Joar', 'Der Strom von Kiz-Joar erhöht das Grundeinkommen um {WERT3} Dyharra.', '', ''),
(9, 'Der Strom von Kiz-Benir', 'Der Strom von Kiz-Benir erhöht das Grundeinkommen um {WERT4} Iradium.', -1, '1DFF87', 0, 9, 'Der Strom von Kiz-Benir', 'Der Strom von Kiz-Benir erhöht das Grundeinkommen um {WERT4} Iradium.', '', ''),
(10, 'Der Strom von Kiz-Vokl', 'Der Strom von Kiz-Vokl erhöht das Grundeinkommen um {WERT5} Eternium.', 8, '1DFF87', 0, 10, 'Der Strom von Kiz-Vokl', 'Der Strom von Kiz-Vokl erhöht das Grundeinkommen um {WERT5} Eternium.', '', ''),
(11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(12, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(13, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(14, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(15, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(16, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(17, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(18, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(19, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(20, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', -1, 'E15001', 0, 11, 'Die Gabe der Reichen', 'Die Gabe der Reichen manipuliert die Hyperraumkonstante und führt somit zu einer um {WERT1}% erhöhten Energieausschüttung bei den Kollektoren. Dieses Artefakt befindet sich immer im schlechtesten Spielersektor und kann nicht mit einer Sektorflotte erobert werden.', '', ''),
(21, 'Das Grab des Ra', 'Das Grab des Ra überwacht sämtlichen Transmitterverkehr im Sektor und senkt die Chance von feindlichen Agenteneinsätzen. Die maximale Erfolgschance liegt bei {WERT6}%.', 5, '8a0fc3', 10, 12, 'Das Grab des Ra', 'Das Grab des Ra überwacht sämtlichen Transmitterverkehr im Sektor und senkt die Chance von feindlichen Agenteneinsätzen. Die maximale Erfolgschance liegt bei {WERT6}%.', '', ''),
(22, 'Der Schild des Herakles', 'Der Schild des Herakles verändert das Raum-Zeit-Gefüge soweit, dass die primitiven Zielsysteme von Sonden nicht mehr in der Lage sind sich innerhalb des Sektors zu orientieren und somit kein Ziel finden.', -1, 'bd4cf2', 10, 13, 'Der Schild des Herakles', 'Der Schild des Herakles verändert das Raum-Zeit-Gefüge soweit, dass die primitiven Zielsysteme von Sonden nicht mehr in der Lage sind sich innerhalb des Sektors zu orientieren und somit kein Ziel finden.', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_auction`
--

CREATE TABLE `de_auction` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `creator` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `bidder` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `start_wt` bigint(20) NOT NULL DEFAULT '0',
  `cost` text NOT NULL,
  `reward` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_basedata_map_kanten`
--

CREATE TABLE `de_basedata_map_kanten` (
  `sec_id` mediumint(9) NOT NULL,
  `knoten_id1` mediumint(9) NOT NULL,
  `knoten_id2` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `de_basedata_map_kanten`
--

INSERT INTO `de_basedata_map_kanten` (`sec_id`, `knoten_id1`, `knoten_id2`) VALUES
(0, 1, 5),
(0, 5, 4),
(0, 9, 1),
(0, 8, 1),
(1, 1, 3),
(0, 3, 8),
(0, 7, 3),
(0, 6, 2),
(0, 2, 7),
(0, 4, 6),
(0, 5, 13),
(0, 9, 3),
(0, 9, 2),
(0, 9, 4),
(0, 8, 12),
(0, 7, 11),
(0, 6, 10),
(1, 3, 4),
(1, 3, 5),
(1, 2, 5),
(2, 1, 2),
(2, 3, 2),
(3, 1, 4),
(3, 1, 2),
(3, 3, 2),
(3, 3, 4),
(4, 3, 4),
(4, 1, 3),
(4, 3, 2),
(4, 3, 5),
(5, 5, 6),
(5, 6, 7),
(5, 7, 8),
(5, 8, 5),
(5, 8, 2),
(5, 8, 4),
(5, 2, 1),
(5, 1, 4),
(6, 1, 5),
(6, 5, 3),
(6, 5, 4),
(6, 5, 2),
(7, 1, 5),
(7, 5, 2),
(7, 5, 4),
(7, 5, 3),
(8, 4, 1),
(8, 2, 3),
(8, 1, 2),
(8, 3, 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_basedata_map_knoten`
--

CREATE TABLE `de_basedata_map_knoten` (
  `sec_id` mediumint(9) NOT NULL,
  `knoten_id` mediumint(9) NOT NULL,
  `pos_x` mediumint(9) NOT NULL,
  `pos_y` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_basedata_map_knoten`
--

INSERT INTO `de_basedata_map_knoten` (`sec_id`, `knoten_id`, `pos_x`, `pos_y`) VALUES
(0, 9, 463, 481),
(0, 11, 775, 782),
(0, 10, 153, 802),
(0, 4, 81, 452),
(0, 13, 160, 151),
(0, 5, 330, 347),
(0, 2, 468, 917),
(1, 1, 195, 176),
(0, 1, 461, 103),
(0, 6, 347, 638),
(0, 7, 648, 637),
(1, 2, 705, 248),
(1, 3, 389, 444),
(1, 4, 184, 777),
(1, 5, 732, 775),
(0, 12, 755, 165),
(2, 1, 166, 849),
(0, 3, 883, 463),
(0, 8, 623, 334),
(2, 2, 382, 492),
(2, 3, 823, 175),
(3, 1, 504, 178),
(3, 2, 205, 433),
(3, 3, 303, 802),
(3, 4, 723, 577),
(4, 1, 149, 150),
(4, 2, 808, 177),
(4, 3, 450, 454),
(4, 4, 111, 899),
(4, 5, 829, 901),
(5, 1, 829, 124),
(5, 2, 534, 165),
(5, 8, 470, 483),
(5, 4, 770, 416),
(5, 5, 108, 572),
(5, 6, 121, 901),
(5, 7, 455, 708),
(6, 1, 149, 145),
(6, 2, 834, 144),
(6, 3, 158, 853),
(6, 4, 810, 845),
(6, 5, 512, 500),
(7, 1, 153, 486),
(7, 2, 806, 491),
(7, 3, 451, 143),
(7, 4, 441, 825),
(7, 5, 449, 467),
(8, 1, 521, 124),
(8, 2, 125, 494),
(8, 3, 464, 857),
(8, 4, 902, 515);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_basedata_map_sector`
--

CREATE TABLE `de_basedata_map_sector` (
  `sec_id` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `typ` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `de_basedata_map_sector`
--

INSERT INTO `de_basedata_map_sector` (`sec_id`, `active`, `typ`) VALUES
(0, 1, 0),
(1, 1, 0),
(2, 1, 0),
(3, 1, 0),
(4, 1, 0),
(5, 1, 0),
(6, 1, 0),
(7, 1, 0),
(8, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_chat_msg`
--

CREATE TABLE `de_chat_msg` (
  `id` int(10) UNSIGNED NOT NULL,
  `channel` mediumint(18) UNSIGNED NOT NULL DEFAULT '0',
  `channeltyp` tinyint(3) UNSIGNED NOT NULL,
  `spielername` varchar(20) NOT NULL DEFAULT 'anonymous',
  `message` text NOT NULL,
  `timestamp` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `owner_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_dez_ausgaben`
--

CREATE TABLE `de_dez_ausgaben` (
  `id` int(11) NOT NULL,
  `zid` int(11) NOT NULL DEFAULT '0',
  `titel` varchar(50) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT '0',
  `ausgabe` text NOT NULL,
  `preis` smallint(11) NOT NULL DEFAULT '0',
  `gekauft` text NOT NULL,
  `frei` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_dez_zeitung`
--

CREATE TABLE `de_dez_zeitung` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `kategorie` smallint(6) NOT NULL DEFAULT '1',
  `userid` int(11) NOT NULL DEFAULT '0',
  `nick` varchar(50) NOT NULL DEFAULT '',
  `logo` varchar(150) NOT NULL DEFAULT '',
  `logofrei` smallint(6) NOT NULL DEFAULT '0',
  `aktuell` int(11) NOT NULL DEFAULT '0',
  `abonenten` text NOT NULL,
  `eingestellt` smallint(6) NOT NULL DEFAULT '0',
  `atemp` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_hfn_buddy_ignore`
--

CREATE TABLE `de_hfn_buddy_ignore` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `sector` smallint(6) NOT NULL DEFAULT '0',
  `system` tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(25) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_hfn_usr_ally`
--

CREATE TABLE `de_hfn_usr_ally` (
  `allytag` varchar(7) NOT NULL DEFAULT '',
  `absender` int(11) NOT NULL DEFAULT '0',
  `fromsec` smallint(6) DEFAULT NULL,
  `fromsys` smallint(6) DEFAULT NULL,
  `fromnic` varchar(20) NOT NULL DEFAULT '',
  `time` bigint(20) UNSIGNED NOT NULL,
  `betreff` tinytext NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_item_data`
--

CREATE TABLE `de_item_data` (
  `item_id` mediumint(8) UNSIGNED NOT NULL,
  `item_name` text NOT NULL,
  `item_quality` tinyint(4) NOT NULL,
  `item_sort_order` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `item_blueprint` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `de_item_data`
--

INSERT INTO `de_item_data` (`item_id`, `item_name`, `item_quality`, `item_sort_order`, `item_blueprint`) VALUES
(1, 'Palenium', 0, 1, ''),
(2, 'Titanen-Energiekern', 1, 2, ''),
(3, 'Eisen', 0, 3, ''),
(4, 'Titan', 0, 4, ''),
(5, 'Mexit', 0, 5, ''),
(6, 'Dulexit', 0, 6, ''),
(7, 'Tekranit', 0, 7, ''),
(8, 'Ylesenium', 0, 8, ''),
(9, 'Serodium', 0, 9, ''),
(10, 'Rowalganium', 0, 10, ''),
(11, 'Sextagit', 0, 11, ''),
(12, 'Octagium', 0, 12, ''),
(13, 'Quantenglimmer', 0, 13, ''),
(14, 'Handelswaren I', 0, 14, 'I3x400;I4x200;Z24;P24x2'),
(15, 'Handelswaren II', 0, 15, 'I5x400;I6x200;Z24;P23x2'),
(16, 'Handelswaren III', 0, 16, 'I7x400;I8x200;Z24;P22x2'),
(17, 'Handelswaren IV', 0, 17, 'I9x400;I10x200;Z24;P21x2'),
(18, 'Handelswaren V', 0, 18, 'I11x400;I12x200;Z24;P20x2'),
(19, 'Drasogi-Kristall', 0, 19, ''),
(20, 'Infiltrator Omega', 0, 20, 'I3x1000;I4x500;I19x1;Z24;P24x10');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_login`
--

CREATE TABLE `de_login` (
  `user_id` mediumint(9) NOT NULL,
  `owner_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `nic` varchar(100) NOT NULL DEFAULT '',
  `reg_mail` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(255) DEFAULT NULL,
  `level` mediumint(3) NOT NULL DEFAULT '1',
  `register` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_click` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `com_sperre` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `supporter` varchar(40) NOT NULL DEFAULT '',
  `logins` smallint(6) NOT NULL DEFAULT '0',
  `points` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `lastpopup` int(11) UNSIGNED DEFAULT NULL,
  `newpass` varchar(32) DEFAULT NULL,
  `sendmail` tinyint(4) NOT NULL DEFAULT '0',
  `savestatus` tinyint(1) NOT NULL DEFAULT '0',
  `inaktmail` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `blocktime` int(11) NOT NULL DEFAULT '0',
  `activetime` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `loginkey` varchar(16) NOT NULL DEFAULT '',
  `loginkeytime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `loginkeyip` varchar(15) NOT NULL DEFAULT '',
  `delmode` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `cooperation` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_map_kanten`
--

CREATE TABLE `de_map_kanten` (
  `knoten_id1` int(11) NOT NULL,
  `knoten_id2` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_map_objects`
--

CREATE TABLE `de_map_objects` (
  `id` int(10) UNSIGNED NOT NULL,
  `data` mediumtext NOT NULL,
  `system_typ` mediumint(9) NOT NULL,
  `system_subtyp` mediumint(9) NOT NULL,
  `always_visible` tinyint(4) NOT NULL,
  `pos_x` decimal(10,6) NOT NULL,
  `pos_y` decimal(10,6) NOT NULL,
  `cluster_x` mediumint(9) NOT NULL,
  `cluster_y` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_news_overview`
--

CREATE TABLE `de_news_overview` (
  `id` int(5) UNSIGNED NOT NULL,
  `typ` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `betreff` varchar(50) NOT NULL DEFAULT '',
  `nachricht` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `klicks` int(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_news_sector`
--

CREATE TABLE `de_news_sector` (
  `id` int(10) UNSIGNED NOT NULL,
  `wt` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `typ` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `sector` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_news_server`
--

CREATE TABLE `de_news_server` (
  `id` int(10) UNSIGNED NOT NULL,
  `wt` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `typ` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sector`
--

CREATE TABLE `de_sector` (
  `sec_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `bk` tinyint(4) NOT NULL DEFAULT '0',
  `restyp01` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `restyp02` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `restyp03` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `restyp04` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `restyp05` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `techs` varchar(10) NOT NULL DEFAULT 's000000000',
  `buildgnr` mediumint(9) NOT NULL DEFAULT '0',
  `buildgtime` mediumint(9) NOT NULL DEFAULT '0',
  `skmes` text NOT NULL,
  `e1` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e2` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `zielsec` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `aktion` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `zeit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `aktzeit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `gesrzeit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `ssteuer` int(3) NOT NULL DEFAULT '5',
  `platz` mediumint(9) NOT NULL DEFAULT '0',
  `pass` varchar(10) NOT NULL DEFAULT '',
  `platz_last_day` int(4) NOT NULL DEFAULT '0',
  `npc` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `votetimer` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `votecounter` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `col` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `ekey` varchar(11) NOT NULL,
  `tempcol` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `arthold` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_sector`
--

INSERT INTO `de_sector` (`sec_id`, `name`, `url`, `bk`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `techs`, `buildgnr`, `buildgtime`, `skmes`, `e1`, `e2`, `zielsec`, `aktion`, `zeit`, `aktzeit`, `gesrzeit`, `ssteuer`, `platz`, `pass`, `platz_last_day`, `npc`, `votetimer`, `votecounter`, `col`, `ekey`, `tempcol`, `arthold`) VALUES
(1, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 82001, 0),
(2, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 1, 0, 0, 0, '100;0;0;0', 400, 0),
(3, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 171, 0),
(4, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 314, 0),
(5, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 117, 0),
(6, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 66, 0),
(7, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 84, 0),
(8, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 38, 0),
(9, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 153, 0),
(10, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 174, 0),
(11, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 170, 0),
(12, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 653, 0),
(13, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 124, 0),
(14, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 306, 0),
(15, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 623, 0),
(16, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 749, 0),
(17, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 695, 0),
(18, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 192, 0),
(19, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 96, 0),
(20, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 245, 0),
(21, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2136, 0),
(22, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 345, 0),
(23, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 3392, 0),
(24, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(25, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 1924, 0),
(26, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 345, 0),
(27, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 1627, 0),
(28, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(29, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 1245, 0),
(30, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(31, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 3487, 0),
(32, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(33, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 1228, 0),
(34, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(35, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 1796, 0),
(36, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(37, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2225, 0),
(38, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(39, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2539, 0),
(40, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(41, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2580, 0),
(42, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(43, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 1528, 0),
(44, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(45, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2032, 0),
(46, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(47, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2136, 0),
(48, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(49, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 2914, 0),
(50, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(51, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 651, 0),
(52, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(53, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 802, 0),
(54, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(55, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(56, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 236, 0),
(57, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 362, 0),
(58, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(59, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(60, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(61, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 79237, 0),
(62, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 324, 0),
(63, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 44837, 0),
(64, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(65, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 72482, 0),
(66, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(67, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 43531, 0),
(68, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(69, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 60541, 0),
(70, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(71, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 30436, 0),
(72, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(73, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 42385, 0),
(74, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 345, 0),
(75, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 116541, 0),
(76, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(77, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 62095, 0),
(78, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(79, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 42867, 0),
(80, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(81, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 51239, 0),
(82, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(83, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 84709, 0),
(84, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(85, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 23063, 0),
(86, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(87, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 59844, 0),
(88, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(89, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 50672, 0),
(90, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(91, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 112010, 0),
(92, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(93, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 54993, 0),
(94, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(95, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 133905, 0),
(96, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(97, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 46487, 0),
(98, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(99, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 47572, 0),
(100, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(101, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 81165, 0),
(102, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 345, 0),
(103, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 44089, 0),
(104, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(105, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 89794, 0),
(106, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(107, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 82487, 0),
(108, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(109, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 108658, 0),
(110, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(111, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 84298, 0),
(112, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(113, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 152999, 0),
(114, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 301, 0),
(115, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 62348, 0),
(116, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 305, 0),
(117, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 62454, 0),
(118, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(119, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 54422, 0),
(120, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(121, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 76427, 0),
(122, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(123, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 57403, 0),
(124, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(125, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 57585, 0),
(126, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(127, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 64198, 0),
(128, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(129, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 52267, 0),
(130, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(131, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 48854, 0),
(132, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(133, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 53203, 0),
(134, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(135, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 63631, 0),
(136, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(137, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 74774, 0),
(138, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(139, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 64044, 0),
(140, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(141, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 58653, 0),
(142, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(143, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 73142, 0),
(144, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(145, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 69731, 0),
(146, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(147, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 44612, 0),
(148, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(149, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 109626, 0),
(150, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(151, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 82558, 0),
(152, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 360, 0),
(153, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 139398, 0),
(154, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 180, 0),
(155, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 73044, 0),
(156, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(157, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 40901, 0),
(158, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(159, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 31514, 0),
(160, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(161, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 59009, 0),
(162, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 354, 0),
(163, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 40358, 0),
(164, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(165, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 63096, 0),
(166, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 307, 0),
(167, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 119169, 0),
(168, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(169, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 92022, 0),
(170, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(171, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 30, 0),
(172, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(173, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 51, 0),
(174, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(175, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 9, 0),
(176, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(177, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 52, 0),
(178, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(179, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 29, 0),
(180, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(181, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 8837, 0),
(182, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(183, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(184, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(185, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(186, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(187, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(188, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 217, 0),
(189, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(190, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 325, 0),
(191, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(192, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 38, 0),
(193, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(194, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(195, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(196, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(197, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(198, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 170, 0),
(199, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(200, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 132, 0),
(201, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(202, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 256, 0),
(203, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(204, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(205, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(206, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 225, 0),
(207, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(208, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(209, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(210, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 170, 0),
(211, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(212, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(213, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(214, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 111, 0),
(215, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(216, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(217, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(218, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 400, 0),
(219, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(220, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 261, 0),
(221, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(222, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(223, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(224, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(225, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(226, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(227, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(228, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 165, 0),
(229, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(230, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 247, 0),
(231, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(232, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 25, 0),
(233, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(234, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(235, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(236, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(237, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(238, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(239, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(240, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(241, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(242, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(243, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(244, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(245, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(246, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(247, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(248, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(249, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(250, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(251, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(252, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(253, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(254, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(255, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(256, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(257, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(258, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(259, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(260, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(261, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(262, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(263, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(264, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(265, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(266, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(267, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(268, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(269, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(270, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(271, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(272, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(273, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(274, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(275, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(276, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(277, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(278, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(279, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(280, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(281, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(282, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(283, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(284, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 200, 0),
(285, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(286, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(287, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(288, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(289, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(290, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(291, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(292, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(293, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(294, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(295, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(296, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(297, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(298, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(299, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(300, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(301, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(302, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(303, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(304, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(305, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(306, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(307, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(308, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(309, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(310, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(311, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(312, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(313, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(314, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(315, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(316, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(317, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(318, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(319, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(320, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(321, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(322, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(323, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(324, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(325, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(326, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(327, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(328, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(329, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(330, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(331, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(332, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(333, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(334, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(335, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(336, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(337, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(338, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(339, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(340, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(341, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(342, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(343, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(344, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(345, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(346, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(347, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(348, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(349, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(350, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(351, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(352, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(353, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(354, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(355, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(356, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(357, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(358, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(359, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(360, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(361, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(362, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(363, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(364, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(365, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(366, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(367, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(368, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(369, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(370, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(371, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(372, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(373, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(374, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(375, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(376, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(377, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(378, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(379, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(380, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(381, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(382, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(383, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(384, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(385, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(386, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(387, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(388, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(389, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(390, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(391, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(392, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(393, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(394, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(395, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(396, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(397, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(398, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(399, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(400, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(401, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(402, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(403, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(404, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(405, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(406, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(407, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(408, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(409, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(410, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0);
INSERT INTO `de_sector` (`sec_id`, `name`, `url`, `bk`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `techs`, `buildgnr`, `buildgtime`, `skmes`, `e1`, `e2`, `zielsec`, `aktion`, `zeit`, `aktzeit`, `gesrzeit`, `ssteuer`, `platz`, `pass`, `platz_last_day`, `npc`, `votetimer`, `votecounter`, `col`, `ekey`, `tempcol`, `arthold`) VALUES
(411, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(412, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(413, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(414, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(415, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(416, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(417, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(418, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(419, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(420, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(421, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(422, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(423, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(424, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(425, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(426, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(427, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(428, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(429, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(430, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(431, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(432, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(433, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(434, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(435, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(436, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(437, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(438, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(439, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(440, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(441, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(442, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(443, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(444, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(445, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(446, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(447, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(448, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(449, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(450, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(451, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(452, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(453, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(454, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(455, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(456, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(457, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(458, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(459, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(460, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(461, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(462, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(463, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(464, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(465, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(466, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(467, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(468, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(469, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(470, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(471, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(472, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(473, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(474, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(475, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(476, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(477, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(478, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(479, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(480, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(481, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(482, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(483, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(484, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(485, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(486, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(487, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(488, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(489, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(490, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(491, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(492, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(493, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(494, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(495, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(496, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(497, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(498, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(499, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(500, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(501, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(502, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(503, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(504, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(505, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(506, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(507, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(508, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(509, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(510, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(511, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(512, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(513, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(514, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(515, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(516, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(517, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(518, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(519, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(520, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(521, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(522, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(523, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(524, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(525, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(526, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(527, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(528, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(529, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(530, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(531, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(532, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(533, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(534, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(535, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(536, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(537, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(538, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(539, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(540, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(541, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(542, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(543, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(544, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(545, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(546, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(547, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(548, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(549, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(550, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(551, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(552, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(553, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(554, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(555, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(556, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(557, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(558, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(559, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(560, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(561, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(562, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(563, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(564, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(565, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(566, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(567, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(568, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(569, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(570, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(571, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(572, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(573, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(574, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(575, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(576, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(577, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(578, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(579, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(580, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(581, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(582, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(583, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(584, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(585, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(586, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(587, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(588, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(589, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(590, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(591, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(592, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(593, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(594, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(595, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(596, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(597, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(598, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(599, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(600, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(601, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(602, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(603, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(604, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(605, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(606, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(607, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(608, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(609, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(610, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(611, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(612, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(613, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(614, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(615, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(616, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(617, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(618, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(619, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(620, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(621, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(622, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(623, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(624, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(625, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(626, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(627, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(628, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(629, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(630, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(631, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(632, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(633, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(634, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(635, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(636, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(637, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(638, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(639, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(640, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(641, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(642, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(643, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(644, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(645, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(646, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(647, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(648, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(649, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(650, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(651, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(652, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(653, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(654, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(655, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(656, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(657, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(658, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(659, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(660, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(661, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(662, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(663, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(664, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(665, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(666, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 546703, 0),
(667, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(668, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(669, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(670, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(671, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(672, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(673, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(674, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(675, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(676, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(677, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(678, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(679, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(680, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(681, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(682, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(683, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(684, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(685, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(686, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(687, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(688, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(689, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(690, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(691, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(692, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(693, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(694, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(695, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(696, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(697, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(698, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(699, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(700, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(701, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(702, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(703, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(704, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(705, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(706, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(707, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(708, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(709, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(710, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(711, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(712, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(713, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(714, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(715, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(716, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(717, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(718, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(719, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(720, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(721, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(722, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(723, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(724, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(725, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(726, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(727, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(728, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(729, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(730, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(731, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(732, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(733, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(734, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(735, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(736, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(737, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(738, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(739, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(740, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(741, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(742, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(743, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(744, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(745, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(746, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(747, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(748, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(749, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(750, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(751, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(752, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(753, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(754, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(755, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(756, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(757, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(758, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(759, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(760, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(761, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(762, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(763, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(764, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(765, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(766, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(767, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(768, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(769, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(770, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(771, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(772, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(773, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(774, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(775, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(776, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(777, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(778, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(779, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(780, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(781, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(782, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(783, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(784, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(785, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(786, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(787, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(788, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(789, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(790, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(791, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(792, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(793, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(794, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(795, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(796, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(797, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(798, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(799, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(800, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(801, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(802, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(803, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(804, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(805, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(806, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(807, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(808, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(809, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(810, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(811, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(812, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(813, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(814, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(815, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(816, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(817, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(818, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(819, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(820, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(821, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(822, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(823, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0);
INSERT INTO `de_sector` (`sec_id`, `name`, `url`, `bk`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `techs`, `buildgnr`, `buildgtime`, `skmes`, `e1`, `e2`, `zielsec`, `aktion`, `zeit`, `aktzeit`, `gesrzeit`, `ssteuer`, `platz`, `pass`, `platz_last_day`, `npc`, `votetimer`, `votecounter`, `col`, `ekey`, `tempcol`, `arthold`) VALUES
(824, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(825, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(826, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(827, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(828, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(829, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(830, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(831, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(832, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(833, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(834, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(835, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(836, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(837, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(838, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(839, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(840, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(841, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(842, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(843, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(844, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(845, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(846, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(847, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(848, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(849, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(850, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(851, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(852, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(853, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(854, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(855, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(856, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(857, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(858, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(859, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(860, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(861, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(862, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(863, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(864, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(865, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(866, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(867, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(868, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(869, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(870, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(871, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(872, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(873, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(874, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(875, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(876, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(877, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(878, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(879, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(880, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(881, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(882, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(883, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(884, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(885, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(886, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(887, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(888, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(889, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(890, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(891, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(892, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(893, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(894, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(895, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(896, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(897, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(898, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(899, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(900, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(901, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(902, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(903, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(904, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(905, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(906, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(907, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(908, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(909, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(910, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(911, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(912, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(913, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(914, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(915, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(916, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(917, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(918, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(919, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(920, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(921, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(922, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(923, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(924, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(925, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(926, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(927, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(928, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(929, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(930, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(931, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(932, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(933, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(934, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(935, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(936, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(937, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(938, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(939, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(940, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(941, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(942, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(943, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(944, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(945, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(946, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(947, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(948, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(949, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(950, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(951, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(952, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(953, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(954, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(955, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(956, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(957, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(958, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(959, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(960, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(961, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(962, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(963, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(964, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(965, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(966, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(967, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(968, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(969, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(970, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(971, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(972, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(973, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(974, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(975, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(976, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(977, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(978, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(979, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(980, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(981, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(982, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(983, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(984, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(985, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(986, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(987, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(988, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(989, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(990, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(991, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(992, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(993, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(994, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(995, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(996, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(997, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(998, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(999, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1000, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1001, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1002, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1003, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1004, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1005, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1006, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1007, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1008, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1009, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1010, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1011, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1012, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1013, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1014, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1015, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1016, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1017, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1018, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1019, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1020, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1021, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1022, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1023, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1024, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1025, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1026, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1027, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1028, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1029, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1030, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1031, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1032, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1033, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1034, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1035, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1036, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1037, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1038, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1039, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1040, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1041, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1042, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1043, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1044, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1045, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1046, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1047, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1048, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1049, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1050, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1051, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1052, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1053, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1054, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1055, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1056, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1057, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1058, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1059, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1060, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1061, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1062, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1063, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1064, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1065, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1066, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1067, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1068, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1069, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1070, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1071, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1072, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1073, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1074, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1075, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1076, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1077, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1078, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1079, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1080, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1081, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1082, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1083, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1084, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1085, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1086, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1087, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1088, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1089, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1090, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1091, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1092, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1093, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1094, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1095, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1096, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1097, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1098, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1099, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1100, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1101, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1102, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1103, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1104, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1105, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1106, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1107, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1108, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1109, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1110, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1111, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1112, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1113, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1114, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1115, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1116, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1117, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1118, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1119, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1120, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1121, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1122, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1123, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1124, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1125, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1126, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1127, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1128, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1129, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1130, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1131, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1132, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1133, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1134, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1135, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1136, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1137, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1138, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1139, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1140, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1141, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1142, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1143, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1144, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1145, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1146, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1147, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1148, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1149, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1150, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1151, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1152, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1153, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1154, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1155, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1156, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1157, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1158, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1159, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1160, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1161, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1162, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1163, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1164, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1165, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1166, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1167, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1168, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1169, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1170, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1171, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1172, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1173, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1174, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1175, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1176, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1177, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1178, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1179, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1180, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1181, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1182, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1183, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1184, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1185, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1186, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1187, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1188, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1189, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1190, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1191, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1192, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1193, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1194, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1195, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1196, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1197, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1198, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1199, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1200, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1201, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1202, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1203, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1204, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1205, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1206, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1207, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1208, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1209, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1210, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1211, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1212, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1213, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1214, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1215, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1216, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1217, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1218, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1219, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1220, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1221, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1222, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1223, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1224, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1225, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1226, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1227, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1228, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1229, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1230, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1231, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1232, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1233, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1234, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0);
INSERT INTO `de_sector` (`sec_id`, `name`, `url`, `bk`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `techs`, `buildgnr`, `buildgtime`, `skmes`, `e1`, `e2`, `zielsec`, `aktion`, `zeit`, `aktzeit`, `gesrzeit`, `ssteuer`, `platz`, `pass`, `platz_last_day`, `npc`, `votetimer`, `votecounter`, `col`, `ekey`, `tempcol`, `arthold`) VALUES
(1235, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1236, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1237, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1238, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1239, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1240, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1241, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1242, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1243, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1244, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1245, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1246, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1247, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1248, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1249, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1250, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1251, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1252, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1253, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1254, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1255, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1256, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1257, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1258, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1259, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1260, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1261, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1262, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1263, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1264, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1265, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1266, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1267, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1268, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1269, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1270, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1271, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1272, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1273, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1274, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1275, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1276, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1277, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1278, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1279, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1280, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1281, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1282, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1283, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1284, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1285, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1286, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1287, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1288, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1289, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1290, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1291, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1292, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1293, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1294, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1295, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1296, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1297, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1298, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1299, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1300, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1301, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1302, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1303, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1304, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1305, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1306, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1307, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1308, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1309, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1310, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1311, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1312, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1313, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1314, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1315, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1316, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1317, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1318, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1319, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1320, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1321, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1322, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1323, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1324, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1325, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1326, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1327, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1328, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1329, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1330, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1331, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1332, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1333, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1334, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1335, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1336, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1337, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1338, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1339, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1340, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1341, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1342, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1343, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1344, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1345, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1346, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1347, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1348, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1349, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1350, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1351, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1352, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1353, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1354, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1355, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1356, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1357, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1358, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1359, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1360, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1361, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1362, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1363, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1364, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1365, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1366, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1367, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1368, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1369, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1370, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1371, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1372, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1373, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1374, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1375, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1376, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1377, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1378, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1379, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1380, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1381, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1382, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1383, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1384, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1385, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1386, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1387, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1388, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1389, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1390, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1391, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1392, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1393, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1394, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1395, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1396, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1397, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1398, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1399, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1400, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1401, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1402, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1403, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1404, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1405, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1406, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1407, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1408, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1409, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1410, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1411, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1412, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1413, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1414, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1415, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1416, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1417, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1418, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1419, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1420, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1421, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1422, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1423, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1424, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1425, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1426, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1427, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1428, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1429, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1430, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1431, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1432, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1433, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1434, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1435, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1436, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1437, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1438, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1439, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1440, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1441, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1442, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1443, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1444, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1445, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1446, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1447, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1448, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1449, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1450, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1451, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1452, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1453, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1454, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1455, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1456, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1457, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1458, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1459, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1460, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1461, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1462, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1463, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1464, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1465, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1466, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1467, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1468, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1469, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1470, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1471, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1472, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1473, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1474, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1475, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1476, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1477, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1478, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1479, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1480, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1481, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1482, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1483, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1484, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1485, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1486, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1487, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1488, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1489, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1490, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1491, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1492, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1493, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1494, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1495, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1496, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1497, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1498, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1499, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1500, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1501, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1502, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1503, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1504, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1505, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1506, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1507, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1508, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1509, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1510, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1511, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1512, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1513, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1514, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1515, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1516, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1517, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1518, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1519, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1520, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1521, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1522, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1523, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1524, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1525, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1526, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1527, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1528, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1529, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1530, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1531, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1532, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1533, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1534, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1535, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1536, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1537, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1538, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1539, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1540, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1541, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1542, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1543, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1544, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1545, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1546, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1547, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1548, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1549, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1550, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1551, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1552, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1553, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1554, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1555, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1556, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1557, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1558, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1559, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1560, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1561, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1562, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1563, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1564, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1565, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1566, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1567, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1568, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1569, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1570, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1571, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1572, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1573, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1574, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1575, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1576, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1577, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1578, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1579, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1580, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1581, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1582, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1583, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1584, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1585, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1586, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1587, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1588, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1589, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1590, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1591, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1592, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1593, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1594, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1595, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1596, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1597, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1598, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1599, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1600, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1601, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1602, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1603, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1604, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1605, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1606, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1607, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1608, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1609, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1610, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1611, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1612, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1613, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1614, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1615, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1616, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1617, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1618, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1619, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1620, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1621, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1622, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1623, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1624, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1625, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1626, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1627, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1628, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1629, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1630, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1631, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1632, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1633, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1634, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1635, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1636, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1637, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1638, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1639, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1640, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1641, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1642, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1643, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1644, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0);
INSERT INTO `de_sector` (`sec_id`, `name`, `url`, `bk`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `techs`, `buildgnr`, `buildgtime`, `skmes`, `e1`, `e2`, `zielsec`, `aktion`, `zeit`, `aktzeit`, `gesrzeit`, `ssteuer`, `platz`, `pass`, `platz_last_day`, `npc`, `votetimer`, `votecounter`, `col`, `ekey`, `tempcol`, `arthold`) VALUES
(1645, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1646, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1647, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1648, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1649, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1650, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1651, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1652, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1653, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1654, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1655, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1656, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1657, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1658, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1659, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1660, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1661, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1662, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1663, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1664, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1665, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1666, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1667, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1668, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1669, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1670, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1671, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1672, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1673, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1674, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1675, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1676, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1677, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1678, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1679, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1680, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1681, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1682, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1683, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1684, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1685, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1686, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1687, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1688, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1689, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1690, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1691, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1692, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1693, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1694, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1695, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1696, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1697, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1698, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1699, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1700, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1701, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1702, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1703, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1704, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1705, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1706, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1707, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1708, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1709, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1710, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1711, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1712, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1713, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1714, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1715, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1716, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1717, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1718, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1719, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1720, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1721, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1722, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1723, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1724, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1725, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1726, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1727, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1728, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1729, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1730, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1731, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1732, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1733, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1734, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1735, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1736, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1737, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1738, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1739, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1740, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1741, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1742, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1743, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1744, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1745, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1746, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1747, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1748, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1749, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1750, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1751, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1752, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1753, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1754, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1755, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1756, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1757, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1758, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1759, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1760, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1761, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1762, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1763, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1764, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1765, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1766, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1767, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1768, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1769, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1770, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1771, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1772, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1773, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1774, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1775, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1776, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1777, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1778, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1779, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1780, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1781, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1782, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1783, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1784, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1785, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1786, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1787, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1788, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1789, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1790, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1791, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1792, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1793, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1794, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1795, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1796, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1797, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1798, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1799, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1800, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1801, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1802, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1803, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1804, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1805, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1806, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1807, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1808, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1809, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1810, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1811, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1812, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1813, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1814, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1815, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1816, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1817, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1818, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1819, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1820, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1821, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1822, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1823, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1824, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1825, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1826, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1827, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1828, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1829, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1830, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1831, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1832, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1833, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1834, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1835, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1836, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1837, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1838, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1839, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1840, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1841, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1842, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1843, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1844, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1845, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1846, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1847, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1848, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1849, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1850, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1851, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1852, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1853, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1854, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1855, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1856, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1857, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1858, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1859, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1860, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1861, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1862, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1863, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1864, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1865, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1866, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1867, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1868, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1869, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1870, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1871, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1872, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1873, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1874, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1875, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1876, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1877, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1878, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1879, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1880, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1881, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1882, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1883, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1884, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1885, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1886, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1887, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1888, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1889, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1890, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1891, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1892, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1893, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1894, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1895, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1896, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1897, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1898, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1899, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1900, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1901, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1902, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1903, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1904, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1905, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1906, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1907, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1908, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1909, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1910, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1911, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1912, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1913, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1914, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1915, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1916, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1917, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1918, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1919, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1920, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1921, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1922, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1923, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1924, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1925, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1926, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1927, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1928, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1929, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1930, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1931, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1932, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1933, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1934, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1935, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1936, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1937, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1938, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1939, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1940, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1941, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1942, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1943, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1944, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1945, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1946, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1947, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1948, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1949, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1950, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1951, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1952, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1953, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1954, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1955, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1956, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1957, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1958, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1959, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1960, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1961, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1962, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1963, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1964, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1965, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1966, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1967, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1968, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1969, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1970, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1971, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1972, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1973, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1974, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1975, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1976, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1977, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1978, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1979, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1980, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1981, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1982, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1983, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1984, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1985, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1986, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1987, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1988, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1989, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1990, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1991, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1992, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1993, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1994, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1995, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1996, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1997, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1998, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(1999, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0),
(2000, '', '', 0, 0, 0, 0, 0, 0, 's000000000', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 5, 0, '', 0, 0, 0, 0, 0, '100;0;0;0', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sectorforum_posts`
--

CREATE TABLE `de_sectorforum_posts` (
  `postid` int(11) UNSIGNED NOT NULL,
  `poster` varchar(20) NOT NULL DEFAULT '0',
  `post` text NOT NULL,
  `time` int(13) NOT NULL DEFAULT '0',
  `thread` int(11) NOT NULL DEFAULT '0',
  `title` varchar(25) NOT NULL DEFAULT '',
  `edit` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sectorforum_threads`
--

CREATE TABLE `de_sectorforum_threads` (
  `id` int(11) UNSIGNED NOT NULL,
  `threadname` varchar(50) NOT NULL DEFAULT '',
  `creator` varchar(20) NOT NULL DEFAULT '0',
  `sector` int(11) NOT NULL DEFAULT '0',
  `lastposter` varchar(20) NOT NULL DEFAULT '0',
  `lastactive` int(13) NOT NULL DEFAULT '0',
  `open` int(1) NOT NULL DEFAULT '1',
  `anzposts` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `gelesen` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `wichtig` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sector_build`
--

CREATE TABLE `de_sector_build` (
  `sector_id` int(11) NOT NULL DEFAULT '0',
  `tech_id` mediumint(9) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0',
  `verbzeit` mediumint(9) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sector_stat`
--

CREATE TABLE `de_sector_stat` (
  `sec_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `datum` varchar(10) NOT NULL DEFAULT '',
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `col` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `platz` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sector_umzug`
--

CREATE TABLE `de_sector_umzug` (
  `user_id` mediumint(11) NOT NULL DEFAULT '0',
  `typ` tinyint(4) NOT NULL DEFAULT '0',
  `sector` mediumint(9) NOT NULL DEFAULT '0',
  `system` mediumint(9) NOT NULL DEFAULT '0',
  `pass` varchar(10) NOT NULL DEFAULT '',
  `ticks` mediumint(9) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_sector_voteout`
--

CREATE TABLE `de_sector_voteout` (
  `sector_id` int(11) NOT NULL DEFAULT '0',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `votes` text NOT NULL,
  `ticks` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_server_round_toplist`
--

CREATE TABLE `de_server_round_toplist` (
  `round_id` mediumint(8) UNSIGNED NOT NULL,
  `player_owner_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `player_spielername` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
  `player_sector` mediumint(9) NOT NULL,
  `player_system` smallint(5) UNSIGNED NOT NULL,
  `player_col` int(10) UNSIGNED NOT NULL,
  `player_score` bigint(20) UNSIGNED NOT NULL,
  `player_rasse` smallint(6) NOT NULL,
  `round_wt` int(11) NOT NULL,
  `sector_id` mediumint(8) UNSIGNED NOT NULL,
  `sector_name` varchar(30) CHARACTER SET utf8mb4 NOT NULL,
  `sector_score` bigint(20) UNSIGNED NOT NULL,
  `ally_id` smallint(5) UNSIGNED NOT NULL,
  `ally_tag` varchar(7) CHARACTER SET utf8mb4 NOT NULL,
  `ally_roundpoints` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_server_stat`
--

CREATE TABLE `de_server_stat` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `datum` date NOT NULL,
  `active_player` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_eh_score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_eh_score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_col` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_col` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_agent` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_agent` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_agent_lost` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_agent_lost` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_col_build` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_col_build` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `gesamt_kartefakt` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `max_kartefakt` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_system`
--

CREATE TABLE `de_system` (
  `lasttick` varchar(14) DEFAULT NULL,
  `lastmtick` varchar(14) NOT NULL DEFAULT '',
  `doetick` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `domtick` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `dortick` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `dodelinactiv` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `dodeloldtrade` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `wt` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `kt` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `winid` mediumint(9) NOT NULL DEFAULT '0',
  `winticks` mediumint(9) NOT NULL DEFAULT '0',
  `trade_active` char(1) NOT NULL DEFAULT '1',
  `nachtcron` tinyint(4) NOT NULL DEFAULT '0',
  `efta1` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `efta2` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `efta3` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `siegel1` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `s1res1` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `s1res2` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `s1res3` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `s1res4` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `s1res5` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `s1history` text NOT NULL,
  `a1userid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `a1npc` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `a1tick` mediumint(9) NOT NULL DEFAULT '0',
  `a2userid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `a3userid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `a4userid` mediumint(8) UNSIGNED NOT NULL,
  `a5userid` mediumint(8) UNSIGNED NOT NULL,
  `a6userid` mediumint(8) UNSIGNED NOT NULL,
  `a7userid` mediumint(8) UNSIGNED NOT NULL,
  `a8userid` mediumint(8) UNSIGNED NOT NULL,
  `a9userid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `a10userid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `a11userid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat1` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat2` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat3` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat4` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat5` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat6` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat7` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat100` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat101` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat102` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat103` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat104` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat105` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat106` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat107` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat108` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat109` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat110` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat111` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat112` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat113` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `smstat114` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `creditefta` mediumint(9) UNSIGNED DEFAULT '0',
  `creditea` mediumint(9) UNSIGNED DEFAULT '0',
  `roundpointsflag` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `npcleader` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `rundenstart_datum` date NOT NULL,
  `reshuffle` tinyint(4) NOT NULL DEFAULT '0',
  `create_map_objects` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_system`
--

INSERT INTO `de_system` (`lasttick`, `lastmtick`, `doetick`, `domtick`, `dortick`, `dodelinactiv`, `dodeloldtrade`, `wt`, `kt`, `winid`, `winticks`, `trade_active`, `nachtcron`, `efta1`, `efta2`, `efta3`, `siegel1`, `s1res1`, `s1res2`, `s1res3`, `s1res4`, `s1res5`, `s1history`, `a1userid`, `a1npc`, `a1tick`, `a2userid`, `a3userid`, `a4userid`, `a5userid`, `a6userid`, `a7userid`, `a8userid`, `a9userid`, `a10userid`, `a11userid`, `smstat1`, `smstat2`, `smstat3`, `smstat4`, `smstat5`, `smstat6`, `smstat7`, `smstat100`, `smstat101`, `smstat102`, `smstat103`, `smstat104`, `smstat105`, `smstat106`, `smstat107`, `smstat108`, `smstat109`, `smstat110`, `smstat111`, `smstat112`, `smstat113`, `smstat114`, `creditefta`, `creditea`, `roundpointsflag`, `npcleader`, `rundenstart_datum`, `reshuffle`, `create_map_objects`) VALUES
('20210227121121', '20210130102714', 1, 1, 1, 0, 1, 16012, 14927, 98818, 0, '1', 12, 16, 1, 39, 0, 0, 0, 0, 0, 0, '', 103, 1, 0, 191, 174, 137, 137, 192, 173, 92, 184, 132, 13, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 38344, '2016-06-15', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tauction`
--

CREATE TABLE `de_tauction` (
  `id` int(11) NOT NULL,
  `seller` int(11) DEFAULT NULL,
  `maxbid` int(11) DEFAULT NULL,
  `bids` int(11) DEFAULT NULL,
  `ticks` int(11) DEFAULT NULL,
  `bidder` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `sellername` varchar(30) NOT NULL DEFAULT '',
  `biddername` varchar(30) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tech_data`
--

CREATE TABLE `de_tech_data` (
  `tech_id` int(11) NOT NULL,
  `tech_name` text NOT NULL,
  `tech_build_cost` text NOT NULL,
  `tech_vor` text NOT NULL,
  `tech_desc` text NOT NULL,
  `tech_typ` tinyint(4) NOT NULL,
  `tech_level` int(11) NOT NULL,
  `tech_build_time` int(11) NOT NULL,
  `tech_sort_id` mediumint(9) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `de_tech_data`
--

INSERT INTO `de_tech_data` (`tech_id`, `tech_name`, `tech_build_cost`, `tech_vor`, `tech_desc`, `tech_typ`, `tech_level`, `tech_build_time`, `tech_sort_id`) VALUES
(1, 'Konstruktionszentrum I;Werkstatt I;Zentralbau I;Stock I;Replikator I', 'R1x4000;R2x1000', '', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 1, 90, 10),
(2, 'Konstruktionszentrum II;Werkstatt II;Zentralbau II;Stock II;Replikator II', 'R1x8000;R2x2000', 'T1', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 2, 180, 10),
(3, 'Planetare Börse;Planetarer Markt;Platz des Sektortausches;Planetare Handelswabe;Transfermatrix', 'R1x8000;R2x2000', 'T2;T65', 'Die Planetare Börse ermöglicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine Währung im Universum unnötig ist. Dieses Gebäude kann erweitert werden.;Der Planetare Markt ermöglicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine Währung im Universum unnötig ist. Dieses Gebäude kann erweitert werden.;Auch wenn die K’tharr im Grunde alle anderen Spezies hassen und ihr einziger Instinkt die Vernichtung und Zerstörung ist, haben sie doch gelernt, dass man Ressourcen auch durch Handel erhalten kann. Mit diesem Platz erhält eine Kolonie die Möglichkeit, mit anderen Mitgliedern ihres Sektors Tauschgeschäfte durchzuführen. \r\n;Die Planetare Handelswabe ermöglicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine Währung im Universum unnötig ist. Dieses Gebäude kann erweitert werden.;platzhalter', 0, 2, 8800, 40),
(4, 'Weltraumhandelsgilde;Galaktische Handelszunft;Platz des Raumtausches;Handelswabe des Universums;Unimatrix', 'R1x40000;R2x10000', 'T3;T113', 'Die Weltraumhandelsgilde ist die Erweiterung der Planetaren Börse und ermöglicht den Handel auch über die Sektorengrenzen hinaus.;Die Galaktische Handelszunft ist die Erweiterung des Planetaren Marktes und ermöglicht den Handel auch über die Sektorengrenzen\r\n\r\nhinaus.;Mit dem Platz des Raumtausches erhält eine Kolonie nicht nur die Möglichkeit, mit dem gesamten Universum zu handeln. Sie bietet damit auch anderen Völkern die Möglichkeit, die Kolonie förmlich mit Agenten zu überschwemmen.\r\n;Die Handelswabe des Universums ist die Erweiterung der Planetaren Handelswabe und ermöglicht den Handel auch über die Sektorengrenzen\r\n\r\nhinaus.;platzhalter', 0, 4, 21600, 40),
(5, 'Sprungfeldbegrenzer;Sprungbegrenzungswall;Bau des Blockens;Wabe des Haltens;EMP-Dominator', 'R1x200000;R2x200000;R3x80000;R4x40000;R5x75', 'T63;T117', 'Der Sprungfeldbegrenzer ermöglicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine größere Möglichkeit besteht noch Verstärkung von Verbündeten zu erhalten.;Der Sprungbegrenzungswall ermöglicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine größere Möglichkeit besteht noch Verstärkung von Verbündeten zu erhalten.;Der Bau des Blockens legt ein gewaltiges Kraftfeld um den Planeten. Diese arbeitet mit einer Technologie, die den Teleportorganen der Netzfänger sehr ähnlich ist, jedoch in die entgegenbesetze Richtung wirkt. Sie bremst gegnerische Flotten so aus, dass diese einen vollen Kampftick später eintreffen, lässt verbündete Flotten jedoch mit normaler Geschwindigkeit passieren.\r\n;Die Wabe des Haltens ermöglicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine größere Möglichkeit besteht noch Verstärkung von Verbündeten zu erhalten.;platzhalter', 0, 8, 64800, 30),
(6, 'Recyclotron;Schrottschmelze;Bau der Verwertung;Extraktorwabe;Rekonverter', 'R1x120000;R2x80000;R3x20000;R4x5000', 'T117;T65', 'Ist das Recyclotron gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Transmitterfeld eingefangen. Da die gegnerischen Schiffe weiter außerhalb dieses Transmitterfeldes liegen, können nur die Wracks der verteidigenden Flotten und der eigenen Türme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEin Recyclotron gewinnt 10% der Ressourcen der zerstörten Schiffe zurück! \r\n;Ist die Schrottschmelze gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Dimensionsfeld eingefangen. Da die gegnerischen Schiffe weiter außerhalb dieses Transmitterfeldes liegen, können nur die Wracks der verteidigenden Flotten und der eigenen Türme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEine Schrottschmelze gewinnt 10% der Ressourcen der zerstörten Schiffe zurück!\r\n\r\n\r\n;Der Bau der Verwertung beherbergt kleine Bioschiffe, die den Weltraum nach Überresten von zerstörten K’tharr Schiffen absuchen. Sie fressen deren Überreste, um selber im Bau der Verwertung verdaut zu werden. Leider verschmähen diese parasitären Schiffe jegliches feindliche Schiff und es war den Forschern der K’tharr bislang nicht möglich eine Ursache hierfür zu finden. Es ist dem Bau der Verwertung möglich, ca. 10 % der Ressourcen aller zerstörten verbündeten Schiffe und eigenen Türme zu recyceln.\r\n;Ist die Extraktorwabe gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Transwabenfeld eingefangen. Da die gegnerischen Schiffe weiter außerhalb dieses Transwabenfeldes liegen, können nur die Wracks der verteidigenden Flotten und der eigenen Türme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEin Recyclotron gewinnt 10% der Ressourcen der zerstörten Schiffe zurück!\r\n\r\n\r\n;platzhalter', 0, 8, 86000, 30),
(7, 'Kollektorenfabrik;Sonnenschildfabrik;Zentrum der Wandler;Arbeiterwabe;AssimFab-DX', 'R1x6000;R2x1000', 'T1;T80', 'Die Kollektorenfabrik stellt die Sonnenkollektoren zur Energiegewinnung her. Sind Kollektoren fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebraucht und auf die Sonne ausgerichtet.;Die Sonnenschildfabrik stellt die Sonnenschilder zur Energiegewinnung her. Sind die Sonnenschilder fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebracht und auf die Sonne ausgerichtet.;Kollektoren sind das Herzstück einer jeden Wirtschaft. Das Zentrum der Wandler hat sich deshalb auf deren Produktion spezialisiert.;Die Arbeiterwabe stellt die Sammlerwaben zur Energiegewinnung her. Sind Sammlerwaben fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebraucht und auf die Sonne\r\n\r\nausgerichtet.;platzhalter', 0, 1, 2000, 30),
(8, 'Forschungszentrum I;Alchemielabor I;Kammer der Evolution I;Netzwerk des Denkens I;Kompilator I', 'R1x6000;R2x2000', 'T1', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Alchemielabor dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Netzwerk des Denkens dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;platzhalter', 0, 1, 100, 20),
(9, 'Geheimdienst;Spionageabteilung;Zentrum der Unterwanderung;Aufklärerwabe;Inputanalysator', 'R1x16000;R2x4000', 'T2', 'Der Geheimdienst bietet die Möglichkeit in geheimen Werkstätten Spionagesonden zu bauen um mehr über den Gegner zu erfahren.;Die Spionageabteilung bietet die Möglichkeit in geheimen Werkstätten Spionagedrohnen zu bauen um mehr über den Gegner zu erfahren.;Obwohl die Spionage nicht ihre Stärke ist, so haben die K’tharr doch gelernt, dass Agenten trotzdem notwendig sind, um sich vor feindlichen Spionageangriffen zu schützen. Ein Zentrum der Unterwanderung ermöglicht die Ausbildung eigener Agenten, sowie den Bau von Spionagesonden.\r\n;Die Aufklärerwabe bietet die Möglichkeit in geheimen Werkstätten Tunnellarven zu bauen um mehr über den Gegner zu erfahren.;platzhalter', 0, 2, 16200, 70),
(10, 'Weltraumscanner;Weltraumsonar;Kammer des Raumblickes;Augen der Arbeiterin;RWS-Nexus-DX0A', 'R1x4000;R2x1000', 'T1', 'Der Weltraumscanner ermöglicht es ankommende Flotten frühzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit beträgt die Chance eine Flotte zu erkennen nur 33%! Es können aber auch Spionagesonden erkannt werden, hier liegt die Wahrscheinlichkeit bei 15%. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Das Weltraumsonar ermöglicht es ankommende Flotten frühzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit beträgt die Chance eine Flotte zu erkennen nur 33%! Es können aber auch Spionagesonden erkannt werden, hier liegt die Wahrscheinlichkeit bei 15%. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Jeder K’tharr-Bau sollte diese Kammer besitzen, da sie die Erforschung des Weltalls ermöglicht. Leider ist der einfache Raumblick sehr ungenau, weshalb angreifende Flotten nur mit einer Wahrscheinlichkeit von 33% entdeckt werden. Auch die Entdeckung von Sonden ist möglich, jedoch nur in 15% aller Fälle. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Die Augen der Arbeiterin ermöglichen es ankommende Flotten frühzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit beträgt die Chance eine Flotte zu erkennen nur 33%! Es können aber auch Spionagesonden erkannt werden, hier liegt die Wahrscheinlichkeit bei 15%. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;', 0, 2, 10800, 50),
(11, 'Tachyonscanner;Elektronensonar;Erweiterung des Raumblickes;Augen der Drohne;RWS-Nexus-DX0B', 'R1x20000;R2x5000;R3x1000', 'T10;T114', 'Diese neue Scannertechnik benutzt einen weitreichenden Tachyonenstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance beträgt hier 66% und für Spionagesonden 30 %. Dieses Gebäude kann erweitert werden! Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Diese neue Scannertechnik benutzt einen weitreichenden Elektronenrichtstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance beträgt hier 66% und für Spionagedrohnen 30 %. Dieses Gebäude kann erweitert werden! Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Der Raumblick an sich ist sehr ungenau. Durch eine Verbesserung seiner optischen Organe kann er effizienter gemacht werden, so dass er nun in 66% aller Fälle eine anfliegende Flotte entdeckt. Sonden werden nun in 30 % aller Fälle entdeckt. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Diese neue Scannertechnik benutzt einen weitreichenden Ultrawellenstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance beträgt hier 66% und für Tunnellarven 30 %. Dieses Gebäude kann erweitert werden! Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;platzhalter', 0, 5, 10000, 30),
(12, 'Neutronenscanner;Photonensonar;Kammer des Tiefraumblickes;Augen der Koenigin;RWS-Nexus-DX0C', 'R1x100000;R2x40000;R3x4000;R4x1000', 'T11;T117', 'Diese revolutionäre Scannertechnik ermöglicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Für Spionagesonden gilt eine Wahrscheinlichkeit von 45 %. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Diese revolutionäre Sonartechnik ermöglicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Für Spionagedrohnen gilt eine Wahrscheinlichkeit von 45 %. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Die besten Biologen der K’Tharr haben optische Organe gezüchtet, die in der Lage sind, jede sich nähernde Flotte zu entdecken. Leider haben sie nach wie vor Probleme, die Schattenfelder von Sonden zu entdecken, so dass diese zwar mit einer Wahrscheinlichkeit von 45% entdeckt werden können, jedoch immer noch die Möglichkeit besteht, sie nicht zu entdecken. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;Diese revolutionäre Scannertechnik ermöglicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Leider haben sie nach wie vor Probleme Sonden zu entdecken, so dass diese zwar mit einer Wahrscheinlichkeit von 45% entdeckt werden können, jedoch immer noch die Möglichkeit besteht, sie nicht zu entdecken. Die Entdeckungswahrscheinlichkeit gilt zum Zeitpunkt des Flottenstarts, Flotten die bereits unterwegs sind, sind davon nicht betroffen.;platzhalter', 0, 8, 43200, 40),
(13, 'Raumwerft II;Raumschmiede II;Schwarmstock II;Drohnenwabe II;Dualassambler II', 'R1x20000;R2x4000', 'T113;T129', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;platzhalter', 0, 4, 1200, 30),
(14, 'Materieumwandler M;Raffinerie M;Wandlerkammer M;Arbeiterlager M;Manipulator M', 'R1x10000;R2x1000', 'T1', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie eine Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Multiplex. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Multiplex nur in einem Verhältnis von 2:1 produziert wird.\r\n;Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;platzhalter', 0, 1, 2700, 25),
(15, 'Materieumwandler D;Raffinerie D;Wandlerkammer D;Arbeiterlager D;Manipulator D', 'R1x12500;R2x2000', 'T2;T14', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Dhyarra. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Dhyarra nur in einem Verhältnis von 4:1 produziert wird.;Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;platzhalter', 0, 2, 5400, 25),
(16, 'Materieumwandler I;Raffinerie I;Wandlerkammer I;Arbeiterlager I;Manipulator I', 'R1x15000;R2x3000', 'T15;T112', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Iradium. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Iradium nur in einem Verhältnis von 6:1 produziert wird.;Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;platzhalter', 0, 3, 8100, 25),
(17, 'Materieumwandler E;Raffinerie E;Wandlerkammer E;Arbeiterlager E;Manipulator E', 'R1x17500;R2x4000', 'T16;T113', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!;Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Ethernium. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Eternium nur in einem Verhältnis von 8:1 produziert wird.;Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!;platzhalter', 0, 4, 10800, 25),
(18, 'Hochleistungsumwandler M;Große Raffinerie M;Große Wandlerkammer M;Arbeitergrosslager M;Extrem-Manipulator M', 'R1x100000;R2x20000;R3x5000;R4x2000', 'T14;T114', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.;Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;Der Ausbau der Wandlerkammer M erhöht die Umwandlungseffizienz auf ein Verhältnis von 1:1.\r\n;Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;platzhalter', 0, 5, 27000, 25),
(19, 'Hochleistungsumwandler D;Große Raffinerie D;Große Wandlerkammer D;Arbeitergrosslager D;Extrem-Manipulator D', 'R1x125000;R2x30000;R3x8000;R4x3000', 'T15;T115', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.;Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;Der Ausbau der Wandlerkammer D erhöht die Umwandlungseffizienz auf ein Verhältnis von 2:1.\r\n\r\n;Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;platzhalter', 0, 6, 33300, 25),
(20, 'Hochleistungsumwandler I;Große Raffinerie I;Große Wandlerkammer I;Arbeitergrosslager I;Extrem-Manipulator I', 'R1x150000;R2x40000;R3x11000;R4x4000', 'T16;T116', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.;Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;Der Ausbau der Wandlerkammer I erhöht die Umwandlungseffizienz auf ein Verhältnis von 3:1.\r\n;Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;platzhalter', 0, 7, 39600, 25),
(21, 'Hochleistungsumwandler E;Große Raffinerie E;Große Wandlerkammer E;Arbeitergrosslager E;Extrem-Manipulator E', 'R1x175000;R2x50000;R3x14000;R4x5000', 'T17;T117', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.;Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;Der Ausbau der Wandlerkammer E erhöht die Umwandlungseffizienz auf ein Verhältnis von 4:1.\r\n;Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.;platzhalter', 0, 8, 45000, 25),
(22, 'Verteidigungszentrum;Belagerungszentrum;Bau des Schutzes;Wabe der Hilfe;Kontrastyx', 'R1x16000;R2x4000', 'T112', 'Das Verteidigungszentrum ist für den Bau und die Verwaltung der planetaren Verteidigungsanlagen zuständig.;Das Belagerungszentrum ist für den Bau und die Verwaltung der planetaren Verteidigungsanlagen zuständig.;Der Schutz einer jeden Kolonie hat absoluten Vorrang, doch leider können nicht immer Schiffe bereitstehen. Verteidigungswaffen können diesen Nachteil ausgleichen. Um diese errichten und auch kontrollieren zu können, ist der Bau des Schutzes unumgänglich.\r\n;Die Wabe der Hilfe ist für den Bau und die Verwaltung der planetaren Verteidigungsanlagen zuständig.;platzhalter', 0, 3, 16200, 40),
(23, 'Kollektorempfänger;Sonnenschildakzeptor;Zellenempfänger;Sammlerwabe;Assimkollektor', 'R1x20000;R2x20000', 'T4;T65;T113', 'Der Kollektorempfänger ist ein gigantischer orbitaler Transmitter, der in der Lage ist die Kollektoren zu empfangen, die von den Transmitterschiffen in Kämpfen erbeutet und abgestrahlt werden.;Der Sonnenschildakzeptor ist ein gigantisches orbitales Dimensionstor, der in der Lage ist die Sonnenschilder zu empfangen, die von den Transmitterschiffen in Kämpfen erbeutet und abgestrahlt\r\n\r\nwerden.;Wenn sich Netzfänger selbst zerstören, um Kollektoren zum Heimatplaneten zu schicken, bildet der Zellenempfänger das Gegenportal, um die Kollektoren wieder zu rematerialisieren. Ohne Zellenempfänger sind Netzfänger wirkungslos.;Die Sammlerwabe ist ein gigantischer orbitaler Transmitter, der in der Lage ist die Arbeiter zu empfangen, die von den Sammlern in Kämpfen erbeutet und abgestrahlt werden.;platzhalter', 0, 4, 21600, 50),
(24, 'Planetarer Schild;Planetarer Schildwall;Planetarer Schattenpanzer;Planetarer Panzer;Lurcrefelktor', 'R1x150000;R2x200000;R3x100000;R4x80000;R5x20', 'T44;T119', 'Die planetaren Verteidigungsanlagen führten den Flotten der Angreifer meist unglaubliche Schäden zu, waren jedoch Schiffe in der Lage durch die Ränge der verteidigenden Orbitalflotten und der Geschwader der Atmosphärenjäger zu brechen, konnten sie die Planetenoberfläche mit großer Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen.\r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Gebäude der Planetenoberfläche viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Ewigen bereit wären eine enorme Menge von Ressourcen und Zeit für eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass ein Planetarer Schild die besten Erfolgsquoten gegen planetengerichtete Projektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterstützt wurde.<br><br>\r\n\r\nDer Planetare Schild verfügt über eine Kombination von bodengestützten leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschießen, sowie über eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schwächen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengestützten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden können. <br>;Die planetaren Verteidigungsanlagen führten den Flotten der Angreifer meist unglaubliche Schäden zu, waren jedoch Schiffe in der Lage durch die Ränge der verteidigenden Orbitalflotten und der Geschwader der Atmosphärenjäger zu brechen, konnten sie die Planetenoberfläche mit großer Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen. \r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Gebäude der Planetenoberfläche viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Ishtar bereit wären eine enorme Menge von Ressourcen und Zeit für eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass ein Planetarer Schildwall die besten Erfolgsquoten gegen planetengerichtete\r\n\r\nProjektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterstützt wurde.<br><br>\r\n\r\nDer Planetare Schildwall verfügt über eine Kombination von bodengestützten leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschießen, sowie über eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schwächen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengestützten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden können. <br>;Der planetare Schattenpanzer bildet den Versuch, die schwachen K’tharr Verteidigungsanlagen einer Kolonie zu verstärken. Hierbei handelt es sich weniger um ein Schutzschild, als vielmehr um ein komplexes Netzwerk aus inaktiven Drüsen, die den vorhandenen Drüsen zum Verwechseln ähnlich sehen und aus allen möglichen Täuschkörpern und Gegenmaßnahmen, die einen Teil des gegnerischen Feuers auf sich ziehen. Im Endeffekt erhöht der planetare Schild damit die Anzahl der eigenen Drüsen um 10%, bietet jedoch keinerlei offensive Möglichkeiten.\r\n;Die planetaren Verteidigungsanlagen führten den Flotten der Angreifer meist unglaubliche Schäden zu, waren jedoch Schiffe in der Lage durch die Ränge der verteidigenden Orbitalflotten und der Geschwader der Atmosphärenjäger zu brechen, konnten sie die Planetenoberfläche mit großer Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen. \r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Gebäude der Planetenoberfläche viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Z’tha-ara bereit wären eine enorme Menge von Ressourcen und Zeit für eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass eine Planetarer Panzer die besten Erfolgsquoten gegen planetengerichtete\r\n\r\nProjektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterstützt wurde.<br><br>\r\n\r\nDer Planetare Panzer verfügt über eine Kombination von bodengestützen leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschießen, sowie über eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schwächen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengestützten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden können. <br>;platzhalter', 0, 10, 86400, 40),
(25, 'Hyperraumpfad-Stabilisator;Hyperraumweg-Befestiger;Hypernetzpfad-Verstärker;Hyperstraßen-Befestiger;Por-Zi-Pfadox', 'R1x8000;R2x3600', 'T10;T113', 'Diese Technologie ermöglicht die Erforschung von Hyperraumverbindungen, die nicht von der STRUKTUR unterstützt werden. Sie ermöglicht die Stabilisierung der Hyperraumverbindung und somit einen Zugriff vom eigenen System aus.;Diese Technologie ermöglicht die Erforschung von Hyperraumverbindungen, die nicht von der STRUKTUR unterstützt werden. Sie ermöglicht die Stabilisierung der Hyperraumverbindung und somit einen Zugriff vom eigenen System aus.;Diese Technologie ermöglicht die Erforschung von Hyperraumverbindungen, die nicht von der STRUKTUR unterstützt werden. Sie ermöglicht die Stabilisierung der Hyperraumverbindung und somit einen Zugriff vom eigenen System aus.;Diese Technologie ermöglicht die Erforschung von Hyperraumverbindungen, die nicht von der STRUKTUR unterstützt werden. Sie ermöglicht die Stabilisierung der Hyperraumverbindung und somit einen Zugriff vom eigenen System aus.;', 3, 4, 12800, 300),
(26, 'Unendlichkeitssphäre;Unendlichkeitstor;Unendlichkeitskammer;Unendlichkeitssphäre;DX-Warp-Bi-Tri', 'R1x100000;R2x100000;R3x100000;R4x100000;R5x10', 'T4;T70', 'Unendlichkeitssphäre;Unendlichkeitstor;Unendlichkeitskammer;Unendlichkeitssphäre;platzhalter', 0, 1, 28800, 1000),
(27, 'Paleniumverstärker;Paleniumverstärker;Paleniumverstärker;Paleniumverstärker;Paleniumverstärker', 'R1x25000;R2x12000;R3x2000;R4x2000;R5x5', 'T28', 'Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.;Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.;Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.;Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.;Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen. Palenium tritt in verstärkter Konzentration in EFTA auf.', 0, 1, 28800, 1000),
(28, 'Artefaktzentrum;Artefakthort;Artefaktbau;Artefaktstock;Artefakt-JD-BED', 'R1x10000;R2x5000;R3x25000;R4x3000', 'T4;T115', 'Dieses Gebäude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Gebäude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt näher.;Dieses Gebäude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Gebäude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt näher.;Dieses Gebäude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Gebäude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt näher.;Dieses Gebäude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Gebäude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt näher.;Dieses Gebäude dient der Aufbewahrung und Veredelung von Artefakten. Die Artefakte können über den Handel, oder über EFTA bezogen werden. Mit diesem Gebäude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt näher.', 0, 6, 21600, 40),
(29, 'Missionszentrale;Missionshort;Missionsbau;Missionsstock;Mission-KT-TCK', 'R1x20000;R2x10000;R3x50000;R4x6000;R5x2', 'T4;T116', 'Dieses Gebäude dient zur Koordinierung von Missionen.;Dieses Gebäude dient zur Koordinierung von Missionen.;Dieses Gebäude dient zur Koordinierung von Missionen.;Dieses Gebäude dient zur Koordinierung von Missionen.;Dieses Gebäude dient zur Koordinierung von Missionen.', 0, 7, 25200, 50),
(30, 'Planetare Schilderweiterung;Planetare Schildwallerweiterung;Planetare Schattenpanzererweiterung;Planetare Panzererweiterung;Lurcrefelktor Exalt', 'R1x50000;R2x80000;R3x400000;R4x100000;R5x25', 'T24;T46;T119', 'Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.;Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.;Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.;Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.;Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.', 0, 10, 115200, 50),
(40, 'Schutzschild Klasse I;Energiebarriere Stufe I;Panzerungs-Evolution I;Exoskelett;DX-FW-Schild 0A', 'R1x400;R2x100', 'T8', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse I ist für Jäger.;Schützt Schiffe vor physischen und Energieangriffen. Stufe I ist für Jäger.;Die widerstandsfähigen und trotzdem leichten Hüllen der K’tharr Raumschiffe werden nicht produziert, sondern gezüchtet. Die kleinsten und leichtesten Panzer eignen sich besonders gut für Spider.;Schützt Schiffe vor physischen und Energieangriffen. Das Exoskelett ist für Jäger.;platzhalter', 1, 1, 75, 110),
(41, 'Schutzschild Klasse II;Energiebarriere Stufe II;Panzerungs-Evolution II;Chitinbeschichtung;DX-FW-Schild 0B', 'R1x8000;R2x2000', 'T40;T121', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse II ist für Jagdboote.;Schützt Schiffe vor physischen und Energieangriffen. Stufe II ist für Jagdboote.;Je stärker die Waffen der K’tharr sind, desto härter muss auch die Panzerung sein. Eine Verbesserung wurde erstmals mit Züchtung der Arctic Spider notwendig.;Schützt Schiffe vor physischen und Energieangriffen. Die Chitinbeschichtung ist für Jagdboote.;platzhalter', 1, 3, 10800, 100),
(42, 'Schutzschild Klasse III;Energiebarriere Stufe III;Panzerungs-Evolution III;Doppelchitinbeschichtung;DX-FW-Schild 0C', 'R1x16000;R2x4000;R3x1000', 'T41;T123', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse III ist für Zerstörer.;Schützt Schiffe vor physischen und Energieangriffen. Stufe III ist für Zerstörer.;Mit zunehmendem Evolutionsstand vergrößern sich auch die Anforderungen an die Panzerung. Um die ersten Werespiders züchten zu können, musste deshalb eine neue, allgemein als Klasse III bekannte Panzerung erzeugt werden.\r\n;Schützt Schiffe vor physischen und Energieangriffen. Die Doppelchitinbeschichtung ist für Zerstörer.;platzhalter', 1, 5, 21600, 100),
(43, 'Schutzschild Klasse IV;Energiebarriere Stufe IV;Panzerungs-Evolution IV;Chitinskelett;DX-FW-Schild 0D', 'R1x32000;R2x8000;R3x2000;R4x1000', 'T42;T125', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse IV ist für Kreuzer.;Schützt Schiffe vor physischen und Energieangriffen. Stufe IV ist für Kreuzer.;Das Züchten von Brutbeuteln machte es notwendig Panzerungen zu entwickeln, welche die transportierten Spiders nicht nur besonders schützt, sondern ihnen auch noch die Möglichkeit gibt, die Tarantula gefahrlos zu verlassen. \r\n;Schützt Schiffe vor physischen und Energieangriffen. Das Chitinskelett ist für Kreuzer.;platzhalter', 1, 7, 43200, 100),
(44, 'Multiphasenschilde;Ultrabarriere;Panzerungs-Evolution V;Chitinpanzer;DX-FW-Masterschild', 'R1x64000;R2x16000;R3x4000;R4x2000', 'T43;T127', 'Schützt Schiffe vor physischen und Energieangriffen. Wegen ihrer Größe benötigen die Schlachtschiffe diese besonderen Schilde.;Schützt Schiffe vor physischen und Energieangriffen. Wegen ihrer Größe benötigen die Schlachtschiffe diese besonderen Schilde.;Auch wenn die Klasse IV Panzerung schon undurchdringlich erscheint, so ist sie doch für die Giganten der K’tharr Flotte völlig unzureichend. Erst das Züchten der gewaltigen V’er Panzer brachte die gewünschten Ergebnisse.\r\n;Schützt Schiffe vor physischen und Energieangriffen. Wegen ihrer Größe benötigen die Schlachtschiffe diesen besonderen Chitinpanzer.;platzhalter', 1, 9, 86400, 100),
(45, 'Laser;Laserlanze;Lichtfaden;Stachel;X-Magma', 'R1x400;R2x100', 'T8', 'Stark gebündelte, kohärente Lichtstrahlen, die leichten Schaden verursachen.;Stark gebündelte, kohärente Lichtstrahlen, die leichten Schaden\r\n\r\nverursachen.;Lichtfäden sondern einen, dem Laser nicht unähnlichen, Impuls ab, der jedoch um einiges stärker ist.\r\n;Eine Art \"Laser\" die leichten Schaden verursachen.;platzhalter', 1, 1, 75, 110),
(46, 'Ionenimpulskanone;Impulsblaster;Lähmfaden;Giftstachel;EMP-Kanone', 'R1x8000;R2x2000', 'T45;T121', 'Mit dieser Waffe ist es dem Jagdboot möglich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zukämpfen.;Mit dieser Waffe ist es möglich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zu kämpfen.;Beim Versuch die Impulse der Lichtfäden zu verstärken, trat ein sonderbarer Effekt auf. Statt das Ziel zu zerstören, machte der Impuls das Ziel nun bewegungsunfähig, womit es zur leichten Beute für anderer Waffen wurde.;Mit dieser Waffe ist es dem Jagdboot möglich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zukämpfen.;platzhalter', 1, 3, 10800, 110),
(47, 'Autokanone;Bolzenkanone;Materiefaden;Grosser Stachel;Zermalmer', 'R1x16000;R2x4000;R3x1000', 'T114;T46;T123', 'Ist eine Automatikkanone, die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.;Ist eine Balistikkanone, die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.;Der Materiefaden setzt nicht mehr auf einen einfachen Energieimpuls. Seine Geschosse setzen sich aus mehreren Komponenten zusammen. Beim Aufprall öffnet sich die äußere Kapsel und ein Schwall hochkonzentrierter Säure zerfrisst in Sekundenbruchteilen jedes Material. Die innere Kapsel durchdringt die Panzerung und der biologische Sprengstoff, welcher sich im Inneren befindet, richtet schließlich schweren Schaden an.\r\n;Ist eine Art \"Automatikkanone\", die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.;platzhalter', 1, 5, 21600, 110),
(48, 'Plasmakanone;Plasmalanze;Plasmafaden;Säurestachel;ER-Plasmawerfer', 'R1x32000;R2x8000;R3x2000;R4x1000', 'T47;T125', 'Die Plasmakanone feuert einen Plasmaenergiestoß ab, der großen Schaden anrichtet.;Die Plasmalanze feuert einen Plasmaenergiestoß ab, der großen Schaden anrichtet.;Plasmafäden erhitzen ihre Geschosse so stark, dass nur noch waberndes Plasma übrig bleibt, welches sich mit Leichtigkeit durch jede Art von Panzerung schmilzt.\r\n;Der Säurestachel feuert einen Plasmaenergiestoß ab, der großen Schaden anrichtet.;platzhalter', 1, 7, 43200, 110),
(49, 'Partikelstrahl;Partikellanze;Partikelfaden;Feuerstachel;Sternenneuralisator', 'R1x64000;R2x16000;R3x4000;R4x2000', 'T48;T127', 'Der Partikelstrahl ist die stärkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem gebündelten Energiestrahl ab. Wegen ihrer Größe sind solche Kanonen nur bei Schlachtschiffen installiert.;Die Partikellanze ist die stärkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem gebündelten Energiestrahl ab. Wegen ihrer Größe sind solche Kanonen nur bei Schlachtschiffen installiert.;Der Partikelfaden ist der stärkste und größte Waffensymbiont, den die K’tharr entwickelt haben. Er verschießt einen stark gebündelten Energieimpuls, der kleine Partikel von Antimaterie in sich trägt und sie von der Umwelt hermetisch abschließt. Bei seinem Aufprall wird dieser Schutz zerstört und Materie und Antimaterie reagieren miteinander. Diese Reaktion ist so heftig, dass nichts einer solchen Explosion standhalten kann. Die schiere Größe der hierfür benötigten Organe lässt eine Installation jedoch nur auf Black Widows zu.\r\n;Der Feuerstachel ist die stärkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem gebündeltem Energiestrahl ab. Wegen ihrer Größe sind solche Kanonen nur bei Schlachtschiffen installiert.;platzhalter', 1, 9, 86400, 110),
(50, 'Raketenantrieb;Raketensteuerung;Sporenkanal;Speicheldrüsen;A01-A5-Lafette', 'R1x6000;R2x2000', 'T8', 'Die Forschung im Bereich des Raketenantriebs schafft die Basis für Raketen, die sich im Weltraum selbstständig bewegen.;Die Forschung im Bereich des Raketensteuerung schafft die Basis für Raketen, die sich im Weltraum selbstständig bewegen.;Der Sporenkanal erzeugt Sporen und schleudert sie auf gegnerische Einheiten.\r\n;Die Forschung im Bereich der Speicheldrüsen schafft die Basis für \"Raketen\", die sich im Weltraum selbstständig bewegen.;platzhalter', 1, 1, 8100, 120),
(51, 'Fusionsrakete;Ballistarakete;Sporengeschoss I;Nadelspeichel;ZoM-DX-Kopf', 'R1x4000;R2x1000', 'T50;T121', 'Die Fusionsrakete ist die einfachste Rakete, die es gibt. Sie wird bevorzugt in Raketentürmen verbaut und dient zur \"Luftabwehr\".;Die Ballistarakete ist die einfachste Rakete, die es gibt. Sie wird bevorzugt in Raketentürmen verbaut und dient zur \"Luftabwehr\".;Sporengeschosse ähneln Sprengkörpern in ihrer Funktionsweise. Die erste und schwächste Stufe ist mit einfachen intelligenten Raketen zu vergleichen, wie sie auch in Raketentürmen zu finden sind.\r\n;Der Nadelspeichel ist die einfachste \"Rakete\", die es gibt. Sie wird bevorzugt in Speichelbatterien verbaut und dient zur \"Luftabwehr\".;platzhalter', 1, 3, 5400, 120),
(52, 'Protonentorpedo;Mangenrakete;Sporengeschoss II;Stachelspeichel;itri-03-DX-Kopf', 'R1x8000;R2x2000', 'T51;T123', 'Ist ein Energiegeschoss, das mit Überlichtgeschwindigkeit sein Ziel trifft.;Ist ein Energiegeschoss, das mit Überlichtgeschwindigkeit sein Ziel trifft.;Eine Kreuzung von einfachen Sporengeschossen und Lichtfäden ergab einen schnellen hochenergetischen Lichtimpuls, der beim Aufprall immensen Schaden anrichtet.;Ist ein Energiegeschoss, das mit Überlichtgeschwindigkeit sein Ziel trifft.;platzhalter', 1, 5, 10800, 120),
(53, 'Raumbomben;Astrobomben;Sporengeschoss III;Giftspeichel;Ye-0B-DX-Kopf', 'R1x16000;R2x4000;R3x1000', 'T52;T125', 'Raumbomben sind eigentlich dicke, globige Raketen und werden nur wegen ihrem Aussehen als Bomben bezeichnet.;Astrobomben sind eigentlich dicke, globige Raketen und werden nur wegen ihrem Aussehen als Bomben bezeichnet.;Klasse III Sporengeschosse sind eine einfache Bündelung von Klasse I Sporen, deren Explosionskraft jedoch um ein vielfaches höher ist.\r\n;Giftspeichel sind eigentlich dicke, globige \"Raketen\", die sich auch als Bomben verwenden lassen.;platzhalter', 1, 7, 21600, 120),
(54, 'Antimaterietorpedo;Rammentorpedo;Sporengeschoss IV;Säurespeichel;Ultra-Ra-DX-Kopf', 'R1x32000;R2x8000;R3x2000;R4x1000', 'T53;T125', 'Diese Rakete besteht aus Antimateriekugeln, die beim Aufschlag detonieren.;Diese Rakete besteht aus hochenergetischen Kugeln, die beim Aufschlag detonieren.;Die stärkste und gefährlichste Art der Sporen beinhaltet einen einzelnen Antimateriepartikel. Auch wenn ihre Explosionskraft geringer als die der Partikelfäden ist, richtet sie nach wie vor schwerste Zerstörungen an.\r\n;Diese \"Rakete\" besteht aus Antimateriekugeln, die beim Aufschlag detonieren.;platzhalter', 1, 9, 43200, 120),
(55, 'Jägerchassis;Jägerstruktur;Leichtes Skelett;Larvenstadium;Xinth-Xoc-Konstrukt', 'R1x400;R2x100', 'T8', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Skelette sind das Grundgerüst jedes K’tharr Bioschiffes. Sie beherbergen Waffen- und Antriebsorgane und tragen die leichten bis schweren Panzerplatten der einzelnen K’tharr Schiffe. Die kleinen Skelette eignen sich auf Grund ihres geringen Gewichts und ihrer geringen Tragfähigkeit nur für Spiders.\r\n;Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;platzhalter', 1, 1, 75, 130),
(56, 'Jagdbootchassis;Jagdbootstruktur;Skelett;Bienenstadium;Hunm-oc-Konstrukt', 'R1x8000;R2x2000', 'T55;T121', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Um zusätzliche Biowaffen tragen zu können, sind größere und stärkere Skelette nötig. Sowohl Arctic Spiders als auch Netzfänger profitieren von diesem verbesserten Skelett.\r\n;Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;platzhalter', 1, 3, 10800, 130),
(57, 'Zerstörerchassis;Zerstörerstruktur;Schweres Skelett;Drohnenstadium;Ez-maC-Konstrukt', 'R1x16000;R2x4000;R3x1000', 'T56;T123', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Das schwere Skelett ist die Grundlage der Werespiders und trägt ihre Waffen, Antriebe und Panzerplatten.;Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;platzhalter', 1, 5, 21600, 130),
(58, 'Kreuzerchassis;Kreuzerstruktur;Panzerskelett;Wespenstadium;Zao-tuX-Konstrukt', 'R1x32000;R2x8000;R3x2000;R4x1000', 'T57;T125', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Das Panzerskelett basiert nicht auf dem schweren Skelett, auch wenn dieses der Vorgänger war. Vielmehr wurde eine völlig neue Art von Skelett erschaffen, dass nicht große und schwere Organe tragen kann, sondern ihnen auch ein gehöriges Maß an Schutz bieten kann. Jede Tarantula besitzt ein solches Skelett.\r\n;Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;platzhalter', 1, 7, 43200, 130),
(59, 'Schlachtschiffchassis;Schlachtschiffstruktur;Schweres Panzerskelett;Hummelstadium;Lor-ReX-Konstrukt', 'R1x64000;R2x16000;R3x4000;R4x2000', 'T58;T127', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;Als die K’tharr die ersten Partikelfäden züchteten, versuchten sie, diese auf ihren bis dato größten Schiffe, den Tarantulas, einzupflanzen. Doch die Organe des Waffensymbionten beanspruchten fast den gesamten Platz im Panzerskelett für sich alleine. Deshalb entstanden das schwere Panzerskelett und mit ihm die Black Widow.\r\n;Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.;platzhalter', 1, 9, 86400, 130),
(60, 'Atomantrieb;Neutronenantrieb;Coxadüse;Flügel;Positronenantrieb', 'R1x400;R2x100', 'T8', 'Kleiner kompakter Antrieb, genau richtig für Jäger.;Kleiner kompakter Antrieb, genau richtig für Jäger.;Coxadüsen sind kleine Hochleistungsantriebe. Sie sind extrem leicht zu manövrieren und bieten kurzfristig hohe Geschwindigkeiten, eignen sich jedoch kaum für Langstreckenflüge.\r\n;Kleiner kompakter Antrieb, genau richtig für Jäger.;platzhalter', 1, 1, 75, 140),
(61, 'Fusionsantrieb;Magmaantrieb;Trochanterdüse;Strukturflügel;Srid-XF-Antrieb', 'R1x8000;R2x2000', 'T60;T121', 'Der Fusionsantrieb beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.;Der Magmaantrieb beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.;Diese Düsen sind die Antriebsorgane der Arctic Spiders. Im Gegensatz zu den Coxadüsen bieten sie auch über längere Strecken angenehm kurze Reisezeiten.\r\n\r\n;Der Strukturflügel beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.;platzhalter', 1, 3, 10800, 140);
INSERT INTO `de_tech_data` (`tech_id`, `tech_name`, `tech_build_cost`, `tech_vor`, `tech_desc`, `tech_typ`, `tech_level`, `tech_build_time`, `tech_sort_id`) VALUES
(62, 'Ionenantrieb;Impulsantrieb;Femurdüse;Chitinflügel;Quorxantrieb', 'R1x16000;R2x4000;R3x1000', 'T61;T123', 'Nur dem Ionenantrieb ist es zu verdanken, daß sich der Zerstörer als wendiges Schiff erweist.;Nur dem Impulsantrieb ist es zu verdanken, daß sich der Zerstörer als wendiges Schiff erweist.;Femurdüsen verleihen den Werespiders ihre Wendigkeit. Auch ermöglichen sie es diesen, selbst über lange Strecken mit höchster Geschwindigkeit zu bewegen.\r\n;Nur dem Chitinflügel ist es zu verdanken, daß sich der Zerstörer als wendiges Schiff erweist.;platzhalter', 1, 5, 21600, 140),
(63, 'Antimaterieantrieb;Schwarzlochantrieb;Patelladüse;Raumflügel;Sol-FX-Antrieb', 'R1x32000;R2x8000;R3x2000;R4x1000', 'T62;T125', 'Der Kreuzer wird durch einen Antimaterieantrieb bewegt, jedoch ist der Antrieb deutlich schwerfälliger.;Der Kreuzer wird durch einen Schwarzlochantrieb bewegt, jedoch ist der Antrieb deutlich schwerfälliger.;Die Patelladüse der Tarantulas bietet zwar hohe Geschwindigkeiten auf langen Strecken, ist jedoch nicht in der Lage, diese über kurze Distanzen zu erreichen, weshalb die Tarantula in Gefechten äußerst schwerfällig ist.\r\n;Der Kreuzer wird durch einen Raumflügel bewegt, jedoch ist der Antrieb deutlich\r\n\r\nschwerfälliger.;platzhalter', 1, 7, 43200, 140),
(64, 'Hyperantrieb;Hyperfluxantrieb;Tibadüse;Flügelsynchronisation;Worx-0-3-Antrieb', 'R1x64000;R2x16000;R3x4000;R4x2000', 'T63;T127', 'Der Hyperantrieb hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.;Der Hyperfluxantrieb hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.;Die Tibadüse ist eine Weiterentwicklung der Patelladüse. Sie sollte der Black Widow die Geschwindigkeit einer Tarantula verleihen, doch die schiere Masse der Black Widow machte dies unmöglich.\r\n;Die Flügelsynchronisation hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.;platzhalter', 1, 9, 86400, 140),
(65, 'Transmitterfeld;Dimensionsfeld;Netzevolution;Transwabenfeld;Quad-DX-Mittler', 'R1x8000;R2x2000', 'T8', 'Durch das Transmitterfeld wird ein eroberter Kollektor zu deinem Planeten gebracht.;Durch das Dimensionsfeld wird ein eroberter Sonnenschild zu deinem Planeten gebracht.;Die Netzevolution erschuf den Netzteleporter, welcher das Einfangen und Teleportieren von Kollektoren ermöglicht. \r\n;Durch das Transwabenfeld wird ein eroberter Arbeiter zu deinem Planeten gebracht.;platzhalter', 1, 1, 800, 105),
(66, 'Tarnfeld;Dimensionsverschiebung;Schattenfeld;Schwarzfeld;DX-Cov-23-ER-Feld', 'R1x10000;R2x4000', 'T9;T120', 'Das Tarnfeld verhindert die Entdeckung der Spionagesonde und Geheimagenten. Jedenfalls meistens!;Die Dimensionsverschiebung verhindert die Entdeckung der Knechte und Spionagedrohnen. Jedenfalls meistens!;Schattenfelder krümmen das Licht um sich herum, so dass der Träger dieses Feldes unsichtbar erscheint. Vielaugen und Agenten nutzen dieses Organ, um unerkannt zu bleiben, doch vor den besten Scannern können sie sich nicht verstecken.\r\n;Das Schwarzfeld verhindert die Entdeckung der Tunnellarve und Kundschafter. Jedenfalls meistens!;platzhalter', 1, 2, 10800, 100),
(67, 'Geschütztürme;Abwehrturmbasis;Drüsenevolution;Stachelkanal;Styx-Mod', 'R1x4000;R2x1000', 'T22;T121', 'Der Geschützturm ist ein Multiplex-Sockel der mit verschiedenen Waffenköpfen besetzt werden kann.;Die Abwehrturmbasis ist ein Multiplex-Sockel der mit verschiedenen Waffenköpfen besetzt werden kann.;Die Drüsenevolution ermöglichte es den K’tharr ihre Waffenorgane auch auf ihren Bauten an zu bringen. Da diese Waffen jedoch erst auf kürzere Distanz ihr volles Potential entfalten, sind die Drüsenvarianten schwach und nur für den Notfall geeignet.\r\n;Der Stachelkanal ist ein Multiplex-Sockel der mit verschiedenen Waffenköpfen besetzt werden kann.;platzhalter', 1, 3, 8100, 160),
(68, 'Jägerbuchten;Jägerrampen;Brutbeutelevolution I;Larventaschen;Xinth-Xc-Box', 'R1x12000;R2x3000;R3x1000', 'T58;T125', 'Ermöglicht es 20 Jäger zu transportieren und zu versorgen.;Ermöglicht es 25 Jäger zu transportieren und zu versorgen.;n sackähnlichen Beuteln schlummern Spiders vor sich hin, bis sie geweckt werden und in den Angriff übergehen können. Die kleine Variante der Brutbeutel fasst 20 Spider und ist auf jeder Tarantula vor zu finden.\r\n;Ermöglicht es 25 Jäger zu transportieren und zu\r\n\r\nversorgen.;platzhalter', 1, 7, 16200, 150),
(69, 'Schwere Jägerbuchten;Jägerhangars;Brutbeutelevolution II;Doppelte Larventaschen;Xinth-Xc-Container', 'R1x24000;R2x4000;R3x2000;R4x2000', 'T59;T68;T127', 'Ermöglicht es 50 Jäger zu transportieren und zu \r\nversorgen.;Ermöglicht es 50 Jäger zu transportieren und zu versorgen.;Die Größe der Black Widow ermöglicht es den K’tharr, größere Brutbeutel zu züchten. Diese transportieren 50 Spiders, welche sich ohne Rücksicht auf Verluste auf den Gegner stürzen. \r\n;Ermöglicht es 50 Jäger zu transportieren und zu versorgen.;platzhalter', 1, 9, 32400, 150),
(70, 'Virtuelles Transmitterfeld;Virtuelles Transmitterfeld;Virtuelles Transmitterfeld;Virtuelles Transmitterfeld;Virtuelles Transmitterfeld', 'R1x50000;R2x20000;R3x6000;R4x4000;R5x1', 'T65;T124', 'Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.;Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.;Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.;Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.;Ermöglicht Transmitterverbindungen auch ausserhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', 1, 6, 64800, 50),
(71, 'Mauerbrecher;Palisadensturm;Netzzerreißer;Wallbrecher;MPNW-Bombe', 'R1x5000;R2x10000', 'T121', 'Diese Technologie wurden speziell als Waffe gegen Verteidigungseinheiten entwickelt.;Diese Technologie wurden speziell als Waffe gegen Verteidigungseinheiten entwickelt.;Diese Technologie wurden speziell als Waffe gegen Verteidigungseinheiten entwickelt.;Diese Technologie wurden speziell als Waffe gegen Verteidigungseinheiten entwickelt.;Diese Technologie wurden speziell als Waffe gegen Verteidigungseinheiten entwickelt.', 1, 3, 12000, 150),
(72, 'Cyborginkubationstank;Cyborginkubationstank;Cyborginkubationstank;Cyborginkubationstank;Cyborginkubationstank', 'R1x10000;R2x5000;R3x1000;R4x2000', 'T71', 'Dient zur Züchtung des biologischen Cyborggewebes.;Dient zur Züchtung des biologischen\r\n\r\nCyborggewebes.;Dient zur Züchtung des biologischen Cyborggewebes.;Dient zur Züchtung des biologischen Cyborggewebes.;Dient zur Züchtung des biologischen Cyborggewebes.', 1, 1, 21600, 1000),
(73, 'Cyborgsteuerungskristall;Cyborgsteuerungskristall;Cyborgsteuerungskristall;Cyborgsteuerungskristall;Cyborgsteuerungskristall', 'R1x5000;R2x60000;R3x2000;R4x2000', 'T71', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.;Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.;Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.;Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.;Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.', 1, 1, 48600, 1000),
(74, 'Cyborgimplantate;Cyborgimplantate;Cyborgimplantate;Cyborgimplantate;Cyborgimplantate', 'R1x10000;R2x6000;R3x10000;R4x7000', 'T71', 'Sie bilden das Grundgerüst des Cyborgs.;Sie bilden das Grundgerüst des Cyborgs.;Sie bilden das Grundgerüst des Cyborgs.;Sie bilden das Grundgerüst des Cyborgs.;Sie bilden das Grundgerüst des Cyborgs.', 1, 1, 32400, 1000),
(75, 'Sprungfeldfrequenzmodulator;Sprungfeldfrequenzmodulator;Blockerstrahlmodulator;Frequenzzusatzwabe;EMP-Domimodulator', 'R1x500000;R2x100000;R3x50000;R4x20000;R5x75', 'T5;T128', 'Der Sprungfeldfrequenzmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.;Der Sprungfeldfrequenzmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepaßt.;Der Blockerstrahlmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.;Der Sprungfeldfrequenzmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.;platzhalter', 1, 10, 129600, 100),
(80, 'Kollektoren;Sonnenschild;Wandlerzelle;Arbeiter;Assimilatoren', 'R1x500;R2x50', 'T8', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.;Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.;Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.;Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.;Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', 1, 1, 180, 100),
(81, 'Hornisse;Caesar;Spider;Wespe;Xinth-Xc', 'R1x1000;R2x250', 'T120;T40;T45;T55;T60;T129', 'Die Hornisse ist der Jäger der Ewigen. Die erste Datierung dieses Schiffes ist nicht mehr aufzufinden, da die Konstruktion aus der Zeit vor dem Zusammenbruch der Zentralregierung der Ewigen stammt. Ausgestattet mit zwei Hochleistungslaser und einer hochwertigen Panzerung dient das Schiff als Allroundeinheit der Ewigen. Auf Grund der Größe ist das Schiff mit keinem Sprungantrieb ausgerüstet und sollte daher mittels Kreuzern, Schlachtschiffen oder Trägerschiffen zur Schlacht befördert werden.;Der Caesar-Jäger ist ein kleines wendiges Kampfschiff, welches es zuerst auf die Kollektorschiffklasse und Jäger des Gegners abgesehen hat. Der wendige Jäger ist nur mit einem kleinen Antrieb ausgerüstet und kann weite Strecken nur sehr langsam zurücklegen. Daher werden Caesar meist in Verbindung mit Imperator oder Excalibur eingesetzt, die ihnen durch die Technologie der Jägerrampen als Trägerschiffe dienen können.;(Jägerklasse)\r\nSpider mögen zwar klein und nur leicht bewaffnet sein, doch sollte man sie, so wie auch alle anderen K’tharr Schiffe, auf keinen Fall unterschätzen. Zu Dutzenden fallen sie über einzelne kleine Jägereinheiten her und reißen sie regelrecht in Fetzen. Doch sollen sie es - Gerüchten zu Folge - auch schon geschafft haben, große Kampfschiffe zu zerstören. Egal ob die Gerüchte nun stimmen oder nicht, K’tharr Flotten mit vielen Spiders sind eine sehr ernstzunehmende Bedrohung auf die reagiert werden muss. Allerdings sind ihre Antriebe nicht für lange Flüge ausgelegt, weshalb sie für lange Strecken von Trägerschiffen transportiert werden sollten.  ;(Jäger-Klasse) Der Wespe-Jäger ist ein kleines wendiges Kampfschiff, das es zu allererst auf die Transmitterschiffe und Jäger des Gegners abgesehen hat. Der wendige Jäger ist nur mit einem kleinen Antrieb ausgerüstet und kann weite Strecken nur sehr langsam zurücklegen. Daher werden Wespen meist in Verbindung mit Kreuzern oder Schlachtschiffen eingesetzt, die ihnen durch die Technologie der Jägerbuchten als Trägerschiffe dienen können.;platzhalter', 1, 2, 7200, 90),
(82, 'Guillotine;Paladin;Arctic Spider;Feuerskorpion;Hunm-oc', 'R1x4000;R2x1000', 'T13;T41;T45;T46;T56;T61;T122', 'Der Name dieser Jagdbootklasse stammt aus den Aufzeichnungen von der Schlacht bei Port Armor, einer Forschungsstation. Die Staffel aus Prototypen dieses Raumschiffes entschied dort den Kampf gegen mehrere angreifende Piratenverbände. Das Schiff ist als Abwehrwaffe gegen Jäger konstruiert worden, wurde jedoch inzwischen mehrfach überarbeitet.;(Jagdboot-Klasse) Der Paladin hat als Hauptwaffe Impulsblaster, die gegnerische Schiffe außer Gefecht setzen können egal wie groß das Schiff ist. Je größer aber das gegnerische Schiff um so mehr Treffer benötigt aber der Paladin um es lahm zulegen.;(Jagdbootklasse)\r\nObwohl Arctic Spiders ebenso wie die Spiders mit Lichtfäden ausgerüstet sind, ist ihre Primärwaffe doch der Lähmfaden. Ihre Primäraufgabe besteht darin, gegnerische Großkampfschiffe kampfunfähig zu machen, so dass diese keine Bedrohung mehr sind. Sie stürzen sich in Gruppen auf jedes große Schiff und selbst Giganten der Schlachtschiffklasse trudeln nach ein paar gezielten Treffern nur noch hilflos durchs Weltall. \r\n;(Jagdboot-Klasse) Der Feuerskorpion hat als Hauptwaffe einen Giftstachel, der gegnerische Schiffe außer Gefecht setzen können egal wie groß das Schiff ist. Je größer aber das gegnerische Schiff um so mehr Treffer benötigt aber der Feuerskorpion um es zu lähmen.;platzhalter', 1, 4, 14400, 100),
(83, 'Schakal;Vollstrecker;Werespider;Geisterschrecke;Ez-maC', 'R1x15000;R2x5000;R3x1000', 'T130;T42;T47;T51;T57;T62;T124', 'Der Schakal ist der momentan höchst entwickelte Zerstörer der Ewigen und besticht im Kampf durch seine Geschwindigkeit und groß ausgelegtes Waffenarsenal, welches ihn gegen mehrere Schiffe kampffähig macht.;(Zerstörer-Klasse) Der Vollstrecker ist ein wendiges Großkampfschiff und am besten geeignet Jagdboote in die ewigen Jagdgründe zu schicken. Aber auch gegen Kreuzer und Schlachtschiffe macht sich der Vollstrecker ganz gut.;(Zerstörerklasse)\r\nBei dem Versuch die Kampfkraft der eigenen Schiffe noch mehr zu erhöhen, manipulierten die K’tharr am Gen-Code der Arctic Spiders. Sie schafften es zwar diese zu vergrößern und ihre Waffen weitaus effektiver zu nutzen, doch der ‚Charakter’ dieser Schiffe ist alles andere als berechenbar. Werespiders rasen mit Vorliebe durch die gegnerischen Flotten und hinterlassen dabei die rauchenden Wracks sämtlicher Schiffe, die dumm genug waren, sich ihnen in den Weg zu stellen. Doch die Werespiders gehen in ihrem Zerstörungsrausch nicht planlos vor. Immer wieder wird berichtet, dass Schiffe der Jagdbootklasse ihre ersten Opfer sind, gefolgt von Kreuzerklassen und Schlachtschiffklassen. \r\n;(Zerstörer-Klasse) Die Geisterschrecke ist ein wendiges Großkampfschiff und am besten geeignet Jagdboote in die ewigen Jagdgründe zu schicken. Aber auch gegen Kreuzer und Schlachtschiffe macht sich die Geisterschrecke ganz gut.;platzhalter', 1, 6, 28800, 100),
(84, 'Marauder;Imperator;Tarantula;Skarabäus;Zao-tuX', 'R1x30000;R2x10000;R3x1000;R4x1500', 'T131;T43;T48;T52;T58;T63;T68;T126', 'Als kleiner Träger und Transportschiffe wurde der Kreuzer in den frühen Gefechten kaum mit Waffen ausgestattet, so dass einzig die Jäger die Raumgefechte entschieden. In weiteren Entwicklungsstufen wurden Panzerung und Waffen aufgerüstet, so dass er inzwischen ein vollwertiges Kampfschiff ist.;(Kreuzer-Klasse) Der Imperator ist der kleine Bruder des Schlachtschiffes. Er ist stärker bewaffnet als ein Zerstörer und konzentriert sich hauptsächlich auf größere Schiffe.;(Kreuzerklasse)\r\nMit den Tarantulas erschufen die K’tharr die ersten Großraumschiffe. Ausgerüstet mit einer Unmenge an Waffensymbionten können sie es mühelos mit allen größeren Schiffen aufnehmen. Doch auf Grund ihrer Trägheit sind sie kaum in der Lage mit Jägern fertig zu werden. Dies war auch der Grund weshalb nach einigen Testreihen die Tarantula mit Brutbeuteln ausgerüstet wurde, worin sie bis zu 20 Spiders transportieren kann. \r\n;(Kreuzer-Klasse) Der Skarabäus ist der kleine Bruder des Schlachtschiffes. Er ist stärker bewaffnet als ein Zerstörer und konzentriert sich hauptsächlich auf größere Schiffe.;KAPAZITÄT BETRÄGT 40 JÄGER!', 1, 8, 57600, 100),
(85, 'Zerberus;Excalibur;Black Widow;Mantis;Lor-ReX', 'R1x50000;R2x20000;R3x2000;R4x4000;R5x2', 'T132;T44;T49;T53;T54;T59;T70;T64;T69;T128', 'Der Zerberus war das erste Schlachtschiff, welches im Weltall auftauchte. Die massiven Waffen und Panzerungen machen ihn zum Schrecken auf dem Schlachtfeld. Da die Bewaffnung auf den Kampf gegen Großkampfschiffe ausgelegt ist, besitzt er Jägertransportkapazitäten um ihn gegen Bomber und Jäger zu schützen. Er kann insgesamt 80 Jäger in den Kampf führen. Ebenso ist das Schlachtschiff mit einem Transmitter für Bergungsoperationen und den besten Offizieren ausgestattet.;(Schlachtschiff-Klasse) Die Excalibur gilt als König der Raumschlacht. Mit dem großen Waffenarsenal kämpft es vor allem gegen seinesgleichen, jedoch sind auch Kreuzer und Zerstörer willkommene Opfer. Außerdem besitzt es eine Ladekapazität für 80 Jäger.;(Schlachtschiffklasse)\r\nDie Black Widow ist zweifellos das größte und gefährlichste Schiff, welches die K’tharr jemals geschaffen haben. Nicht nur das sie vor Waffen starrt und damit problemlos alle größeren Schiffe zu Weltraumschrott verarbeitet, sie ist auch noch in der Lage, bis zu 90 Spiders zu transportieren, die ihr kleine Schiffe vom Leib halten.\r\n;(Schlachtschiff-Klasse) Die Mantis gilt als König der Raumschlacht. Mit dem großen Waffenarsenal kämpft es vor allem gegen seinesgleichen, jedoch sind auch Kreuzer und Zerstörer willkommene Opfer. Außerdem besitzt es eine Ladekapazität für 100 Jäger.;KAPAZITÄT BETRÄGT 120 JÄGER!', 1, 10, 86400, 110),
(86, 'Nachtmar;Phalanx;Hellspider;Höllenkäfer;Xor-L2R', 'R1x1500;R2x500', 'T13;T41;T71;T56;T61;T122', 'Der Nachtmar ist eine weitere Jagdbootklasse, welche jedoch mit schweren Bomben ausgestattet wurde. Im Krieg zwischen den Ewigen und den Ishtar entschied die Entwicklung des Nachtmars die Kapitulation der Ishtar, als deren Verteidigungsanlagen direkt angegriffen werden konnten. Der Nachtmar ist jedoch sehr empfindlich gegen feindliche Jäger und sollte daher von eigenen Jägern geschützt werden. Wie auch Jäger besitzt er nur einen schwachen Antrieb und muss daher transportiert werden. Dabei verbraucht er doppelt soviel Platz wie ein Jäger.;(Jagdboot-Klasse) Die Phalanx ist eine neuere Entwicklung der Ishtar, um gegen planetare Verteidigung vorzugehen. Sie ist wie alle Bomber mit einer hohen Bombenlast ausgestattet, die sie im Raumkampf beinahe nutzlos macht. Anders aber als andere Bomber sind ihre Bomben nicht mit Sprengköpfen beladen, sondern mit EMP Köpfen, die die Verteidigungsanlagen deaktivieren und Lebewesen unbeeinflusst lassen.;(Jagdboot-Klasse) Der Hellspider wurde speziell dazu entwickelt eine massive planetare Verteidigung zu durchbrechen und sie evtl. auszuschalten. Er ist sehr wirksam gegen planetare Geschütze, aber wegen der hohen Bombenlast nicht geeignet im Raumkampf.;(Jagdboot-Klasse) Der Höllenkäfer wurde speziell dazu entwickelt eine massive planetare Verteidigung zu durchbrechen und sie evtl. auszuschalten. Er ist sehr wirksam gegen planetare Geschütze, aber wegen der hohen Bombenlast nicht geeignet im Raumkampf.;platzhalter', 1, 4, 9000, 115),
(87, 'Transmitterschiff;Merlin;Netzfänger;Sammler;Os-mTz', 'R1x2000;R2x1000', 'T13;T23;T56;T61;T65;T122', 'Transmitterschiffe, auch bekannt als Kollektorenpirat. Mit Hilfe dieser Schiffe werden die Kollektoren zum eigenen Planeten gebracht. Die benötigte Energie zum Transfer mittels Transmitterstrahl ist jedoch so hoch, dass das Schiff durch den Transfer zerstört wird. Transmitterschiffe warten meist hinter den eigenen Schlachtlinien, um erst bei einer gewonnen Schlacht in das Zielgebiet einzudringen.;(Transmitterschiffe-klasse) Merline, auch als Sonnenschildpirat bekannt. Mit Hilfe dieser Schiffe werden die Sonnenschilder zu dem eigenen Planeten gebracht. Die benötigte Energie zum Transfer mittels Dimensionsfeld ist jedoch so hoch, daß das Schiff durch den Transfer zerstört wird.;(Kollektorschiffklasse)\r\nNatürlich könnten die K’tharr sich ihre Kollektoren auch selber bauen, doch ist es manchmal wesentlich einfacher, sie einem schwachen Gegner abzunehmen. Zu diesem Zweck erschufen sie die Netzfänger, welche mit einem einzigartigem Organismus ausgestattet sind. Hat ein Netzfänger einen Kollektor erreicht, umschließt er ihn und tötet sich selbst beim Aufbauen eines Portals. Der Kollektor durchquert dieses Portal und erscheint ohne nennenswerten Zeitverlust auf dem Heimatplaneten des Netzfängers.\r\n;(Transmitterschiff) Sammler, auch als Arbeiterpirat bekannt. Mit Hilfe dieser Schiffe werden die Arbeiter zu dem eigenen Planeten gebracht. Die benötigte Energie zum Transfer mittels Transwabenstrahl ist jedoch so hoch, daß das Schiff durch den Transfer zerstört wird.;platzhalter', 1, 4, 10800, 110),
(88, 'Hydra;Colossus;Gigantula;Ekelbrüter;Bi-SoX', 'R1x50000;R2x30000;R3x5000;R4x5000;R5x1', 'T132;T44;T45;T59;T64;T69;T128', 'Eine Neukonstruktion der Ewigen, welche den erhöhten Bedarf an Jägern und Bombern in Raumgefechten decken soll. Insgesamt kann der Hydra 300 Jäger mit in die Schlacht transportieren.;(Schlachtschiff-Klasse) Der Colossus ist ein Trägerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um große Mengen Jäger transportieren zu können wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste dafür die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen Jägern wieder wettmachen. Die Ladekapazität beträgt 150 Jäger;(Schlachtschiff-Klasse) Die Gigantula ist ein Trägerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um große Mengen Jäger transportieren zu können wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste dafür die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen Jägern wieder wettmachen. Die Ladekapazität beträgt 250 Jäger;(Schlachtschiff-Klasse) Der Ekelbrüter ist ein Trägerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um große Mengen Jäger transportieren zu können wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste dafür die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen Jägern wieder wettmachen.;KAPAZITÄT BETRÄGT 400 JÄGER!', 1, 10, 72000, 130),
(89, 'Frachtschiff;Frachtbarke;Frachtnetz;Frachtträger;Fracht-SoX', 'R1x5000;R2x1500;R3x500', 'T130;T42;T45;T46;T57;T62;T124', 'Diese Einheit dient dem Transport von Waren von einem System in ein anderes System.;Diese Einheit dient dem Transport von Waren von einem System in ein anderes System.;Diese Einheit dient dem Transport von Waren von einem System in ein anderes System.;Diese Einheit dient dem Transport von Waren von einem System in ein anderes System.;', 1, 6, 14000, 120),
(90, 'Hyperion;Dragonfire;Titanspider;Die Königin;Titan-X', 'R1x1000000;R2x500000;R3x250000;R4x250000;R5x100;I1x3000;I2x1', 'T141;T85', 'Die Titanen-Klasse verfügt über unglaubliche Feuerkraft und eine imposante Panzerung.;Die Titanen-Klasse verfügt über unglaubliche Feuerkraft und eine imposante Panzerung.;Die Titanen-Klasse verfügt über unglaubliche Feuerkraft und eine imposante Panzerung.;Die Titanen-Klasse verfügt über unglaubliche Feuerkraft und eine imposante Panzerung.;', 1, 11, 42000, 150),
(100, 'Jägergarnison;Brechergarnison;Schwarm der Nestverteidiger;Larvenstock;Xinth-Base', 'R1x10000;R2x2500', 'T113;T13;T22;T122', 'Die Jägergarnisionen dienen als Stützpunkt für modifizierte Hornissen. Ihr Operationsbereich ist einzig das eigene System.;Die Brechergarnison beherbergt mehrere Jäger. Jedoch sind diese Jäger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden können.;Der Schwarm der Nestverteidiger beherbergt mehrere Jäger. Jedoch sind diese Jäger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden können.;Der Larvenstock beherbergt mehrere Jäger. Jedoch sind diese Jäger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden können.;platzhalter', 1, 4, 7200, 120),
(101, 'Raketenturm;Balistenturm;Sporendrüse;Speichelbatterie;EMP-Kanonen-Styx', 'R1x800;R2x550', 'T114;T22;T51;T67;T123', 'Der Raketenturm richtet seine Waffensysteme gegen kleinere, jedoch stärker gepanzerte Ziele.;Der Balistenturm ist mit mehreren Ballistarakete bestückt und dient zur Abwehr von Jagdbooten und Transmitterschiffen.;Sporendrüsen wurden für die Jagd auf Schiffe der Jagdbootklasse geschaffen. Zu diesem Zweck sind sie mit einer Vielzahl von hochexplosiven Sporen ausgerüstet.\r\n;Die Speichelbatterie ist mit haufenweise Nadelspeichel bestückt und dient zur Abwehr von Jagdbooten und Transmitterschiffen.;platzhalter', 1, 5, 18000, 150),
(102, 'Laserturm;Laserlanzenturm;Lichtdrüse;Bodenstachel;X-Magma-Styx', 'R1x250;R2x500', 'T115;T22;T45;T67;T124', 'Der Laserturm ist die primäre Abwehrwaffe gegen leichte Ziele.;Der Laserlanzenturm ist der leichteste Geschützturm.;Diese einfachste Form der Verteidigungsdrüsen wird mit Lichtfäden ähnlichen Organen herangezüchtet. Ihr erhabenes Design macht sie auch zu einem echten Augenschmaus.;Der Bodenstachel ist der leichteste Geschützturm und hauptsachlich zur Abwehr von Jägern geeignet.;platzhalter', 1, 6, 9000, 110),
(103, 'Autokanonenturm;Bolzenkanonenturm;Materiedrüse;Giftstachelbatterie;Zermalmer-Styx', 'R1x2500;R2x300;R3x50', 'T22;T47;T67;T126', 'Der Autokanonenturm ist die primäre Waffen gegen leichte bis mittlere Großkampfschiffe.;Diese großkalibrige Kanone ist der wahre Panzerbrecher und vernichtet Zerstörer und Kreuzer massenweise.;Ihre Geschosse durchbrechen mit Leichtigkeit die stärkste Panzerung. Deshalb sind sie besonders für die Vernichtung von Zerstörern und Kreuzern geeignet. ;Diese großkalibrige Kanone ist der wahre Panzerbrecher und vernichtet Zerstörer und Kreuzer massenweise.;platzhalter', 1, 8, 25200, 110),
(104, 'Plasmaturm;Plasmalanzenturm;Plasmadrüse;Feuerstachelbatterie;ER-Plasmawerfer-Styx', 'R1x2000;R2x1000;R3x500', 'T132;T22;T48;T67;T128', 'Der Plasmaturm wurde auf Basis der Waffensysteme der Kreuzer entwickelt und vermag vernichtenden Schaden gegen alle Großkampfschiffe ausrichten. Für kleinere Ziele ist das Geschoss jedoch zu langsam.;Der Plasmalanzenturm ist der Turm der jedem Schlachtschiff Respekt abverlangt und für klare Verhältnisse sorgt.;Die größte und gefährlichste aller Drüsen zerstört mit Leichtigkeit jede Schlachtschiffklasse, die sich in ihre Waffenreichweite wagt. ;Die Feuerstachelbatterie ist der Turm der jedem Schlachtschiff Respekt abverlangt und für klare Verhältnisse sorgt.;platzhalter', 1, 10, 43200, 140),
(110, 'Sonde;Knecht;Vielauge;Kundschafter;Infiltrator', 'R1x500;R2x500', 'T9;T60;T66;T120;T129', 'Die Sonde ist neben dem Spion die 2. Möglichkeit, um andere Spieler auszukundschaften. Sie scannt nur oberflächlich die Eigenschaften ihres Ziels, genauere Informationen beschafft der Spion wesentlich besser. Da die Sonde mit enorm hohen Geschwindigkeiten fliegen soll, überlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus.\r\nSonden kehren nie zurück. Sonden können von den feindlichen Scannern entdeckt werden, abhängig vom Typ des Scanners.;Der Knecht (Sonde) ist neben dem Knappen die 2. Möglichkeit, um andere Spieler auszukundschaften. Sie scannt nur oberflächlich die Eigenschaften ihres Ziels, genauere Informationen beschafft der Knappe wesentlich besser. Da die Sonde mit enorm hohen Geschwindigkeiten fliegen soll, überlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus. Sonden kehren nie zurück.\r\n\r\nSonden können von den feindlichen Scannern entdeckt werden, abhängig vom Typ des Scanners.;Vielaugen (Sonden) sind kleine merkwürdige Geschöpfe. Ihre Augen ähneln denen des Raumblickes, womit sie in der Lage sind, Planeten aus genügender Entfernung auf Schiffe, Gebäude und Kollektoren zu untersuchen. Aufgrund ihrer Größe sind sie nur schwer von Scannern auszumachen und wenn sie entdeckt werden, erscheinen sie nur als normale Sonden, wodurch ihre Herkunft unbestimmbar bleibt. Wie die Netzfänger springen die Vielaugen durch Portale, um in kürzester Zeit bei ihrem Ziel zu sein. Die Energie reicht jedoch nur für einen Sprung und für die einmalige Übertragung der erspähten Daten. Nach diesem Kraftakt stirbt das Vielauge.;Der Kundschafter (Sonde) ist neben dem Wandellarve die 2. Möglichkeit, um andere Spieler auszukundschaften. Sie scant nur oberflächlich die Eigenschaften ihres Ziels, genauere Informationen beschafft die Wandellarve wesentlich besser. Da der Kundschafter mit enorm hohen Geschwindigkeiten fliegen soll, überlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus. Sonden kehren nie zurück. Kundschafter können von den feindlichen Scannern entdeckt werden, abhängig vom Typ des Scanners.;platzhalter', 1, 2, 1800, 110),
(111, 'Geheimagent;Knappe;Zecke;Wandellarve;Morph-DX', 'R1x500;R2x500;R3x200;R4x100', 'T66;T122', 'Der Spion / Geheimagent:<br><br>\r\n\r\nDer Spion ist eine der wichtigsten Einheiten des Spiels. Mit ihm kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.;Der Knappe / Geheimagent:<br><br>\r\n\r\nDer Knappe ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihm kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.;Der Spion / Geheimagent:<br><br>\r\n\r\nDer Spion ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihm kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.;Die Wandellarve / Geheimagent:<br><br>\r\n\r\nDie Wandellarve ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihr kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.;platzhalter', 1, 4, 7200, 130),
(112, 'Konstruktionszentrum III;Werkstatt III;Zentralbau III;Stock III;Replikator III', 'R1x16000;R2x4000', 'T2', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 3, 600, 0),
(113, 'Konstruktionszentrum IV;Werkstatt IV;Zentralbau IV;Stock IV;Replikator IV', 'R1x32000;R2x8000', 'T112', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 4, 2400, 0),
(114, 'Konstruktionszentrum V;Werkstatt V;Zentralbau V;Stock V;Replikator V', 'R1x50000;R2x12000;R3x3000', 'T113', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 5, 3600, 0),
(115, 'Konstruktionszentrum VI;Werkstatt VI;Zentralbau VI;Stock VI;Replikator VI', 'R1x75000;R2x18000;R3x4500', 'T114', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 6, 5400, 0),
(116, 'Konstruktionszentrum VII;Werkstatt VII;Zentralbau VII;Stock VII;Replikator VII', 'R1x100000;R2x24000;R3x6000', 'T115', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 7, 7200, 0),
(117, 'Konstruktionszentrum VIII;Werkstatt VIII;Zentralbau VIII;Stock VIII;Replikator VIII', 'R1x125000;R2x30000;R3x7500;R4x2000', 'T116', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 8, 9000, 0),
(118, 'Konstruktionszentrum IX;Werkstatt IX;Zentralbau IX;Stock IX;Replikator IX', 'R1x150000;R2x36000;R3x10000;R4x4000', 'T117', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 9, 10800, 0),
(119, 'Konstruktionszentrum X;Werkstatt X;Zentralbau X;Stock X;Replikator X', 'R1x175000;R2x42000;R3x12500;R4x6000;R5x50', 'T118', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;platzhalter', 0, 10, 12600, 0),
(120, 'Forschungszentrum II;Alchemielabor II;Kammer der Evolution II;Netzwerk des Denkens II;Kompilator II', 'R1x12000;R2x4000', 'T2;T8', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 2, 200, 20),
(121, 'Forschungszentrum III;Alchemielabor III;Kammer der Evolution III;Netzwerk des Denkens III;Kompilator III', 'R1x24000;R2x8000', 'T112;T120', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 3, 600, 20),
(122, 'Forschungszentrum IV;Alchemielabor IV;Kammer der Evolution IV;Netzwerk des Denkens IV;Kompilator IV', 'R1x48000;R2x16000', 'T113;T121', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 4, 2400, 20),
(123, 'Forschungszentrum V;Alchemielabor V;Kammer der Evolution V;Netzwerk des Denkens V;Kompilator V', 'R1x60000;R2x20000;R3x2000', 'T114;T122', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 5, 4000, 20),
(124, 'Forschungszentrum VI;Alchemielabor VI;Kammer der Evolution VI;Netzwerk des Denkens VI;Kompilator VI', 'R1x75000;R2x25000;R3x3000', 'T115;T123', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 6, 6000, 20),
(125, 'Forschungszentrum VII;Alchemielabor VII;Kammer der Evolution VII;Netzwerk des Denkens VII;Kompilator VII', 'R1x90000;R2x30000;R3x4000', 'T116;T124', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 7, 8000, 20),
(126, 'Forschungszentrum VIII;Alchemielabor VIII;Kammer der Evolution VIII;Netzwerk des Denkens VIII;Kompilator VIII', 'R1x105000;R2x35000;R3x5000;R4x2000', 'T117;T125', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 8, 10000, 20),
(127, 'Forschungszentrum IX;Alchemielabor IX;Kammer der Evolution IX;Netzwerk des Denkens IX;Kompilator IX', 'R1x120000;R2x40000;R3x6000;R4x4000', 'T118;T126', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 9, 12000, 20),
(128, 'Forschungszentrum X;Alchemielabor X;Kammer der Evolution X;Netzwerk des Denkens X;Kompilator X', 'R1x125000;R2x45000;R3x7000;R4x8000;R5x75', 'T119;T127', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 10, 14000, 20),
(129, 'Raumwerft I;Raumschmiede I;Schwarmstock I;Drohnenwabe I;Dualassambler I', 'R1x10000;R2x2000', 'T2', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;platzhalter', 0, 2, 600, 30),
(130, 'Raumwerft III;Raumschmiede III;Schwarmstock III;Drohnenwabe III;Dualassambler III', 'R1x50000;R2x12000;R3x1000', 'T115;T13', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;platzhalter', 0, 6, 7200, 30),
(131, 'Raumwerft IV;Raumschmiede IV;Schwarmstock IV;Drohnenwabe IV;Dualassambler IV', 'R1x55000;R2x18000;R3x2500;R4x1000', 'T117;T130', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;platzhalter', 0, 8, 18000, 30),
(132, 'Raumwerft V;Raumschmiede V;Schwarmstock V;Drohnenwabe V;Dualassambler V', 'R1x80000;R2x27000;R3x4000;R4x2500;R5x10', 'T119;T131', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;platzhalter', 0, 10, 32000, 30),
(133, 'BS-Artefaktplatz I;BS-Artefaktplatz I;BS-Artefaktplatz I;BS-Artefaktplatz I;BS-Artefaktplatz I', 'R1x4000;R2x1000', 'T129', 'Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;', 2, 2, 600, 200),
(134, 'BS-Artefaktplatz II;BS-Artefaktplatz II;BS-Artefaktplatz II;BS-Artefaktplatz II;BS-Artefaktplatz II', 'R1x8000;R2x2000', 'T13;T133', 'Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;', 2, 4, 12000, 200),
(135, 'BS-Artefaktplatz III;BS-Artefaktplatz III;BS-Artefaktplatz III;BS-Artefaktplatz III;BS-Artefaktplatz III', 'R1x40000;R2x10000;R3x2000;R4x500', 'T130;T134', 'Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;', 2, 6, 16000, 200),
(136, 'BS-Artefaktplatz IV;BS-Artefaktplatz IV;BS-Artefaktplatz IV;BS-Artefaktplatz IV;BS-Artefaktplatz IV', 'R1x46000;R2x15000;R3x3000;R4x1000;I1x1000', 'T131;T135;B1x1', 'Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;', 2, 8, 26000, 200),
(137, 'BS-Artefaktplatz V;BS-Artefaktplatz V;BS-Artefaktplatz V;BS-Artefaktplatz V;BS-Artefaktplatz V', 'R1x60000;R2x22000;R3x4000;R4x2000;I1x2000', 'T132;T136;B1x2', 'Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;', 2, 10, 32000, 200),
(138, 'BS-Artefaktplatz VI;BS-Artefaktplatz VI;BS-Artefaktplatz VI;BS-Artefaktplatz VI;BS-Artefaktplatz VI', 'R1x80000;R2x40000;R3x10000;R4x4000;I1x3000', 'T141;T137;B1x3', 'Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;Mit dieser Technologie wird auf einem Basisschiff ein Platz für die Installation eines Artefaktes geschaffen.;', 2, 11, 40000, 200);
INSERT INTO `de_tech_data` (`tech_id`, `tech_name`, `tech_build_cost`, `tech_vor`, `tech_desc`, `tech_typ`, `tech_level`, `tech_build_time`, `tech_sort_id`) VALUES
(139, 'Konstruktionszentrum XI;Werkstatt XI;Zentralbau XI;Stock XI;Replikator XI', 'R1x200000;R2x80000;R3x15000;R4x10000;R5x75', 'T119', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.\r\n;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;', 0, 11, 16000, 0),
(140, 'Forschungszentrum XI;Alchemielabor XI;Kammer der Evolution XI;Netzwerk des Denkens XI;Kompilator XI', 'R1x150000;R2x60000;R3x10000;R4x12000;R5x100', 'T139;T128', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 11, 16500, 20),
(141, 'Raumwerft VI;Raumschmiede VI;Schwarmstock VI;Drohnenwabe VI;Dualassambler VI', 'R1x100000;R2x35000;R3x8000;R4x5000;R5x20', 'T132;T139;T140', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.;platzhalter', 0, 11, 35000, 30),
(142, 'Weltraumhafen;Weltraumhafen;Weltraumhafen;Weltraumhafen;Weltraumhafen', 'R1x12000;R2x4000;R3x5000;R4x3000', 'T4;T89;T124;T143', 'Bei dieser Technologie handelt es sich um eine Station im Weltraum, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;Bei dieser Technologie handelt es sich um eine Station im Weltraum, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;Bei dieser Technologie handelt es sich um eine Station im Weltraum, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;Bei dieser Technologie handelt es sich um eine Station im Weltraum, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;', 3, 6, 6000, 300),
(143, 'BS-Konstruktionsmodul;BS-Konstruktionsmodul;BS-Konstruktionsmodul;BS-Konstruktionsmodul;BS-Konstruktionsmodul', 'R1x20000;R2x5000;R3x500;R4x500', 'T130;T124', 'Mit dieser Technologie erhält das Basisschiff die Möglichkeit planetare Gebäude und Stationen im Weltraum zu errichten.;Mit dieser Technologie erhält das Basisschiff die Möglichkeit planetare Gebäude und Stationen im Weltraum zu errichten.;Mit dieser Technologie erhält das Basisschiff die Möglichkeit planetare Gebäude und Stationen im Weltraum zu errichten.;Mit dieser Technologie erhält das Basisschiff die Möglichkeit planetare Gebäude und Stationen im Weltraum zu errichten.;', 3, 6, 16000, 210),
(144, 'Planetarer Außenposten;Planetarer Außenposten;Planetarer Außenposten;Planetarer Außenposten;Planetarer Außenposten', 'R1x10000;R2x3000;R3x6000;R4x1200', 'T4;T89;T124;T143', 'Bei dieser Technologie handelt es sich um eine planetare Station, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;Bei dieser Technologie handelt es sich um eine planetare Station, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;Bei dieser Technologie handelt es sich um eine planetare Station, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;Bei dieser Technologie handelt es sich um eine planetare Station, die an das Transmitternetzwerk angeschlossen ist. Sie dient verschiedenen Zwecken, wie z.B. Forschung, Handel, Infrakstrukturerweiterung und Kämpfen.;', 3, 6, 5800, 310),
(145, 'Botschaft;Botschaft;Botschaft;Botschaft;Botschaft', 'R1x10000;R2x3000;R3x6000;R4x1200', 'T4;T89;T124;T143', 'Hiermit lassen sich diplomatische Beziehungen mit fremden Zivilisitionen führen.;Hiermit lassen sich diplomatische Beziehungen mit fremden Zivilisitionen führen.;Hiermit lassen sich diplomatische Beziehungen mit fremden Zivilisitionen führen.;Hiermit lassen sich diplomatische Beziehungen mit fremden Zivilisitionen führen.;', 3, 6, 5800, 320),
(146, 'Eisen-Industrie;Eisen-Industrie;Eisen-Industrie;Eisen-Industrie;Eisen-Industrie', 'R1x12000;R2x2500;R3x5000;R4x2200', 'T125', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 7, 4200, 300),
(147, 'Titan-Industrie;Titan-Industrie;Titan-Industrie;Titan-Industrie;Titan-Industrie', 'R1x15000;R2x4000;R3x7000;R4x4000;I3x200', 'T125;T146', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 7, 4500, 310),
(148, 'Mexit-Industrie;Mexit-Industrie;Mexit-Industrie;Mexit-Industrie;Mexit-Industrie', 'R1x17000;R2x4600;R3x7900;R4x4400;I3x400;I4x200', 'T126;T147', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 8, 4200, 300),
(149, 'Dulexit-Industrie;Dulexit-Industrie;Dulexit-Industrie;Dulexit-Industrie;Dulexit-Industrie', 'R1x19000;R2x5800;R3x9000;R4x5000;I3x800;I4x400;I5x200', 'T126;T148', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 8, 6200, 310),
(150, 'Tekranit-Industrie;Tekranit-Industrie;Tekranit-Industrie;Tekranit-Industrie;Tekranit-Industrie', 'R1x22000;R2x7000;R3x12000;R4x8000;I3x1600;I4x800;I5x400;I6x200', 'T127;T149', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 9, 7200, 300),
(151, 'Ylesenium-Industrie;Ylesenium-Industrie;Ylesenium-Industrie;Ylesenium-Industrie;Ylesenium-Industrie', 'R1x25000;R2x10000;R3x15000;R4x10000;I3x3200;I4x1600;I5x800;I6x400;I7x200', 'T127;T150', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 9, 8000, 310),
(152, 'Serodium-Industrie;Serodium-Industrie;Serodium-Industrie;Serodium-Industrie;Serodium-Industrie', 'R1x30000;R2x15000;R3x20000;R4x12500;I3x6400;I4x3200;I5x1600;I6x800;I7x400;I8x200', 'T128;T151', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 10, 8500, 300),
(153, 'Rowalganium-Industrie;Rowalganium-Industrie;Rowalganium-Industrie;Rowalganium-Industrie;Rowalganium-Industrie', 'R1x60000;R2x30000;R3x40000;R4x20000;I3x12800;I4x6400;I5x3200;I6x1600;I7x800;I8x400;I9x200', 'T128;T152', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 10, 9000, 310),
(154, 'Sextagit-Industrie;Sextagit-Industrie;Sextagit-Industrie;Sextagit-Industrie;Sextagit-Industrie', 'R1x80000;R2x40000;R3x50000;R4x30000;I3x25000;I4x15000;I5x8000;I6x4000;I7x2000;I8x1000;I9x500;I10x200', 'T140;T153', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 11, 10000, 300),
(155, 'Octagium-Industrie;Octagium-Industrie;Octagium-Industrie;Octagium-Industrie;Octagium-Industrie', 'R1x120000;R2x70000;R3x90000;R4x60000;I3x50000;I4x30000;I5x16000;I6x8000;I7x4000;I8x2000;I9x1000;I10x500;I11x200', 'T140;T154', 'Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;Diese Technologie ist eine Kombination von Bergwerk und Hütte und ermöglicht die Produktion von Rohstoffen.;', 3, 11, 11000, 310),
(156, 'Kaserne;Kaserne;Kaserne;Kaserne;Kaserne', 'R1x22000;R2x9500;R3x7000;R4x500', 'T125', 'Diese Technologie dient zur Produktion von Kampfeinheiten und deren Versorgung.;Diese Technologie dient zur Produktion von Kampfeinheiten und deren Versorgung.;Diese Technologie dient zur Produktion von Kampfeinheiten und deren Versorgung.;Diese Technologie dient zur Produktion von Kampfeinheiten und deren Versorgung.;', 3, 7, 3700, 1000),
(157, 'Konstruktionszentrum XII;Werkstatt XII;Zentralbau XII;Stock XII;Replikator XII', 'R1x220000;R2x90000;R3x20000;R4x12000;R5x100', 'T139', 'Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.\r\n;Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.;Dieses Gebäude ist die Grundlage für den Bau von weiteren Gebäuden.;', 0, 12, 20000, 1000),
(158, 'Forschungszentrum XII;Alchemielabor XII;Kammer der Evolution XII;Netzwerk des Denkens XII;Kompilator XII', 'R1x175000;R2x70000;R3x12000;R4x15000;R5x125', 'T157;T140', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.;Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.;', 0, 12, 12500, 1000),
(159, 'Basisstern;Basisstern;Basisstern;Basisstern;Basisstern', 'R1x200000;R2x100000;R3x50000;R4x50000;R5x10;I1x100;I2x10;I3x1000', 'T122;T13', 'Basissterne finden ihre Verwendung in Missionen und Battlegrounds.;Basissterne finden ihre Verwendung in Missionen und Battlegrounds.;Basissterne finden ihre Verwendung in Missionen und Battlegrounds.;Basissterne finden ihre Verwendung in Missionen und Battlegrounds.;', 3, 4, 5000, 210),
(160, 'Tronicator;Tronicator;Tronicator;Tronicator;Tronicator', 'R1x210000;R2x17000;R3x80000;R4x50000;I4x500', 'T139;T140', 'Der Tronicator sammelt über einen längeren Zeitraum Hyperenergie und stößt dann ein Tronic aus.;Der Tronicator sammelt über einen längeren Zeitraum Hyperenergie und stößt dann ein Tronic aus.;Der Tronicator sammelt über einen längeren Zeitraum Hyperenergie und stößt dann ein Tronic aus.;Der Tronicator sammelt über einen längeren Zeitraum Hyperenergie und stößt dann ein Tronic aus.;platzhalter', 0, 11, 37000, 40);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tech_data1`
--

CREATE TABLE `de_tech_data1` (
  `tech_id` int(11) NOT NULL DEFAULT '0',
  `tech_name` varchar(40) NOT NULL DEFAULT '',
  `restyp01` int(11) NOT NULL DEFAULT '0',
  `restyp02` int(11) NOT NULL DEFAULT '0',
  `restyp03` int(11) NOT NULL DEFAULT '0',
  `restyp04` int(11) NOT NULL DEFAULT '0',
  `restyp05` int(11) NOT NULL DEFAULT '0',
  `tech_ticks` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `tech_vor` varchar(40) NOT NULL DEFAULT '',
  `des` text NOT NULL,
  `tech_name1` varchar(40) NOT NULL DEFAULT '',
  `des1` text NOT NULL,
  `tech_name2` varchar(40) NOT NULL DEFAULT '',
  `des2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_tech_data1`
--

INSERT INTO `de_tech_data1` (`tech_id`, `tech_name`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `tech_ticks`, `score`, `tech_vor`, `des`, `tech_name1`, `des1`, `tech_name2`, `des2`) VALUES
(1, 'Konstruktionszentrum', 4000, 1000, 0, 0, 0, 1, 600, '0', 'Das Konstruktionszentrum ist eine kleine Geb&auml;udefabrik. Hier werden einfache Geb&auml;ude durch Roboter gefertigt. Dieses Geb&auml;ude kann erweitert werden.', 'Konstruktionszentrum', 'Das Konstruktionszentrum ist eine kleine Gebäudefabrik. Hier werden einfache Gebäude durch Roboter gefertigt. Dieses Gebäude kann erweitert werden.', '', ''),
(2, 'Erweitertes Konstruktionszentrum', 100000, 10000, 8000, 1000, 0, 48, 14800, '1', 'Dies ist die Erweiterung des Konstruktionszentrum. Hier lassen sich hochwertigere Geb&auml;ude herstellen.', 'Erweitertes Konstruktionszentrum', 'Dies ist die Erweiterung des Konstruktionszentrum. Hier lassen sich hochwertigere Gebäude herstellen.', '', ''),
(3, 'Planetare B&ouml;rse', 8000, 2000, 0, 0, 0, 12, 1200, '65', 'Die Planetare B&ouml;rse erm&ouml;glicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine W&auml;hrung im Universum unn&ouml;tig ist. Dieses Geb&auml;ude kann erweitert werden.', 'Planetare Börse', 'Die Planetare Börse ermöglicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine Währung im Universum unnötig ist. Dieses Gebäude kann erweitert werden.', '', ''),
(4, 'Weltraumhandelsgilde', 40000, 10000, 0, 0, 0, 24, 6000, '2;3', 'Die Weltraumhandelsgilde ist die Erweiterung der Planetaren B&ouml;rse und erm&ouml;glicht den Handel auch &uuml;ber die Sektorengrenzen hinaus.', 'Weltraumhandelsgilde', 'Die Weltraumhandelsgilde ist die Erweiterung der Planetaren Börse und ermöglicht den Handel auch über die Sektorengrenzen hinaus.', '', ''),
(5, 'Sprungfeldbegrenzer', 200000, 200000, 80000, 40000, 75, 72, 175000, '2;63', 'Der Sprungfeldbegrenzer erm&ouml;glicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine gr&ouml;&szlig;ere M&ouml;glichkeit besteht noch Verst&auml;rkung von Verb&uuml;ndeten zu erhalten.', 'Sprungfeldbegrenzer', 'Der Sprungfeldbegrenzer ermöglicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine größere Möglichkeit besteht noch Verstärkung von Verbündeten zu erhalten.', '', ''),
(6, 'Recyclotron', 120000, 80000, 20000, 5000, 0, 96, 36000, '2;65', 'Ist das Recyclotron gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Transmitterfeld eingefangen. Da die gegnerischen Schiffe weiter au&szlig;erhalb dieses Transmitterfeldes liegen, k&ouml;nnen nur die Wracks der verteidigenden Flotten und der eigenen T&uuml;rme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEin Recyclotron gewinnt 10% der Ressourcen der zerst&ouml;rten Schiffe zur&uuml;ck! \r\n', 'Recyclotron', 'Ist das Recyclotron gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Transmitterfeld eingefangen. Da die gegnerischen Schiffe weiter außerhalb dieses Transmitterfeldes liegen, können nur die Wracks der verteidigenden Flotten und der eigenen Türme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEin Recyclotron gewinnt 10% der Ressourcen der zerstörten Schiffe zurück! \r\n', '', ''),
(7, 'Kollektorenfabrik', 6000, 1000, 0, 0, 0, 6, 800, '1', 'Die Kollektorenfabrik stellt die Sonnenkollektoren zur Energiegewinnung her. Sind Kollektoren fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebraucht und auf die Sonne ausgerichtet.', 'Kollektorenfabrik', 'Die Kollektorenfabrik stellt die Sonnenkollektoren zur Energiegewinnung her. Sind Kollektoren fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebraucht und auf die Sonne ausgerichtet.', '', ''),
(8, 'Forschungszentrum', 12000, 3000, 0, 0, 0, 12, 1800, '1', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien erm&ouml;glichen dann den weiteren Bau von Geb&auml;uden, Gesch&uuml;tzt&uuml;rmen und Schiffen.', 'Forschungszentrum', 'Das Forschungszentrum dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.', '', ''),
(9, 'Geheimdienst', 16000, 4000, 0, 0, 0, 18, 2400, '1', 'Der Geheimdienst bietet die M&ouml;glichkeit in geheimen Werkst&auml;tten Spionagesonden zu bauen um mehr &uuml;ber den Gegner zu erfahren.', 'Geheimdienst', 'Der Geheimdienst bietet die Möglichkeit in geheimen Werkstätten Spionagesonden zu bauen um mehr über den Gegner zu erfahren.', '', ''),
(10, 'Weltraumscanner', 4000, 1000, 0, 0, 0, 12, 600, '1', 'Der Weltraumscanner erm&ouml;glicht es ankommende Flotten fr&uuml;hzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit betr&auml;gt die Chance eine Flotte zu erkennen nur 33%! Es k&ouml;nnen aber auch Spionagesonden erkannt werden; hier betr&auml;gt die Chance 15 %. Dieses Geb&auml;ude kann erweitert werden!', 'Weltraumscanner', 'Der Weltraumscanner ermöglicht es ankommende Flotten frühzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit beträgt die Chance eine Flotte zu erkennen nur 33%! Es können aber auch Spionagesonden erkannt werden; hier beträgt die Chance 15 %. Dieses Gebäude kann erweitert werden!', '', ''),
(11, 'Tachyonscanner', 20000, 5000, 1000, 0, 0, 24, 3300, '2;10', 'Diese neue Scannertechnik benutzt einen weitreichenden Tachyonenstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance betr&auml;gt hier 66% und f&uuml;r Spionagesonden 30 %. Dieses Geb&auml;ude kann erweitert werden!', 'Tachyonscanner', 'Diese neue Scannertechnik benutzt einen weitreichenden Tachyonenstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance beträgt hier 66% und für Spionagesonden 30 %. Dieses Gebäude kann erweitert werden!', '', ''),
(12, 'Neutronenscanner', 100000, 40000, 4000, 1000, 0, 48, 19600, '11', 'Diese revolution&auml;re Scannertechnik erm&ouml;glicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. F&uuml;r Spionagesonden gilt eine Wahrscheinlichkeit von 45 %.', 'Neutronenscanner', 'Diese revolutionäre Scannertechnik ermöglicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Für Spionagesonden gilt eine Wahrscheinlichkeit von 45 %.', '', ''),
(13, 'Raumwerft', 10000, 2000, 0, 0, 0, 18, 1400, '1', 'Die Raumwerft dient zur Produktion der modernsten und gr&ouml;&szlig;ten Raumschiffen, die das All je gesehen hat.', 'Raumwerft', 'Die Raumwerft dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.', '', ''),
(14, 'Materieumwandler M', 10000, 1000, 0, 0, 0, 3, 1200, '1', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Umwandler so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Materieumwandler M', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(15, 'Materieumwandler D', 12500, 2000, 0, 0, 0, 6, 1650, '14', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Umwandler so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Materieumwandler D', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(16, 'Materieumwandler I', 15000, 3000, 0, 0, 0, 9, 2100, '15', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Umwandler so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Materieumwandler I', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(17, 'Materieumwandler E', 17500, 4000, 0, 0, 0, 12, 2550, '16', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Umwandler so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Materieumwandler E', 'Die Materieumwandler erzeugen aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Umwandler so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Umwandler zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(18, 'Hochleistungsumwandler M', 100000, 20000, 5000, 2000, 0, 30, 16300, '2;14', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser optimiert.', 'Hochleistungsumwandler M', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.', '', ''),
(19, 'Hochleistungsumwandler D', 125000, 30000, 8000, 3000, 0, 37, 22100, '2;15', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser optimiert.', 'Hochleistungsumwandler D', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.', '', ''),
(20, 'Hochleistungsumwandler I', 150000, 40000, 11000, 4000, 0, 44, 27900, '2;16', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser optimiert.', 'Hochleistungsumwandler I', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.', '', ''),
(21, 'Hochleistungsumwandler E', 175000, 50000, 14000, 5000, 0, 50, 33700, '2;17', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser optimiert.', 'Hochleistungsumwandler E', 'Das sind die Erweiterungen der Materieumwandler. Das Produktionsverhältnis ist hier natürlich besser optimiert.', '', ''),
(22, 'Verteidigungszentrum', 16000, 4000, 0, 0, 0, 18, 2400, '1', 'Das Verteidigungszentrum ist f&uuml;r den Bau und die Verwaltung der planetaren Verteidigungsanlagen zust&auml;ndig.', 'Verteidigungszentrum', 'Das Verteidigungszentrum ist für den Bau und die Verwaltung der planetaren Verteidigungsanlagen zuständig.', '', ''),
(23, 'Kollektorempf&auml;nger', 20000, 20000, 0, 0, 0, 24, 6000, '2;4;65', 'Der Kollektorempf&auml;nger ist ein gigantischer orbitaler Transmitter, der in der Lage ist die Kollektoren zu empfangen, die von den Transmitterschiffen in K&auml;mpfen erbeutet und abgestrahlt werden.', 'Kollektorempfänger', 'Der Kollektorempfänger ist ein gigantischer orbitaler Transmitter, der in der Lage ist die Kollektoren zu empfangen, die von den Transmitterschiffen in Kämpfen erbeutet und abgestrahlt werden.', '', ''),
(24, 'Planetarer Schild', 150000, 200000, 100000, 80000, 20, 96, 137000, '2;44', 'Die planetaren Verteidigungsanlagen f&uuml;hrten den Flotten der Angreifer meist unglaubliche Sch&auml;den zu, waren jedoch Schiffe in der Lage durch die R&auml;nge der verteidigenden Orbitalflotten und der Geschwader der Atmosph&auml;renj&auml;ger zu brechen, konnten sie die Planetenoberfl&auml;che mit gro&szlig;er Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen.\r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Geb&auml;ude der Planetenoberfl&auml;che viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Ewigen bereit w&auml;ren eine enorme Menge von Ressourcen und Zeit f&uuml;r eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass ein Planetarer Schild die besten Erfolgsquoten gegen planetengerichtete Projektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterst&uuml;tzt wurde.<br><br>\r\n\r\nDer Planetare Schild verf&uuml;gt &uuml;ber eine Kombination von bodengest&uuml;tzten leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschie&szlig;en, sowie &uuml;ber eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schw&auml;chen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengest&uuml;tzten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden k&ouml;nnen. <br>', 'Planetarer Schild', 'Die planetaren Verteidigungsanlagen führten den Flotten der Angreifer meist unglaubliche Schäden zu, waren jedoch Schiffe in der Lage durch die Ränge der verteidigenden Orbitalflotten und der Geschwader der Atmosphärenjäger zu brechen, konnten sie die Planetenoberfläche mit großer Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen.\r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Gebäude der Planetenoberfläche viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Ewigen bereit wären eine enorme Menge von Ressourcen und Zeit für eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass ein Planetarer Schild die besten Erfolgsquoten gegen planetengerichtete Projektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterstützt wurde.<br><br>\r\n\r\nDer Planetare Schild verfügt über eine Kombination von bodengestützten leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschießen, sowie über eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schwächen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengestützten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden können. <br>', '', ''),
(25, 'Efta-Projekt', 80000, 36000, 15000, 8000, 5, 72, 27900, '4;70;72;73;74', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es m&ouml;glich sein einen Cyborg zum Planeten Efta schicken zu k&ouml;nnen. Aufgrund der widrigen Umst&auml;nde ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit m&ouml;glich.', 'Efta-Projekt', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es möglich sein einen Cyborg zum Planeten Efta schicken zu können. Aufgrund der widrigen Umstände ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit möglich.', '', ''),
(26, 'Unendlichkeitssph&auml;re', 100000, 100000, 100000, 100000, 10, 96, 110000, '4;70', 'Unendlichkeitssph&auml;re', 'Unendlichkeitssphäre', 'Unendlichkeitssphäre', '', ''),
(27, 'Paleniumverst&auml;rker', 25000, 12000, 2000, 2000, 5, 32, 11300, '28', 'Der Paleniumverst&auml;rker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erh&ouml;hen.', 'Paleniumverstärker', 'Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.', '', ''),
(28, 'Artefaktzentrum', 10000, 5000, 25000, 3000, 0, 24, 10700, '4', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschlie&szlig;t man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', 'Artefaktzentrum', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', '', ''),
(29, 'Missionszentrale', 20000, 10000, 50000, 6000, 2, 28, 23400, '4', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', 'Missionszentrale', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', '', ''),
(30, 'Planetare Schilderweiterung', 50000, 80000, 400000, 100000, 25, 128, 206000, '2;24;46', 'Diese Erweiterung sorgt daf&uuml;r, dass die Wirkung von EMP-Waffen auf T&uuml;rme zu einem gewissen Teil absorbiert werden kann.', 'Planetare Schilderweiterung', 'Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.', '', ''),
(40, 'Schutzschild Klasse I', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Klasse I ist f&uuml;r J&auml;ger.', 'Schutzschild Klasse I', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse I ist für Jäger.', '', ''),
(41, 'Schutzschild Klasse II', 8000, 2000, 0, 0, 0, 12, 1200, '40', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Klasse II ist f&uuml;r Jagdboote.', 'Schutzschild Klasse II', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse II ist für Jagdboote.', '', ''),
(42, 'Schutzschild Klasse III', 16000, 4000, 1000, 0, 0, 24, 2700, '41', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Klasse III ist f&uuml;r Zerst&ouml;rer.', 'Schutzschild Klasse III', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse III ist für Zerstörer.', '', ''),
(43, 'Schutzschild Klasse IV', 32000, 8000, 2000, 1000, 0, 48, 5800, '42', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Klasse IV ist f&uuml;r Kreuzer.', 'Schutzschild Klasse IV', 'Schützt Schiffe vor physischen und Energieangriffen. Klasse IV ist für Kreuzer.', '', ''),
(44, 'Multiphasenschilde', 64000, 16000, 4000, 2000, 0, 96, 11600, '43', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Wegen ihrer Gr&ouml;&szlig;e ben&ouml;tigen die Schlachtschiffe diese besonderen Schilde.', 'Multiphasenschilde', 'Schützt Schiffe vor physischen und Energieangriffen. Wegen ihrer Größe benötigen die Schlachtschiffe diese besonderen Schilde.', '', ''),
(45, 'Laser', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Stark geb&uuml;ndelte, koh&auml;rente Lichtstrahlen, die leichten Schaden verursachen.', 'Laser', 'Stark gebündelte, kohärente Lichtstrahlen, die leichten Schaden verursachen.', '', ''),
(46, 'Ionenimpulskanone', 8000, 2000, 0, 0, 0, 12, 1200, '45', 'Mit dieser Waffe ist es dem Jagdboot m&ouml;glich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zuk&auml;mpfen.', 'Ionenimpulskanone', 'Mit dieser Waffe ist es dem Jagdboot möglich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zukämpfen.', '', ''),
(47, 'Autokanone', 16000, 4000, 1000, 0, 0, 24, 2700, '46', 'Ist eine Automatikkanone, die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.', 'Autokanone', 'Ist eine Automatikkanone, die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.', '', ''),
(48, 'Plasmakanone', 32000, 8000, 2000, 1000, 0, 48, 5800, '47', 'Die Plasmakanone feuert einen Plasmaenergiesto&szlig; ab, der gro&szlig;en Schaden anrichtet.', 'Plasmakanone', 'Die Plasmakanone feuert einen Plasmaenergiestoß ab, der großen Schaden anrichtet.', '', ''),
(49, 'Partikelstrahl', 64000, 16000, 4000, 2000, 0, 96, 11600, '48', 'Der Partikelstrahl ist die st&auml;rkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem geb&uuml;ndelten Energiestrahl ab. Wegen ihrer Gr&ouml;&szlig;e sind solche Kanonen nur bei Schlachtschiffen installiert.', 'Partikelstrahl', 'Der Partikelstrahl ist die stärkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem gebündelten Energiestrahl ab. Wegen ihrer Größe sind solche Kanonen nur bei Schlachtschiffen installiert.', '', ''),
(50, 'Raketenantrieb', 6000, 2000, 0, 0, 0, 9, 1000, '0', 'Die Forschung im Bereich des Raketenantriebs schafft die Basis f&uuml;r Raketen, die sich im Weltraum selbstst&auml;ndig bewegen.', 'Raketenantrieb', 'Die Forschung im Bereich des Raketenantriebs schafft die Basis für Raketen, die sich im Weltraum selbstständig bewegen.', '', ''),
(51, 'Fusionsrakete', 4000, 1000, 0, 0, 0, 6, 600, '50', 'Die Fusionsrakete ist die einfachste Rakete, die es gibt. Sie wird bevorzugt in Raketent&uuml;rmen verbaut und dient zur \"Luftabwehr\".', 'Fusionsrakete', 'Die Fusionsrakete ist die einfachste Rakete, die es gibt. Sie wird bevorzugt in Raketentürmen verbaut und dient zur \"Luftabwehr\".', '', ''),
(52, 'Protonentorpedo', 8000, 2000, 0, 0, 0, 12, 1200, '51', 'Ist ein Energiegeschoss, das mit &Uuml;berlichtgeschwindigkeit sein Ziel trifft.', 'Protonentorpedo', 'Ist ein Energiegeschoss, das mit Überlichtgeschwindigkeit sein Ziel trifft.', '', ''),
(53, 'Raumbomben', 16000, 4000, 1000, 0, 0, 24, 2700, '52', 'Raumbomben sind eigentlich dicke, globige Raketen und werden nur wegen ihrem Aussehen als Bomben bezeichnet.', 'Raumbomben', 'Raumbomben sind eigentlich dicke, globige Raketen und werden nur wegen ihrem Aussehen als Bomben bezeichnet.', '', ''),
(54, 'Antimaterietorpedo', 32000, 8000, 2000, 1000, 0, 48, 5800, '53', 'Diese Rakete besteht aus Antimateriekugeln, die beim Aufschlag detonieren.', 'Antimaterietorpedo', 'Diese Rakete besteht aus Antimateriekugeln, die beim Aufschlag detonieren.', '', ''),
(55, 'J&auml;gerchassis', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Jägerchassis', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(56, 'Jagdbootchassis', 8000, 2000, 0, 0, 0, 12, 1200, '55', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Jagdbootchassis', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(57, 'Zerst&ouml;rerchassis', 16000, 4000, 1000, 0, 0, 24, 2700, '56', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Zerstörerchassis', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(58, 'Kreuzerchassis', 32000, 8000, 2000, 1000, 0, 48, 5800, '57', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Kreuzerchassis', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(59, 'Schlachtschiffchassis', 64000, 16000, 4000, 2000, 0, 96, 11600, '58', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Schlachtschiffchassis', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(60, 'Atomantrieb', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Kleiner kompakter Antrieb, genau richtig f&uuml;r J&auml;ger.', 'Atomantrieb', 'Kleiner kompakter Antrieb, genau richtig für Jäger.', '', ''),
(61, 'Fusionsantrieb', 8000, 2000, 0, 0, 0, 12, 1200, '60', 'Der Fusionsantrieb beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.', 'Fusionsantrieb', 'Der Fusionsantrieb beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.', '', ''),
(62, 'Ionenantrieb', 16000, 4000, 1000, 0, 0, 24, 2700, '61', 'Nur dem Ionenantrieb ist es zu verdanken, da&szlig; sich der Zerst&ouml;rer als wendiges Schiff erweist.', 'Ionenantrieb', 'Nur dem Ionenantrieb ist es zu verdanken, daß sich der Zerstörer als wendiges Schiff erweist.', '', ''),
(63, 'Antimaterieantrieb', 32000, 8000, 2000, 1000, 0, 48, 5800, '62', 'Der Kreuzer wird durch einen Antimaterieantrieb bewegt, jedoch ist der Antrieb deutlich schwerf&auml;lliger.', 'Antimaterieantrieb', 'Der Kreuzer wird durch einen Antimaterieantrieb bewegt, jedoch ist der Antrieb deutlich schwerfälliger.', '', ''),
(64, 'Hyperantrieb', 64000, 16000, 4000, 2000, 0, 96, 11600, '63', 'Der Hyperantrieb hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.', 'Hyperantrieb', 'Der Hyperantrieb hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.', '', ''),
(65, 'Transmitterfeld', 8000, 2000, 0, 0, 0, 9, 1200, '0', 'Durch das Transmitterfeld wird ein eroberter Kollektor zu deinem Planeten gebracht.', 'Transmitterfeld', 'Durch das Transmitterfeld wird ein eroberter Kollektor zu deinem Planeten gebracht.', '', ''),
(66, 'Tarnfeld', 10000, 4000, 0, 0, 0, 12, 1800, '9', 'Das Tarnfeld verhindert die Entdeckung der Spionagesonde und Geheimagenten. Jedenfalls meistens!', 'Tarnfeld', 'Das Tarnfeld verhindert die Entdeckung der Spionagesonde und Geheimagenten. Jedenfalls meistens!', '', ''),
(67, 'Gesch&uuml;tzt&uuml;rme', 4000, 1000, 0, 0, 0, 9, 600, '22', 'Der Gesch&uuml;tzturm ist ein Multiplex-Sockel der mit verschiedenen Waffenk&ouml;pfen besetzt werden kann.', 'Geschütztürme', 'Der Geschützturm ist ein Multiplex-Sockel der mit verschiedenen Waffenköpfen besetzt werden kann.', '', ''),
(68, 'J&auml;gerbuchten', 12000, 3000, 1000, 0, 0, 18, 2100, '58', 'Erm&ouml;glicht es 20 J&auml;ger zu transportieren und zu versorgen.', 'Jägerbuchten', 'Ermöglicht es 20 Jäger zu transportieren und zu versorgen.', '', ''),
(69, 'Schwere J&auml;gerbuchten', 24000, 4000, 2000, 2000, 0, 36, 4600, '59;68', 'Erm&ouml;glicht es 50 J&auml;ger zu transportieren und zu \r\nversorgen.', 'Schwere Jägerbuchten', 'Ermöglicht es 50 Jäger zu transportieren und zu \r\nversorgen.', '', ''),
(70, 'Virtuelles Transmitterfeld', 50000, 20000, 6000, 4000, 1, 72, 13400, '65', 'Erm&ouml;glicht Transmitterverbindungen auch au&szlig;erhalb des normalen Transmittersystems, jedoch unter hohem Risiko f&uuml;r den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', 'Virtuelles Transmitterfeld', 'Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', '', ''),
(71, 'Cyborggrundlagen', 20000, 20000, 10000, 8000, 0, 36, 12200, '8', 'Erm&ouml;glicht die Entwicklung von Cyborgtechnologien.', 'Cyborggrundlagen', 'Ermöglicht die Entwicklung von Cyborgtechnologien.', '', ''),
(72, 'Cyborginkubationstank', 10000, 5000, 1000, 2000, 0, 24, 3100, '71', 'Dient zur Z&uuml;chtung des biologischen Cyborggewebes.', 'Cyborginkubationstank', 'Dient zur Züchtung des biologischen Cyborggewebes.', '', ''),
(73, 'Cyborgsteuerungskristall', 5000, 60000, 2000, 2000, 0, 54, 13900, '71', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsf&auml;hig.', 'Cyborgsteuerungskristall', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.', '', ''),
(74, 'Cyborgimplantate', 10000, 6000, 10000, 7000, 0, 36, 8000, '71', 'Sie bilden das Grundger&uuml;st des Cyborgs.', 'Cyborgimplantate', 'Sie bilden das Grundgerüst des Cyborgs.', '', ''),
(75, 'Sprungfeldfrequenzmodulator', 500000, 100000, 50000, 20000, 75, 144, 168000, '5', 'Der Sprungfeldfrequenzmodulator erm&ouml;glicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen au&szlig;er Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.', 'Sprungfeldfrequenzmodulator', 'Der Sprungfeldfrequenzmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.', '', ''),
(80, 'Kollektoren', 1000, 100, 0, 0, 0, 4, 120, '7', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung f&uuml;r Kollektoren f&uuml;hrt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann f&uuml;r den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', 'Kollektoren', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', '', ''),
(81, 'Hornisse', 1000, 250, 0, 0, 0, 8, 150, '13;40;45;55;60', 'Die Hornisse ist der J&auml;ger der Ewigen. Die erste Datierung dieses Schiffes ist nicht mehr aufzufinden, da die Konstruktion aus der Zeit vor dem Zusammenbruch der Zentralregierung der Ewigen stammt. Ausgestattet mit zwei Hochleistungslaser und einer hochwertigen Panzerung dient das Schiff als Allroundeinheit der Ewigen. Auf Grund der Gr&ouml;&szlig;e ist das Schiff mit keinem Sprungantrieb ausger&uuml;stet und sollte daher mittels Kreuzern, Schlachtschiffen oder Tr&auml;gerschiffen zur Schlacht bef&ouml;rdert werden.', 'Hornisse', 'Die Hornisse ist der Jäger der Ewigen. Die erste Datierung dieses Schiffes ist nicht mehr aufzufinden, da die Konstruktion aus der Zeit vor dem Zusammenbruch der Zentralregierung der Ewigen stammt. Ausgestattet mit zwei Hochleistungslaser und einer hochwertigen Panzerung dient das Schiff als Allroundeinheit der Ewigen. Auf Grund der Größe ist das Schiff mit keinem Sprungantrieb ausgerüstet und sollte daher mittels Kreuzern, Schlachtschiffen oder Trägerschiffen zur Schlacht befördert werden.', '', ''),
(82, 'Guillotine', 4000, 1000, 0, 0, 0, 16, 600, '13;41;45;46;56;61', 'Der Name dieser Jagdbootklasse stammt aus den Aufzeichnungen von der Schlacht bei Port Armor, einer Forschungsstation. Die Staffel aus Prototypen dieses Raumschiffes entschied dort den Kampf gegen mehrere angreifende Piratenverb&auml;nde. Das Schiff ist als Abwehrwaffe gegen J&auml;ger konstruiert worden, wurde jedoch inzwischen mehrfach &uuml;berarbeitet.', 'Guillotine', 'Der Name dieser Jagdbootklasse stammt aus den Aufzeichnungen von der Schlacht bei Port Armor, einer Forschungsstation. Die Staffel aus Prototypen dieses Raumschiffes entschied dort den Kampf gegen mehrere angreifende Piratenverbände. Das Schiff ist als Abwehrwaffe gegen Jäger konstruiert worden, wurde jedoch inzwischen mehrfach überarbeitet.', '', ''),
(83, 'Schakal', 15000, 5000, 1000, 0, 0, 32, 2800, '13;42;47;51;57;62', 'Der Schakal ist der momentan h&ouml;chst entwickelte Zerst&ouml;rer der Ewigen und besticht im Kampf durch seine Geschwindigkeit und gro&szlig; ausgelegtes Waffenarsenal, welches ihn gegen mehrere Schiffe kampff&auml;hig macht.', 'Schakal', 'Der Schakal ist der momentan höchst entwickelte Zerstörer der Ewigen und besticht im Kampf durch seine Geschwindigkeit und groß ausgelegtes Waffenarsenal, welches ihn gegen mehrere Schiffe kampffähig macht.', '', ''),
(84, 'Marauder', 30000, 10000, 1000, 1500, 0, 64, 5900, '13;43;48;52;58;63;68', 'Als kleiner Tr&auml;ger und Transportschiffe wurde der Kreuzer in den fr&uuml;hen Gefechten kaum mit Waffen ausgestattet, so dass einzig die J&auml;ger die Raumgefechte entschieden. In weiteren Entwicklungsstufen wurden Panzerung und Waffen aufger&uuml;stet, so dass er inzwischen ein vollwertiges Kampfschiff ist. Noch immer besitzt der Marauder Platz f&uuml;r 20 J&auml;ger, um diese in die Schlacht zu tragen.', 'Marauder', 'Als kleiner Träger und Transportschiffe wurde der Kreuzer in den frühen Gefechten kaum mit Waffen ausgestattet, so dass einzig die Jäger die Raumgefechte entschieden. In weiteren Entwicklungsstufen wurden Panzerung und Waffen aufgerüstet, so dass er inzwischen ein vollwertiges Kampfschiff ist. Noch immer besitzt der Marauder Platz für 20 Jäger, um diese in die Schlacht zu tragen.', '', ''),
(85, 'Zerberus', 50000, 20000, 2000, 4000, 2, 96, 13200, '13;44;49;53;54;59;70;64;69', 'Der Zerberus war das erste Schlachtschiff, welches im Weltall auftauchte. Die massiven Waffen und Panzerungen machen ihn zum Schrecken auf dem Schlachtfeld. Da die Bewaffnung auf den Kampf gegen Gro&szlig;kampfschiffe ausgelegt ist, besitzt er J&auml;gertransportkapazit&auml;ten um ihn gegen Bomber und J&auml;ger zu sch&uuml;tzen. Er kann insgesamt 80 J&auml;ger in den Kampf f&uuml;hren. Ebenso ist das Schlachtschiff mit einem Transmitter f&uuml;r Bergungsoperationen und den besten Offizieren ausgestattet.', 'Zerberus', 'Der Zerberus war das erste Schlachtschiff, welches im Weltall auftauchte. Die massiven Waffen und Panzerungen machen ihn zum Schrecken auf dem Schlachtfeld. Da die Bewaffnung auf den Kampf gegen Großkampfschiffe ausgelegt ist, besitzt er Jägertransportkapazitäten um ihn gegen Bomber und Jäger zu schützen. Er kann insgesamt 80 Jäger in den Kampf führen. Ebenso ist das Schlachtschiff mit einem Transmitter für Bergungsoperationen und den besten Offizieren ausgestattet.', '', ''),
(86, 'Nachtmar', 1500, 500, 0, 0, 0, 10, 250, '13;41;53;56;61', 'Der Nachtmar ist eine weitere Jagdbootklasse, welche jedoch mit schweren Bomben ausgestattet wurde. Im Krieg zwischen den Ewigen und den Ishtar entschied die Entwicklung des Nachtmars die Kapitulation der Ishtar, als deren Verteidigungsanlagen direkt angegriffen werden konnten. Der Nachtmar ist jedoch sehr empfindlich gegen feindliche J&auml;ger und sollte daher von eigenen J&auml;gern gesch&uuml;tzt werden. Wie auch J&auml;ger besitzt er nur einen schwachen Antrieb und muss daher transportiert werden. Dabei verbraucht er doppelt soviel Platz wie ein J&auml;ger.', 'Nachtmar', 'Der Nachtmar ist eine weitere Jagdbootklasse, welche jedoch mit schweren Bomben ausgestattet wurde. Im Krieg zwischen den Ewigen und den Ishtar entschied die Entwicklung des Nachtmars die Kapitulation der Ishtar, als deren Verteidigungsanlagen direkt angegriffen werden konnten. Der Nachtmar ist jedoch sehr empfindlich gegen feindliche Jäger und sollte daher von eigenen Jägern geschützt werden. Wie auch Jäger besitzt er nur einen schwachen Antrieb und muss daher transportiert werden. Dabei verbraucht er doppelt soviel Platz wie ein Jäger.', '', ''),
(87, 'Transmitterschiff', 2000, 1000, 0, 0, 0, 12, 400, '13;23;56;61;65', 'Transmitterschiffe, auch bekannt als Kollektorenpirat. Mit Hilfe dieser Schiffe werden die Kollektoren zum eigenen Planeten gebracht. Die ben&ouml;tigte Energie zum Transfer mittels Transmitterstrahl ist jedoch so hoch, dass das Schiff durch den Transfer zerst&ouml;rt wird. Transmitterschiffe warten meist hinter den eigenen Schlachtlinien, um erst bei einer gewonnen Schlacht in das Zielgebiet einzudringen.', 'Transmitterschiff', 'Eine Neukonstruktion der Ewigen, welche den erhöhten Bedarf an Jägern und Bombern in Raumgefechten decken soll. Insgesamt kann der Hydra 300 Jäger mit in die Schlacht transportieren.', '', ''),
(88, 'Hydra', 50000, 30000, 5000, 5000, 1, 80, 15500, '13;44;45;59;64;69', 'Eine Neukonstruktion der Ewigen, welche den erh&ouml;hten Bedarf an J&auml;gern und Bombern in Raumgefechten decken soll. Insgesamt kann der Hydra 300 J&auml;ger mit in die Schlacht transportieren.', 'Hydra', 'Transmitterschiffe, auch bekannt als Kollektorenpirat. Mit Hilfe dieser Schiffe werden die Kollektoren zum eigenen Planeten gebracht. Die benötigte Energie zum Transfer mittels Transmitterstrahl ist jedoch so hoch, dass das Schiff durch den Transfer zerstört wird. Transmitterschiffe warten meist hinter den eigenen Schlachtlinien, um erst bei einer gewonnen Schlacht in das Zielgebiet einzudringen.', '', ''),
(100, 'J&auml;gergarnison', 10000, 2500, 0, 0, 0, 8, 1500, '13;22', 'Die J&auml;gergarnisionen dienen als St&uuml;tzpunkt f&uuml;r modifizierte Hornissen. Ihr Operationsbereich ist einzig das eigene System.', 'Jägergarnison', 'Die Jägergarnisionen dienen als Stützpunkt für modifizierte Hornissen. Ihr Operationsbereich ist einzig das eigene System.', '', ''),
(101, 'Raketenturm', 800, 550, 0, 0, 0, 20, 190, '22;51;67', 'Der Raketenturm richtet seine Waffensysteme gegen kleinere, jedoch st&auml;rker gepanzerte Ziele.', 'Raketenturm', 'Der Raketenturm richtet seine Waffensysteme gegen kleinere, jedoch stärker gepanzerte Ziele.', '', ''),
(102, 'Laserturm', 250, 500, 0, 0, 0, 10, 125, '22;45;67', 'Der Laserturm ist die prim&auml;re Abwehrwaffe gegen leichte Ziele.', 'Laserturm', 'Der Laserturm ist die primäre Abwehrwaffe gegen leichte Ziele.', '', ''),
(103, 'Autokanonenturm', 2500, 300, 50, 0, 0, 28, 325, '22;47;67', 'Der Autokanonenturm ist die prim&auml;re Waffen gegen leichte bis mittlere Gro&szlig;kampfschiffe.', 'Autokanonenturm', 'Der Autokanonenturm ist die primäre Waffen gegen leichte bis mittlere Großkampfschiffe.', '', ''),
(104, 'Plasmaturm', 2000, 1000, 500, 0, 0, 48, 550, '22;48;67', 'Der Plasmaturm wurde auf Basis der Waffensysteme der Kreuzer entwickelt und vermag vernichtenden Schaden gegen alle Gro&szlig;kampfschiffe ausrichten. F&uuml;r kleinere Ziele ist das Geschoss jedoch zu langsam.', 'Plasmaturm', 'Der Plasmaturm wurde auf Basis der Waffensysteme der Kreuzer entwickelt und vermag vernichtenden Schaden gegen alle Großkampfschiffe ausrichten. Für kleinere Ziele ist das Geschoss jedoch zu langsam.', '', ''),
(110, 'Spionagesonde', 500, 500, 0, 0, 0, 2, 150, '9;13;62;66', 'Sonde:<br><br>\r\n\r\nDie Sonde ist neben dem Spion die 2. M&ouml;glichkeit, um andere Spieler auszukundschaften.<br>\r\n\r\nSie scannt nur oberfl&auml;chlich die Eigenschaften ihres Ziels, genauere Informationen beschafft der Spion wesentlich besser.<br><br>\r\n\r\nDa die Sonde mit enorm hohen Geschwindigkeiten fliegen soll, &uuml;berlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus.\r\nSonden kehren nie zur&uuml;ck.<br><br>\r\n\r\nSonden k&ouml;nnen von den feindlichen Scannern entdeckt werden, abh&auml;ngig vom Typ des Scanners.', 'Spionagesonde', 'Sonde:<br><br>\r\n\r\nDie Sonde ist neben dem Spion die 2. Möglichkeit, um andere Spieler auszukundschaften.<br>\r\n\r\nSie scannt nur oberflächlich die Eigenschaften ihres Ziels, genauere Informationen beschafft der Spion wesentlich besser.<br><br>\r\n\r\nDa die Sonde mit enorm hohen Geschwindigkeiten fliegen soll, überlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus.\r\nSonden kehren nie zurück.<br><br>\r\n\r\nSonden können von den feindlichen Scannern entdeckt werden, abhängig vom Typ des Scanners.', '', ''),
(111, 'Geheimagent', 500, 500, 200, 100, 0, 8, 250, '66', 'Der Spion / Geheimagent:<br><br>\r\n\r\nDer Spion ist eine der wichtigsten Einheiten des Spiels. Mit ihm kann man &uuml;ber jeden beliebigen Spieler so ziemlich alles herausfinden.', 'Geheimagent', 'Der Spion / Geheimagent:<br><br>\r\n\r\nDer Spion ist eine der wichtigsten Einheiten des Spiels. Mit ihm kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.', '', ''),
(120, 'Sektorraumbasis', 7500000, 3750000, 750000, 375000, 250, 192, 2125000, '0', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und Wohn- und Arbeitst&auml;tte f&uuml;r alle Bewohner der Sektors sein kann.\r\n<br>Vornehmlich dient sie als Navigations- und Handelsst&uuml;tzpunkt, kann aber durch Erweiterung auch als Flottenbasis f&uuml;r die Sektorflotte dienen.\r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektorbasis ist aufgrund der unglaublichen Gr&ouml;&szlig;e sehr teuer in der Konstruktion, besonders Tronic wird f&uuml;r eine derartig gro&szlig;e Konstruktion viel ben&ouml;tigt.', 'Sektorraumbasis', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und Wohn- und Arbeitstätte für alle Bewohner der Sektors sein kann.\r\n<br>Vornehmlich dient sie als Navigations- und Handelsstützpunkt, kann aber durch Erweiterung auch als Flottenbasis für die Sektorflotte dienen.\r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektorbasis ist aufgrund der unglaublichen Größe sehr teuer in der Konstruktion, besonders Tronic wird für eine derartig große Konstruktion viel benötigt.', '', ''),
(121, 'Sektorsprungfeldbegrenzer', 1500000, 750000, 375000, 150000, 150, 144, 622500, '120', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', 'Sektorsprungfeldbegrenzer', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', '', ''),
(122, 'Sektorraumwerft', 750000, 750000, 375000, 375000, 60, 144, 547500, '120', 'In der Sektorraumwerft k&ouml;nnen Schiffe f&uuml;r die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', 'Sektorraumwerft', 'In der Sektorraumwerft können Schiffe für die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', '', ''),
(123, 'Sektorhandelszentrum', 150000, 75000, 75000, 150000, 30, 96, 142500, '120', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', 'Sektorhandelszentrum', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', '', ''),
(124, 'Scannerphalanx', 600000, 1500000, 250000, 750000, 110, 96, 845000, '120', 'Die Scannerphalanx erm&ouml;glicht das Scannen von Sektorraumbasen und deren Flottenst&auml;rke.', 'Scannerphalanx', 'Die Scannerphalanx ermöglicht das Scannen von Sektorraumbasen und deren Flottenstärke.', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tech_data2`
--

CREATE TABLE `de_tech_data2` (
  `tech_id` int(11) NOT NULL DEFAULT '0',
  `tech_name` varchar(40) NOT NULL DEFAULT '',
  `restyp01` int(11) NOT NULL DEFAULT '0',
  `restyp02` int(11) NOT NULL DEFAULT '0',
  `restyp03` int(11) NOT NULL DEFAULT '0',
  `restyp04` int(11) NOT NULL DEFAULT '0',
  `restyp05` int(11) NOT NULL DEFAULT '0',
  `tech_ticks` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `tech_vor` varchar(40) NOT NULL DEFAULT '',
  `des` text NOT NULL,
  `tech_name1` varchar(40) NOT NULL DEFAULT '',
  `des1` text NOT NULL,
  `tech_name2` varchar(40) NOT NULL DEFAULT '',
  `des2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_tech_data2`
--

INSERT INTO `de_tech_data2` (`tech_id`, `tech_name`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `tech_ticks`, `score`, `tech_vor`, `des`, `tech_name1`, `des1`, `tech_name2`, `des2`) VALUES
(1, 'Werkstatt', 4000, 1000, 0, 0, 0, 1, 600, '0', 'Die Werkstatt ist eine kleine Geb&auml;udefabrik. Hier werden einfache Geb&auml;ude durch Roboter gefertigt. Dieses Geb&auml;ude kann erweitert werden.', 'Werkstatt', 'Die Werkstatt ist eine kleine Gebäudefabrik. Hier werden einfache Gebäude durch Roboter gefertigt. Dieses Gebäude kann erweitert werden.', '', ''),
(2, 'Erweiterte Werkstatt', 100000, 10000, 8000, 1000, 0, 48, 14800, '1', 'Dies ist die Erweiterung der Werkstatt. Hier lassen sich hochwertigere Geb&auml;ude herstellen.', 'Erweiterte Werkstatt', 'Dies ist die Erweiterung der Werkstatt. Hier lassen sich hochwertigere Gebäude herstellen.', '', ''),
(3, 'Planetarer Markt', 4000, 1000, 0, 0, 0, 6, 600, '65', 'Der Planetare Markt erm&ouml;glicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine W&auml;hrung im Universum unn&ouml;tig ist. Dieses Geb&auml;ude kann erweitert werden.', 'Planetarer Markt', 'Der Planetare Markt ermöglicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine Währung im Universum unnötig ist. Dieses Gebäude kann erweitert werden.', '', ''),
(4, 'Galaktische Handelszunft', 30000, 7500, 0, 0, 0, 18, 4500, '2;3', 'Die Galaktische Handelszunft ist die Erweiterung des Planetaren Marktes und erm&ouml;glicht den Handel auch &uuml;ber die Sektorengrenzen\r\n\r\nhinaus.', 'Galaktische Handelszunft', 'Die Galaktische Handelszunft ist die Erweiterung des Planetaren Marktes und ermöglicht den Handel auch über die Sektorengrenzen\r\n\r\nhinaus.', '', ''),
(5, 'Sprungbegrenzungswall', 220000, 215000, 81000, 41000, 75, 77, 180700, '2;63', 'Der Sprungbegrenzungswall erm&ouml;glicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine gr&ouml;&szlig;ere M&ouml;glichkeit besteht noch Verst&auml;rkung von Verb&uuml;ndeten zu erhalten.', 'Sprungbegrenzungswall', 'Der Sprungbegrenzungswall ermöglicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine größere Möglichkeit besteht noch Verstärkung von Verbündeten zu erhalten.', '', ''),
(6, 'Schrottschmelze', 135000, 90000, 21500, 5250, 0, 105, 40050, '2;65', 'Ist die Schrottschmelze gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Dimensionsfeld eingefangen. Da die gegnerischen Schiffe weiter au&szlig;erhalb dieses Transmitterfeldes liegen, k&ouml;nnen nur die Wracks der verteidigenden Flotten und der eigenen T&uuml;rme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEine Schrottschmelze gewinnt 10% der Ressourcen der zerst&ouml;rten Schiffe zur&uuml;ck!\r\n\r\n\r\n', 'Schrottschmelze', 'Ist die Schrottschmelze gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Dimensionsfeld eingefangen. Da die gegnerischen Schiffe weiter außerhalb dieses Transmitterfeldes liegen, können nur die Wracks der verteidigenden Flotten und der eigenen Türme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEine Schrottschmelze gewinnt 10% der Ressourcen der zerstörten Schiffe zurück!\r\n\r\n\r\n', '', ''),
(7, 'Sonnenschildfabrik', 6000, 1000, 0, 0, 0, 6, 800, '1', 'Die Sonnenschildfabrik stellt die Sonnenschilder zur Energiegewinnung her. Sind die Sonnenschilder fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebracht und auf die Sonne ausgerichtet.', 'Sonnenschildfabrik', 'Die Sonnenschildfabrik stellt die Sonnenschilder zur Energiegewinnung her. Sind die Sonnenschilder fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebracht und auf die Sonne ausgerichtet.', '', ''),
(8, 'Alchemielabor', 12000, 3000, 0, 0, 0, 12, 1800, '1', 'Das Alchemielabor dient zur Erforschung von noch unbekannten Technologien. Diese Technologien erm&ouml;glichen dann den weiteren Bau von Geb&auml;uden, Gesch&uuml;tzt&uuml;rmen und Schiffen.', 'Alchemielabor', 'Das Alchemielabor dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.', '', ''),
(9, 'Spionageabteilung', 16000, 4000, 0, 0, 0, 18, 2400, '1', 'Die Spionageabteilung bietet die M&ouml;glichkeit in geheimen Werkst&auml;tten Spionagedrohnen zu bauen um mehr &uuml;ber den Gegner zu erfahren.', 'Spionageabteilung', 'Die Spionageabteilung bietet die Möglichkeit in geheimen Werkstätten Spionagedrohnen zu bauen um mehr über den Gegner zu erfahren.', '', ''),
(10, 'Weltraumsonar', 4000, 1000, 0, 0, 0, 12, 600, '1', 'Das Weltraumsonar erm&ouml;glicht es ankommende Flotten fr&uuml;hzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit betr&auml;gt die Chance eine Flotte zu erkennen nur 33%! Es k&ouml;nnen aber auch Spionagedrohnen erkannt werden; hier betr&auml;gt die Chance 15 %. Dieses Geb&auml;ude kann erweitert werden!', 'Weltraumsonar', 'Das Weltraumsonar ermöglicht es ankommende Flotten frühzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit beträgt die Chance eine Flotte zu erkennen nur 33%! Es können aber auch Spionagedrohnen erkannt werden; hier beträgt die Chance 15 %. Dieses Gebäude kann erweitert werden!', '', ''),
(11, 'Elektronensonar', 25000, 6000, 1250, 0, 0, 30, 4075, '2;10', 'Diese neue Scannertechnik benutzt einen weitreichenden Elektronenrichtstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance betr&auml;gt hier 66% und f&uuml;r Spionagedrohnen 30 %. Dieses Geb&auml;ude kann erweitert werden!', 'Elektronensonar', 'Diese neue Scannertechnik benutzt einen weitreichenden Elektronenrichtstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance beträgt hier 66% und für Spionagedrohnen 30 %. Dieses Gebäude kann erweitert werden!', '', ''),
(12, 'Photonensonar', 115000, 47500, 4250, 1250, 0, 55, 22775, '11', 'Diese revolution&auml;re Sonartechnik erm&ouml;glicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. F&uuml;r Spionagedrohnen gilt eine Wahrscheinlichkeit von 45 %.', 'Photonensonar', 'Diese revolutionäre Sonartechnik ermöglicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Für Spionagedrohnen gilt eine Wahrscheinlichkeit von 45 %.', '', ''),
(13, 'Raumschmiede', 8000, 1500, 0, 0, 0, 14, 1100, '1', 'Die Raumschmiede dient zur Produktion der modernsten und gr&ouml;&szlig;ten Raumschiffen, die das All je gesehen hat.', 'Raumschmiede', 'Die Raumschmiede dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.', '', ''),
(14, 'Raffinerie M', 9000, 1000, 0, 0, 0, 2, 1100, '1', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Raffinerien so mu&szlig; die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie eine Raffinerie zugef&uuml;hrt wird um so mehr produziert sie von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Raffinerie M', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie eine Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(15, 'Raffinerie D', 11000, 1500, 0, 0, 0, 5, 1400, '14', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Raffinerien so mu&szlig; die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugef&uuml;hrt wird um so mehr produziert sie von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Raffinerie D', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(16, 'Raffinerie I', 14000, 2500, 0, 0, 0, 7, 1900, '15', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Raffinerien so mu&szlig; die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugef&uuml;hrt wird um so mehr produziert sie von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Raffinerie I', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(17, 'Raffinerie E', 16000, 3500, 0, 0, 0, 10, 2300, '16', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Raffinerien so mu&szlig; die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugef&uuml;hrt wird um so mehr produziert sie von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Raffinerie E', 'Die Raffinerien erzeugen aus der Energie der Sonnenschilder die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Raffinerien so muß die Sonnenschildenergie prozentual verteilt werden. Je mehr Energie einer Raffinerie zugeführt wird um so mehr produziert sie von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(18, 'gr. Raffinerie M', 100000, 20000, 5000, 2000, 0, 30, 16300, '2;14', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'gr. Raffinerie M', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(19, 'gr. Raffinerie D', 125000, 27500, 8000, 3000, 0, 37, 21600, '2;15', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'gr. Raffinerie D', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(20, 'gr. Raffinerie I', 140000, 35000, 10000, 3500, 0, 42, 25400, '2;16', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'gr. Raffinerie I', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(21, 'gr. Raffinerie E', 155000, 45000, 12000, 4000, 0, 46, 29700, '2;17', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'gr. Raffinerie E', 'Das sind die Erweiterungen der Raffinerie. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(22, 'Belagerungszentrum', 12000, 3500, 0, 0, 0, 14, 1900, '1', 'Das Belagerungszentrum ist f&uuml;r den Bau und die Verwaltung der planetaren Verteidigungsanlagen zust&auml;ndig.', 'Belagerungszentrum', 'Das Belagerungszentrum ist für den Bau und die Verwaltung der planetaren Verteidigungsanlagen zuständig.', '', ''),
(23, 'Sonnenschildakzeptor', 25000, 20000, 0, 0, 0, 30, 6500, '2;4;65', 'Der Sonnenschildakzeptor ist ein gigantisches orbitales Dimensionstor, der in der Lage ist die Sonnenschilder zu empfangen, die von den Transmitterschiffen in K&auml;mpfen erbeutet und abgestrahlt\r\n\r\nwerden.', 'Sonnenschildakzeptor', 'Der Sonnenschildakzeptor ist ein gigantisches orbitales Dimensionstor, der in der Lage ist die Sonnenschilder zu empfangen, die von den Transmitterschiffen in Kämpfen erbeutet und abgestrahlt\r\n\r\nwerden.', '', ''),
(24, 'Planetarer Schildwall', 135000, 175000, 90000, 72500, 20, 86, 124500, '2;44', 'Die planetaren Verteidigungsanlagen f&uuml;hrten den Flotten der Angreifer meist unglaubliche Sch&auml;den zu, waren jedoch Schiffe in der Lage durch die R&auml;nge der verteidigenden Orbitalflotten und der Geschwader der Atmosph&auml;renj&auml;ger zu brechen, konnten sie die Planetenoberfl&auml;che mit gro&szlig;er Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen. \r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Geb&auml;ude der Planetenoberfl&auml;che viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Ishtar bereit w&auml;ren eine enorme Menge von Ressourcen und Zeit f&uuml;r eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass ein Planetarer Schildwall die besten Erfolgsquoten gegen planetengerichtete\r\n\r\nProjektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterst&uuml;tzt wurde.<br><br>\r\n\r\nDer Planetare Schildwall verf&uuml;gt &uuml;ber eine Kombination von bodengest&uuml;tzten leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschie&szlig;en, sowie &uuml;ber eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schw&auml;chen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengest&uuml;tzten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden k&ouml;nnen. <br>', 'Planetarer Schildwall', 'Die planetaren Verteidigungsanlagen führten den Flotten der Angreifer meist unglaubliche Schäden zu, waren jedoch Schiffe in der Lage durch die Ränge der verteidigenden Orbitalflotten und der Geschwader der Atmosphärenjäger zu brechen, konnten sie die Planetenoberfläche mit großer Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen. \r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Gebäude der Planetenoberfläche viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Ishtar bereit wären eine enorme Menge von Ressourcen und Zeit für eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass ein Planetarer Schildwall die besten Erfolgsquoten gegen planetengerichtete\r\n\r\nProjektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterstützt wurde.<br><br>\r\n\r\nDer Planetare Schildwall verfügt über eine Kombination von bodengestützten leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschießen, sowie über eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schwächen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengestützten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden können. <br>', '', ''),
(25, 'Efta-Projekt', 80000, 36000, 15000, 8000, 5, 72, 27900, '4;70;72;73;74', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es m&ouml;glich sein einen Cyborg zum Planeten Efta schicken zu k&ouml;nnen. Aufgrund der widrigen Umst&auml;nde ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit m&ouml;glich.', 'Efta-Projekt', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es möglich sein einen Cyborg zum Planeten Efta schicken zu können. Aufgrund der widrigen Umstände ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit möglich.', '', ''),
(26, 'Unendlichkeitstor', 110000, 110000, 110000, 107500, 10, 105, 119000, '4;70', 'Unendlichkeitstor', 'Unendlichkeitstor', 'Unendlichkeitstor', '', ''),
(27, 'Paleniumverst&auml;rker', 25000, 12000, 2000, 2000, 5, 32, 11300, '28', 'Der Paleniumverst&auml;rker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erh&ouml;hen.', 'Paleniumverstärker', 'Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.', '', ''),
(28, 'Artefakthort', 12000, 4000, 27000, 2000, 0, 20, 10900, '4', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschlie&szlig;t man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', 'Artefakthort', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', '', ''),
(29, 'Missionshort', 24000, 8000, 54000, 4000, 2, 24, 23800, '4', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', 'Missionshort', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', '', ''),
(30, 'Planetare Schildwallerweiterung', 50000, 60000, 400000, 110000, 25, 128, 206000, '2;24;46', 'Diese Erweiterung sorgt daf&uuml;r, dass die Wirkung von EMP-Waffen auf T&uuml;rme zu einem gewissen Teil absorbiert werden kann.', 'Planetare Schildwallerweiterung', 'Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.', '', ''),
(40, 'Energiebarriere Stufe I', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Stufe I ist f&uuml;r J&auml;ger.', 'Energiebarriere Stufe I', 'Schützt Schiffe vor physischen und Energieangriffen. Stufe I ist für Jäger.', '', ''),
(41, 'Energiebarriere Stufe II', 8000, 2000, 0, 0, 0, 12, 1200, '40', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Stufe II ist f&uuml;r Jagdboote.', 'Energiebarriere Stufe II', 'Schützt Schiffe vor physischen und Energieangriffen. Stufe II ist für Jagdboote.', '', ''),
(42, 'Energiebarriere Stufe III', 16000, 4000, 1000, 0, 0, 24, 2700, '41', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Stufe III ist f&uuml;r Zerst&ouml;rer.', 'Energiebarriere Stufe III', 'Schützt Schiffe vor physischen und Energieangriffen. Stufe III ist für Zerstörer.', '', ''),
(43, 'Energiebarriere Stufe IV', 32000, 8000, 2000, 1000, 0, 48, 5800, '42', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Stufe IV ist f&uuml;r Kreuzer.', 'Energiebarriere Stufe IV', 'Schützt Schiffe vor physischen und Energieangriffen. Stufe IV ist für Kreuzer.', '', ''),
(44, 'Ultrabarriere', 64000, 16000, 4000, 2000, 0, 96, 11600, '43', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Wegen ihrer Gr&ouml;&szlig;e ben&ouml;tigen die Schlachtschiffe diese besonderen Schilde.', 'Ultrabarriere', 'Schützt Schiffe vor physischen und Energieangriffen. Wegen ihrer Größe benötigen die Schlachtschiffe diese besonderen Schilde.', '', ''),
(45, 'Laserlanze', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Stark geb&uuml;ndelte, koh&auml;rente Lichtstrahlen, die leichten Schaden\r\n\r\nverursachen.', 'Laserlanze', 'Stark gebündelte, kohärente Lichtstrahlen, die leichten Schaden\r\n\r\nverursachen.', '', ''),
(46, 'Impulsblaster', 8000, 2000, 0, 0, 0, 12, 1200, '45', 'Mit dieser Waffe ist es m&ouml;glich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zu k&auml;mpfen.', 'Impulsblaster', 'Mit dieser Waffe ist es möglich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zu kämpfen.', '', ''),
(47, 'Bolzenkanone', 14000, 3500, 875, 0, 0, 21, 2363, '46', 'Ist eine Balistikkanone, die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.', 'Bolzenkanone', 'Ist eine Balistikkanone, die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.', '', ''),
(48, 'Plasmalanze', 30000, 7500, 1875, 900, 0, 45, 5423, '47', 'Die Plasmalanze feuert einen Plasmaenergiesto&szlig; ab, der gro&szlig;en Schaden anrichtet.', 'Plasmalanze', 'Die Plasmalanze feuert einen Plasmaenergiestoß ab, der großen Schaden anrichtet.', '', ''),
(49, 'Partikellanze', 64000, 16000, 4000, 2000, 0, 96, 11600, '48', 'Die Partikellanze ist die st&auml;rkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem geb&uuml;ndelten Energiestrahl ab. Wegen ihrer Gr&ouml;&szlig;e sind solche Kanonen nur bei Schlachtschiffen installiert.', 'Partikellanze', 'Die Partikellanze ist die stärkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem gebündelten Energiestrahl ab. Wegen ihrer Größe sind solche Kanonen nur bei Schlachtschiffen installiert.', '', ''),
(50, 'Raketensteuerung', 6000, 2000, 0, 0, 0, 9, 1000, '0', 'Die Forschung im Bereich des Raketensteuerung schafft die Basis f&uuml;r Raketen, die sich im Weltraum selbstst&auml;ndig bewegen.', 'Raketensteuerung', 'Die Forschung im Bereich des Raketensteuerung schafft die Basis für Raketen, die sich im Weltraum selbstständig bewegen.', '', ''),
(51, 'Ballistarakete', 2000, 500, 0, 0, 0, 3, 300, '50', 'Die Ballistarakete ist die einfachste Rakete, die es gibt. Sie wird bevorzugt in Raketent&uuml;rmen verbaut und dient zur \"Luftabwehr\".', 'Ballistarakete', 'Die Ballistarakete ist die einfachste Rakete, die es gibt. Sie wird bevorzugt in Raketentürmen verbaut und dient zur \"Luftabwehr\".', '', ''),
(52, 'Mangenrakete', 8000, 2000, 0, 0, 0, 12, 1200, '51', 'Ist ein Energiegeschoss, das mit &Uuml;berlichtgeschwindigkeit sein Ziel trifft.', 'Mangenrakete', 'Ist ein Energiegeschoss, das mit Überlichtgeschwindigkeit sein Ziel trifft.', '', ''),
(53, 'Astrobomben', 16000, 4000, 1000, 0, 0, 24, 2700, '52', 'Astrobomben sind eigentlich dicke, globige Raketen und werden nur wegen ihrem Aussehen als Bomben bezeichnet.', 'Astrobomben', 'Astrobomben sind eigentlich dicke, globige Raketen und werden nur wegen ihrem Aussehen als Bomben bezeichnet.', '', ''),
(54, 'Rammentorpedo', 32000, 8000, 2000, 1000, 0, 48, 5800, '53', 'Diese Rakete besteht aus hochenergetischen Kugeln, die beim Aufschlag detonieren.', 'Rammentorpedo', 'Diese Rakete besteht aus hochenergetischen Kugeln, die beim Aufschlag detonieren.', '', ''),
(55, 'J&auml;gerstruktur', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Jägerstruktur', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(56, 'Jagdbootstruktur', 8000, 2000, 0, 0, 0, 12, 1200, '55', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Jagdbootstruktur', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(57, 'Zerst&ouml;rerstruktur', 16000, 4000, 1000, 0, 0, 24, 2700, '56', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Zerstörerstruktur', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(58, 'Kreuzerstruktur', 32000, 8000, 2000, 1000, 0, 48, 5800, '57', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Kreuzerstruktur', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(59, 'Schlachtschiffstruktur', 64000, 16000, 4000, 2000, 0, 96, 11600, '58', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Schlachtschiffstruktur', 'Die Struktur ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(60, 'Neutronenantrieb', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Kleiner kompakter Antrieb, genau richtig f&uuml;r J&auml;ger.', 'Neutronenantrieb', 'Kleiner kompakter Antrieb, genau richtig für Jäger.', '', ''),
(61, 'Magmaantrieb', 8000, 2000, 0, 0, 0, 12, 1200, '60', 'Der Magmaantrieb beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.', 'Magmaantrieb', 'Der Magmaantrieb beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.', '', ''),
(62, 'Impulsantrieb', 20000, 5000, 1250, 0, 0, 30, 3375, '61', 'Nur dem Impulsantrieb ist es zu verdanken, da&szlig; sich der Zerst&ouml;rer als wendiges Schiff erweist.', 'Impulsantrieb', 'Nur dem Impulsantrieb ist es zu verdanken, daß sich der Zerstörer als wendiges Schiff erweist.', '', ''),
(63, 'Schwarzlochantrieb', 35000, 8000, 2000, 1100, 0, 52, 6140, '62', 'Der Kreuzer wird durch einen Schwarzlochantrieb bewegt, jedoch ist der Antrieb deutlich schwerf&auml;lliger.', 'Schwarzlochantrieb', 'Der Kreuzer wird durch einen Schwarzlochantrieb bewegt, jedoch ist der Antrieb deutlich schwerfälliger.', '', ''),
(64, 'Hyperfluxantrieb', 64000, 16000, 4000, 2000, 0, 96, 11600, '63', 'Der Hyperfluxantrieb hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.', 'Hyperfluxantrieb', 'Der Hyperfluxantrieb hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.', '', ''),
(65, 'Dimensionsfeld', 4000, 1000, 0, 0, 0, 5, 600, '0', 'Durch das Dimensionsfeld wird ein eroberter Sonnenschild zu deinem Planeten gebracht.', 'Dimensionsfeld', 'Durch das Dimensionsfeld wird ein eroberter Sonnenschild zu deinem Planeten gebracht.', '', ''),
(66, 'Dimensionsverschiebung', 15000, 6000, 0, 0, 0, 18, 2700, '9', 'Die Dimensionsverschiebung verhindert die Entdeckung der Knechte und Spionagedrohnen. Jedenfalls meistens!', 'Dimensionsverschiebung', 'Die Dimensionsverschiebung verhindert die Entdeckung der Knechte und Spionagedrohnen. Jedenfalls meistens!', '', ''),
(67, 'Abwehrturmbasis', 2000, 500, 0, 0, 0, 6, 300, '22', 'Die Abwehrturmbasis ist ein Multiplex-Sockel der mit verschiedenen Waffenk&ouml;pfen besetzt werden kann.', 'Abwehrturmbasis', 'Die Abwehrturmbasis ist ein Multiplex-Sockel der mit verschiedenen Waffenköpfen besetzt werden kann.', '', ''),
(68, 'J&auml;gerrampen', 12000, 3000, 1000, 0, 0, 18, 2100, '58', 'Erm&ouml;glicht es 25 J&auml;ger zu transportieren und zu versorgen.', 'Jägerrampen', 'Ermöglicht es 25 Jäger zu transportieren und zu versorgen.', '', ''),
(69, 'J&auml;gerhangars', 24000, 4000, 2000, 2000, 0, 36, 4600, '59;68', 'Erm&ouml;glicht es 50 J&auml;ger zu transportieren und zu versorgen.', 'Jägerhangars', 'Ermöglicht es 50 Jäger zu transportieren und zu versorgen.', '', ''),
(70, 'Virtuelles Transmitterfeld', 50000, 20000, 6000, 4000, 1, 72, 13400, '65', 'Erm&ouml;glicht Transmitterverbindungen auch au&szlig;erhalb des normalen Transmittersystems, jedoch unter hohem Risiko f&uuml;r den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', 'Virtuelles Transmitterfeld', 'Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', '', ''),
(71, 'Cyborggrundlagen', 20000, 20000, 10000, 8000, 0, 36, 12200, '8', 'Erm&ouml;glicht die Entwicklung von\r\n\r\nCyborgtechnologien.', 'Cyborggrundlagen', 'Ermöglicht die Entwicklung von\r\n\r\nCyborgtechnologien.', '', ''),
(72, 'Cyborginkubationstank', 10000, 5000, 1000, 2000, 0, 24, 3100, '71', 'Dient zur Z&uuml;chtung des biologischen\r\n\r\nCyborggewebes.', 'Cyborginkubationstank', 'Dient zur Züchtung des biologischen\r\n\r\nCyborggewebes.', '', ''),
(73, 'Cyborgsteuerungskristall', 5000, 60000, 2000, 2000, 0, 54, 13900, '71', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsf&auml;hig.', 'Cyborgsteuerungskristall', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.', '', ''),
(74, 'Cyborgimplantate', 10000, 6000, 10000, 7000, 0, 36, 8000, '71', 'Sie bilden das Grundger&uuml;st des Cyborgs.', 'Cyborgimplantate', 'Sie bilden das Grundgerüst des Cyborgs.', '', ''),
(75, 'Sprungfeldfrequenzmodulator', 500000, 100000, 50000, 20000, 75, 144, 168000, '5', 'Der Sprungfeldfrequenzmodulator erm&ouml;glicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen au&szlig;er Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepa&szlig;t.', 'Sprungfeldfrequenzmodulator', 'Der Sprungfeldfrequenzmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepaßt.', '', ''),
(80, 'Sonnenschild', 1000, 100, 0, 0, 0, 4, 120, '7', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung f&uuml;r Kollektoren f&uuml;hrt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann f&uuml;r den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', 'Sonnenschild', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', '', ''),
(81, 'Caesar', 1250, 150, 0, 0, 0, 8, 155, '13;40;45;55;60', 'Der \"Caesar\"-J&auml;ger ist ein kleines wendiges Kampfschiff, welches es zuerst auf die Kollektorschiffklasse und J&auml;ger des Gegners abgesehen hat. Der wendige J&auml;ger ist nur mit einem kleinen Antrieb ausger&uuml;stet und kann weite Strecken nur sehr langsam zur&uuml;cklegen. Daher werden Caesar meist in Verbindung mit Imperator oder Excalibur eingesetzt, die ihnen durch die Technologie der J&auml;gerrampen als Tr&auml;gerschiffe dienen k&ouml;nnen.', 'Caesar', 'Der \"Caesar\"-Jäger ist ein kleines wendiges Kampfschiff, welches es zuerst auf die Kollektorschiffklasse und Jäger des Gegners abgesehen hat. Der wendige Jäger ist nur mit einem kleinen Antrieb ausgerüstet und kann weite Strecken nur sehr langsam zurücklegen. Daher werden Caesar meist in Verbindung mit Imperator oder Excalibur eingesetzt, die ihnen durch die Technologie der Jägerrampen als Trägerschiffe dienen können.', '', ''),
(82, 'Paladin', 3500, 1000, 0, 0, 0, 16, 550, '13;41;45;46;56;61', '(Jagdboot-Klasse) Der Paladin hat als Hauptwaffe Impulsblaster, die gegnerische Schiffe au&szlig;er Gefecht setzen k&ouml;nnen egal wie gro&szlig; das Schiff ist. Je gr&ouml;&szlig;er aber das gegnerische Schiff um so mehr Treffer ben&ouml;tigt aber der Paladin um es lahm zulegen.', 'Paladin', '(Jagdboot-Klasse) Der Paladin hat als Hauptwaffe Impulsblaster, die gegnerische Schiffe außer Gefecht setzen können egal wie groß das Schiff ist. Je größer aber das gegnerische Schiff um so mehr Treffer benötigt aber der Paladin um es lahm zulegen.', '', ''),
(83, 'Vollstrecker', 15500, 4500, 1500, 0, 0, 32, 2900, '13;42;47;51;57;62', '(Zerst&ouml;rer-Klasse) Der Vollstrecker ist ein wendiges Gro&szlig;kampfschiff und am besten geeignet Jagdboote in die ewigen Jagdgr&uuml;nde zu schicken. Aber auch gegen Kreuzer und Schlachtschiffe macht sich der Vollstrecker ganz gut.', 'Vollstrecker', '(Zerstörer-Klasse) Der Vollstrecker ist ein wendiges Großkampfschiff und am besten geeignet Jagdboote in die ewigen Jagdgründe zu schicken. Aber auch gegen Kreuzer und Schlachtschiffe macht sich der Vollstrecker ganz gut.', '', ''),
(84, 'Imperator', 32000, 8000, 1000, 2000, 0, 64, 5900, '13;43;48;52;58;63;68', '(Kreuzer-Klasse) Der Imperator ist der kleine Bruder des Schlachtschiffes. Er ist st&auml;rker bewaffnet als ein Zerst&ouml;rer und konzentriert sich haupts&auml;chlich auf gr&ouml;&szlig;ere Schiffe. Au&szlig;erdem besitzt er eine Ladekapazit&auml;t f&uuml;r 20 J&auml;ger.', 'Imperator', '(Kreuzer-Klasse) Der Imperator ist der kleine Bruder des Schlachtschiffes. Er ist stärker bewaffnet als ein Zerstörer und konzentriert sich hauptsächlich auf größere Schiffe. Außerdem besitzt er eine Ladekapazität für 20 Jäger.', '', ''),
(85, 'Excalibur', 45000, 30000, 2000, 2000, 2, 96, 13900, '13;44;49;53;54;59;70;64;69', '(Schlachtschiff-Klasse) Die Excalibur gilt als K&ouml;nig der Raumschlacht. Mit dem gro&szlig;en Waffenarsenal k&auml;mpft es vor allem gegen seinesgleichen, jedoch sind auch Kreuzer und Zerst&ouml;rer willkommene Opfer. Au&szlig;erdem besitzt es eine Ladekapazit&auml;t f&uuml;r 80 J&auml;ger.', 'Excalibur', '(Schlachtschiff-Klasse) Die Excalibur gilt als König der Raumschlacht. Mit dem großen Waffenarsenal kämpft es vor allem gegen seinesgleichen, jedoch sind auch Kreuzer und Zerstörer willkommene Opfer. Außerdem besitzt es eine Ladekapazität für 80 Jäger.', '', ''),
(86, 'Phalanx', 1200, 400, 0, 0, 0, 10, 200, '13;41;53;56;61', '(Jagdboot-Klasse) Die Phalanx ist eine neuere Entwicklung der Ishtar, um gegen planetare Verteidigung vorzugehen. Sie ist wie alle Bomber mit einer hohen Bombenlast ausgestattet, die sie im Raumkampf beinahe nutzlos macht. Anders aber als andere Bomber sind ihre Bomben nicht mit Sprengk&ouml;pfen beladen, sondern mit EMP K&ouml;pfen, die die Verteidigungsanlagen deaktivieren und Lebewesen unbeeinflusst lassen.', 'Phalanx', '(Jagdboot-Klasse) Die Phalanx ist eine neuere Entwicklung der Ishtar, um gegen planetare Verteidigung vorzugehen. Sie ist wie alle Bomber mit einer hohen Bombenlast ausgestattet, die sie im Raumkampf beinahe nutzlos macht. Anders aber als andere Bomber sind ihre Bomben nicht mit Sprengköpfen beladen, sondern mit EMP Köpfen, die die Verteidigungsanlagen deaktivieren und Lebewesen unbeeinflusst lassen.', '', ''),
(87, 'Merlin', 2000, 1000, 0, 0, 0, 12, 400, '13;23;56;61;65', '(Transmitterschiffe-klasse) Merline, auch als Sonnenschildpirat bekannt. Mit Hilfe dieser Schiffe werden die Sonnenschilder zu dem eigenen Planeten gebracht. Die ben&ouml;tigte Energie zum Transfer mittels Dimensionsfeld ist jedoch so hoch, da&szlig; das Schiff durch den Transfer zerst&ouml;rt wird.', 'Merlin', '(Transmitterschiffe-klasse) Merline, auch als Sonnenschildpirat bekannt. Mit Hilfe dieser Schiffe werden die Sonnenschilder zu dem eigenen Planeten gebracht. Die benötigte Energie zum Transfer mittels Dimensionsfeld ist jedoch so hoch, daß das Schiff durch den Transfer zerstört wird.', '', ''),
(88, 'Colossus', 55000, 25000, 7000, 5000, 2, 80, 16600, '13;44;45;59;64;69', '(Schlachtschiff-Klasse) Der Colossus ist ein Tr&auml;gerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um gro&szlig;e Mengen J&auml;ger transportieren zu k&ouml;nnen wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste daf&uuml;r die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen J&auml;gern wieder wettmachen. Die Ladekapazit&auml;t betr&auml;gt 150 J&auml;ger', 'Colossus', '(Schlachtschiff-Klasse) Der Colossus ist ein Trägerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um große Mengen Jäger transportieren zu können wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste dafür die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen Jägern wieder wettmachen. Die Ladekapazität beträgt 150 Jäger', '', ''),
(100, 'Brechergarnison', 12000, 1500, 0, 0, 0, 8, 1500, '13;22', 'Die Brechergarnison beherbergt mehrere J&auml;ger. Jedoch sind diese J&auml;ger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden k&ouml;nnen.', 'Brechergarnison', 'Die Brechergarnison beherbergt mehrere Jäger. Jedoch sind diese Jäger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden können.', '', ''),
(101, 'Balistenturm', 450, 450, 0, 0, 0, 16, 135, '22;51;67', 'Der Balistenturm ist mit mehreren Ballistarakete best&uuml;ckt und dient zur Abwehr von Jagdbooten und Transmitterschiffen.', 'Balistenturm', 'Der Balistenturm ist mit mehreren Ballistarakete bestückt und dient zur Abwehr von Jagdbooten und Transmitterschiffen.', '', ''),
(102, 'Laserlanzenturm', 250, 250, 0, 0, 0, 8, 75, '22;45;67', 'Der Laserlanzenturm ist der leichteste Gesch&uuml;tzturm.', 'Laserlanzenturm', 'Der Laserlanzenturm ist der leichteste Geschützturm.', '', ''),
(103, 'Bolzenkanonenturm', 2500, 50, 0, 0, 0, 20, 260, '22;47;67', 'Diese gro&szlig;kalibrige Kanone ist der wahre Panzerbrecher und vernichtet Zerst&ouml;rer und Kreuzer massenweise.', 'Bolzenkanonenturm', 'Diese großkalibrige Kanone ist der wahre Panzerbrecher und vernichtet Zerstörer und Kreuzer massenweise.', '', ''),
(104, 'Plasmalanzenturm', 1500, 1000, 250, 250, 0, 40, 525, '22;48;67', 'Der Plasmalanzenturm ist der Turm der jedem Schlachtschiff Respekt abverlangt und f&uuml;r klare Verh&auml;ltnisse sorgt.', 'Plasmalanzenturm', 'Der Plasmalanzenturm ist der Turm der jedem Schlachtschiff Respekt abverlangt und für klare Verhältnisse sorgt.', '', ''),
(110, 'Knecht', 500, 500, 0, 0, 0, 2, 150, '9;13;62;66', 'Knecht / Sonde:<br><br>\r\n\r\nDer Knecht ist neben dem Knappen die 2. M&ouml;glichkeit, um andere Spieler auszukundschaften.<br>\r\n\r\nSie scannt nur oberfl&auml;chlich die Eigenschaften ihres Ziels, genauere Informationen beschafft der Knappe wesentlich besser.<br><br>\r\n\r\nDa die Sonde mit enorm hohen Geschwindigkeiten fliegen soll, &uuml;berlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus. \r\nSonden kehren nie zur&uuml;ck.<br><br>\r\n\r\nSonden k&ouml;nnen von den feindlichen Scannern entdeckt werden, abh&auml;ngig vom Typ des Scanners.', 'Knecht', 'Knecht / Sonde:<br><br>\r\n\r\nDer Knecht ist neben dem Knappen die 2. Möglichkeit, um andere Spieler auszukundschaften.<br>\r\n\r\nSie scannt nur oberflächlich die Eigenschaften ihres Ziels, genauere Informationen beschafft der Knappe wesentlich besser.<br><br>\r\n\r\nDa die Sonde mit enorm hohen Geschwindigkeiten fliegen soll, überlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus. \r\nSonden kehren nie zurück.<br><br>\r\n\r\nSonden können von den feindlichen Scannern entdeckt werden, abhängig vom Typ des Scanners.', '', ''),
(111, 'Knappe', 500, 500, 200, 100, 0, 8, 250, '66', 'Der Knappe / Geheimagent:<br><br>\r\n\r\nDer Knappe ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihm kann man &uuml;ber jeden beliebigen Spieler so ziemlich alles herausfinden.', 'Knappe', 'Der Knappe / Geheimagent:<br><br>\r\n\r\nDer Knappe ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihm kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.', '', ''),
(120, 'Sektorraumbasis', 7500000, 3750000, 750000, 375000, 250, 192, 2125000, '0', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und\r\n\r\nWohn- und Arbeitst&auml;tte f&uuml;r alle Bewohner der Sektors sein kann. \r\n<br>Vornehmlich dient sie als Navigations- und Handelsst&uuml;tzpunkt, kann aber durch Erweiterung auch als Flottenbasis f&uuml;r die Sektorflotte dienen. \r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektobasis ist aufgrund der unglaublichen Gr&ouml;&szlig;e sehr teuer in der Konstruktion, besonders Tronic wird f&uuml;r eine derartig gro&szlig;e Konstruktion viel ben&ouml;tigt.', 'Sektorraumbasis', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und\r\n\r\nWohn- und Arbeitstätte für alle Bewohner der Sektors sein kann. \r\n<br>Vornehmlich dient sie als Navigations- und Handelsstützpunkt, kann aber durch Erweiterung auch als Flottenbasis für die Sektorflotte dienen. \r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektobasis ist aufgrund der unglaublichen Größe sehr teuer in der Konstruktion, besonders Tronic wird für eine derartig große Konstruktion viel benötigt.', '', ''),
(121, 'Sektorsprungfeldbegrenzer', 1500000, 750000, 375000, 150000, 150, 144, 622500, '120', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', 'Sektorsprungfeldbegrenzer', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', '', ''),
(122, 'Sektorraumwerft', 750000, 750000, 375000, 375000, 60, 144, 547500, '120', 'In der Sektorraumwerft k&ouml;nnen Schiffe f&uuml;r die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur\r\n\r\nSektorbasis.', 'Sektorraumwerft', 'In der Sektorraumwerft können Schiffe für die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur\r\n\r\nSektorbasis.', '', ''),
(123, 'Sektorhandelszentrum', 150000, 75000, 75000, 150000, 30, 96, 142500, '120', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', 'Sektorhandelszentrum', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', '', ''),
(124, 'Scannerphalanx', 600000, 1500000, 250000, 750000, 110, 96, 845000, '120', 'Die Scannerphalanx erm&ouml;glicht das Scannen von Sektorraumbasen und deren Flottenst&auml;rke.', 'Scannerphalanx', 'Die Scannerphalanx ermöglicht das Scannen von Sektorraumbasen und deren Flottenstärke.', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tech_data3`
--

CREATE TABLE `de_tech_data3` (
  `tech_id` int(11) NOT NULL DEFAULT '0',
  `tech_name` varchar(40) NOT NULL DEFAULT '',
  `restyp01` int(11) NOT NULL DEFAULT '0',
  `restyp02` int(11) NOT NULL DEFAULT '0',
  `restyp03` int(11) NOT NULL DEFAULT '0',
  `restyp04` int(11) NOT NULL DEFAULT '0',
  `restyp05` int(11) NOT NULL DEFAULT '0',
  `tech_ticks` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `tech_vor` varchar(40) NOT NULL DEFAULT '',
  `des` text NOT NULL,
  `tech_name1` varchar(40) NOT NULL DEFAULT '',
  `des1` text NOT NULL,
  `tech_name2` varchar(40) NOT NULL DEFAULT '',
  `des2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_tech_data3`
--

INSERT INTO `de_tech_data3` (`tech_id`, `tech_name`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `tech_ticks`, `score`, `tech_vor`, `des`, `tech_name1`, `des1`, `tech_name2`, `des2`) VALUES
(1, 'Zentralbau', 4000, 1000, 0, 0, 0, 1, 600, '0', 'Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gez&uuml;chtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n', 'Zentralbau', 'Im Mittelpunkt jeder K’tharr Kolonie steht der Zentralbau. In ihm werden nicht nur neue K’tharr gezüchtet, mit Hilfe von Arbeitsdrohnen werden in ihm auch noch Kammern angelegt und ausgebaut.\r\n', '', ''),
(2, 'Gro&szlig;er Zentralbau', 90000, 9000, 7000, 900, 0, 43, 13260, '1', 'Je gr&ouml;&szlig;er eine Kolonie wird, desto h&ouml;her ist auch ihr Platzanspruch. Durch die Vergr&ouml;&szlig;erung des Zentralbaus wird gen&uuml;gend Platz geschaffen, um weitere gr&ouml;&szlig;ere und aufwendigere Kammern anzulegen.\r\n', 'Großer Zentralbau', 'Je größer eine Kolonie wird, desto höher ist auch ihr Platzanspruch. Durch die Vergrößerung des Zentralbaus wird genügend Platz geschaffen, um weitere größere und aufwendigere Kammern anzulegen.\r\n', '', ''),
(3, 'Platz des Sektortausches', 8000, 2000, 0, 0, 0, 12, 1200, '65', 'Auch wenn die K’tharr im Grunde alle anderen Spezies hassen und ihr einziger Instinkt die Vernichtung und Zerst&ouml;rung ist, haben sie doch gelernt, dass man Ressourcen auch durch Handel erhalten kann. Mit diesem Platz erh&auml;lt eine Kolonie die M&ouml;glichkeit, mit anderen Mitgliedern ihres Sektors Tauschgesch&auml;fte durchzuf&uuml;hren. \r\n', 'Platz des Sektortausches', 'Auch wenn die K’tharr im Grunde alle anderen Spezies hassen und ihr einziger Instinkt die Vernichtung und Zerstörung ist, haben sie doch gelernt, dass man Ressourcen auch durch Handel erhalten kann. Mit diesem Platz erhält eine Kolonie die Möglichkeit, mit anderen Mitgliedern ihres Sektors Tauschgeschäfte durchzuführen. \r\n', '', ''),
(4, 'Platz des Raumtausches', 35000, 9000, 0, 0, 0, 21, 5300, '2;3', 'Mit dem Platz des Raumtausches erh&auml;lt eine Kolonie nicht nur die M&ouml;glichkeit, mit dem gesamten Universum zu handeln. Sie bietet damit auch anderen V&ouml;lkern die M&ouml;glichkeit, die Kolonie f&ouml;rmlich mit Agenten zu &uuml;berschwemmen.\r\n', 'Platz des Raumtausches', 'Mit dem Platz des Raumtausches erhält eine Kolonie nicht nur die Möglichkeit, mit dem gesamten Universum zu handeln. Sie bietet damit auch anderen Völkern die Möglichkeit, die Kolonie förmlich mit Agenten zu überschwemmen.\r\n', '', ''),
(5, 'Bau des Blockens', 200000, 200000, 80000, 40000, 75, 72, 175000, '2;63', 'Der Bau des Blockens legt ein gewaltiges Kraftfeld um den Planeten. Diese arbeitet mit einer Technologie, die den Teleportorganen der Netzf&auml;nger sehr &auml;hnlich ist, jedoch in die entgegenbesetze Richtung wirkt. Sie bremst gegnerische Flotten so aus, dass diese einen vollen Kampftick sp&auml;ter eintreffen, l&auml;sst verb&uuml;ndete Flotten jedoch mit normaler Geschwindigkeit passieren.\r\n', 'Bau des Blockens', 'Der Bau des Blockens legt ein gewaltiges Kraftfeld um den Planeten. Diese arbeitet mit einer Technologie, die den Teleportorganen der Netzfänger sehr ähnlich ist, jedoch in die entgegenbesetze Richtung wirkt. Sie bremst gegnerische Flotten so aus, dass diese einen vollen Kampftick später eintreffen, lässt verbündete Flotten jedoch mit normaler Geschwindigkeit passieren.\r\n', '', ''),
(6, 'Bau der Verwertung', 112500, 75000, 17500, 4250, 0, 90, 33200, '2;65', 'Der Bau der Verwertung beherbergt kleine Bioschiffe, die den Weltraum nach &Uuml;berresten von zerst&ouml;rten K’tharr Schiffen absuchen. Sie fressen deren &Uuml;berreste, um selber im Bau der Verwertung verdaut zu werden. Leider verschm&auml;hen diese parasit&auml;ren Schiffe jegliches feindliche Schiff und es war den Forschern der K’tharr bislang nicht m&ouml;glich eine Ursache hierf&uuml;r zu finden. Es ist dem Bau der Verwertung m&ouml;glich, ca. 10 % der Ressourcen aller zerst&ouml;rten verb&uuml;ndeten Schiffe und eigenen T&uuml;rme zu recyceln.\r\n', 'Bau der Verwertung', 'Der Bau der Verwertung beherbergt kleine Bioschiffe, die den Weltraum nach Überresten von zerstörten K’tharr Schiffen absuchen. Sie fressen deren Überreste, um selber im Bau der Verwertung verdaut zu werden. Leider verschmähen diese parasitären Schiffe jegliches feindliche Schiff und es war den Forschern der K’tharr bislang nicht möglich eine Ursache hierfür zu finden. Es ist dem Bau der Verwertung möglich, ca. 10 % der Ressourcen aller zerstörten verbündeten Schiffe und eigenen Türme zu recyceln.\r\n', '', ''),
(7, 'Zentrum der Wandler', 6000, 1000, 0, 0, 0, 6, 800, '1', 'Kollektoren sind das Herzst&uuml;ck einer jeden Wirtschaft. Das Zentrum der Wandler hat sich deshalb auf deren Produktion spezialisiert.', 'Zentrum der Wandler', 'Kollektoren sind das Herzstück einer jeden Wirtschaft. Das Zentrum der Wandler hat sich deshalb auf deren Produktion spezialisiert.', '', ''),
(8, 'Kammer der Evolution', 14000, 3500, 0, 0, 0, 16, 2100, '1', 'Wie fortschrittlich die K’tharr auch sein m&ouml;gen, ein Stillstand in der Evolution w&uuml;rde ihr Tod bedeuten. Deshalb arbeiten die Kl&uuml;gsten von ihnen in der Kammer der Evolution st&auml;ndig an der Verbesserung von Einheiten und Geb&auml;uden.', 'Kammer der Evolution', 'Wie fortschrittlich die K’tharr auch sein mögen, ein Stillstand in der Evolution würde ihr Tod bedeuten. Deshalb arbeiten die Klügsten von ihnen in der Kammer der Evolution ständig an der Verbesserung von Einheiten und Gebäuden.', '', ''),
(9, 'Zentrum der Unterwanderung', 18000, 4000, 0, 0, 0, 20, 2600, '1', 'Obwohl die Spionage nicht ihre St&auml;rke ist, so haben die K’tharr doch gelernt, dass Agenten trotzdem notwendig sind, um sich vor feindlichen Spionageangriffen zu sch&uuml;tzen. Ein Zentrum der Unterwanderung erm&ouml;glicht die Ausbildung eigener Agenten, sowie den Bau von Spionagesonden.\r\n', 'Zentrum der Unterwanderung', 'Obwohl die Spionage nicht ihre Stärke ist, so haben die K’tharr doch gelernt, dass Agenten trotzdem notwendig sind, um sich vor feindlichen Spionageangriffen zu schützen. Ein Zentrum der Unterwanderung ermöglicht die Ausbildung eigener Agenten, sowie den Bau von Spionagesonden.\r\n', '', ''),
(10, 'Kammer des Raumblickes', 4000, 1000, 0, 0, 0, 12, 600, '1', 'Jeder K’tharr-Bau sollte diese Kammer besitzen, da sie die Erforschung des Weltalls erm&ouml;glicht. Leider ist der einfache Raumblick sehr ungenau, weshalb angreifende Flotten nur mit einer Wahrscheinlichkeit von 33% entdeckt werden. Auch die Entdeckung von Sonden ist m&ouml;glich, jedoch nur in 15% aller F&auml;lle.', 'Kammer des Raumblickes', 'Jeder K’tharr-Bau sollte diese Kammer besitzen, da sie die Erforschung des Weltalls ermöglicht. Leider ist der einfache Raumblick sehr ungenau, weshalb angreifende Flotten nur mit einer Wahrscheinlichkeit von 33% entdeckt werden. Auch die Entdeckung von Sonden ist möglich, jedoch nur in 15% aller Fälle.', '', ''),
(11, 'Erweiterung des Raumblickes', 20000, 5000, 1000, 0, 0, 24, 3300, '2;10', 'Der Raumblick an sich ist sehr ungenau. Durch eine Verbesserung seiner optischen Organe kann er effizienter gemacht werden, so dass er nun in 66% aller F&auml;lle eine anfliegende Flotte entdeckt. Sonden werden nun in 30 % aller F&auml;lle entdeckt.', 'Erweiterung des Raumblickes', 'Der Raumblick an sich ist sehr ungenau. Durch eine Verbesserung seiner optischen Organe kann er effizienter gemacht werden, so dass er nun in 66% aller Fälle eine anfliegende Flotte entdeckt. Sonden werden nun in 30 % aller Fälle entdeckt.', '', ''),
(12, 'Kammer des Tiefraumblickes', 110000, 42500, 4000, 1100, 0, 53, 21140, '11', 'Die besten Biologen der K’Tharr haben optische Organe gez&uuml;chtet, die in der Lage sind, jede sich n&auml;hernde Flotte zu entdecken. Leider haben sie nach wie vor Probleme, die Schattenfelder von Sonden zu entdecken, so dass diese zwar mit einer Wahrscheinlichkeit von 45% entdeckt werden k&ouml;nnen, jedoch immer noch die M&ouml;glichkeit besteht, sie nicht zu entdecken.', 'Kammer des Tiefraumblickes', 'Die besten Biologen der K’Tharr haben optische Organe gezüchtet, die in der Lage sind, jede sich nähernde Flotte zu entdecken. Leider haben sie nach wie vor Probleme, die Schattenfelder von Sonden zu entdecken, so dass diese zwar mit einer Wahrscheinlichkeit von 45% entdeckt werden können, jedoch immer noch die Möglichkeit besteht, sie nicht zu entdecken.', '', ''),
(13, 'Schwarmstock', 8000, 1500, 0, 0, 0, 14, 1100, '1', 'Der Schwarmstock dient zur Produktion der modernsten und gr&ouml;&szlig;ten Raumschiffen, die das All je gesehen hat.', 'Schwarmstock', 'Der Schwarmstock dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.', '', ''),
(14, 'Wandlerkammer M', 9500, 1000, 0, 0, 0, 3, 1150, '1', 'Diese Wandlerkammer erm&ouml;glicht die Umwandlung von Energie in Multiplex. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Multiplex nur in einem Verh&auml;ltnis von 2:1 produziert wird.\r\n', 'Wandlerkammer M', 'Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Multiplex. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Multiplex nur in einem Verhältnis von 2:1 produziert wird.\r\n', '', ''),
(15, 'Wandlerkammer D', 12000, 2000, 0, 0, 0, 6, 1600, '14', 'Diese Wandlerkammer erm&ouml;glicht die Umwandlung von Energie in Dhyarra. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Dhyarra nur in einem Verh&auml;ltnis von 4:1 produziert wird.', 'Wandlerkammer D', 'Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Dhyarra. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Dhyarra nur in einem Verhältnis von 4:1 produziert wird.', '', ''),
(16, 'Wandlerkammer I', 14000, 2500, 0, 0, 0, 8, 1900, '15', 'Diese Wandlerkammer erm&ouml;glicht die Umwandlung von Energie in Iradium. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Iradium nur in einem Verh&auml;ltnis von 6:1 produziert wird.', 'Wandlerkammer I', 'Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Iradium. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Iradium nur in einem Verhältnis von 6:1 produziert wird.', '', ''),
(17, 'Wandlerkammer E', 16000, 3500, 0, 0, 0, 10, 2300, '16', 'Diese Wandlerkammer erm&ouml;glicht die Umwandlung von Energie in Ethernium. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Eternium nur in einem Verh&auml;ltnis von 8:1 produziert wird.', 'Wandlerkammer E', 'Diese Wandlerkammer ermöglicht die Umwandlung von Energie in Ethernium. Die Effizienz ist allerdings nicht sehr hoch, weshalb das Eternium nur in einem Verhältnis von 8:1 produziert wird.', '', ''),
(18, 'Gro&szlig;e Wandlerkammer M', 105000, 21000, 5000, 2100, 0, 31, 17040, '2;14', 'Der Ausbau der Wandlerkammer M erh&ouml;ht die Umwandlungseffizienz auf ein Verh&auml;ltnis von 1:1.\r\n', 'Große Wandlerkammer M', 'Der Ausbau der Wandlerkammer M erhöht die Umwandlungseffizienz auf ein Verhältnis von 1:1.\r\n', '', ''),
(19, 'Gro&szlig;e Wandlerkammer D', 120000, 27500, 7000, 2900, 0, 36, 20760, '2;15', 'Der Ausbau der Wandlerkammer D erh&ouml;ht die Umwandlungseffizienz auf ein Verh&auml;ltnis von 2:1.\r\n\r\n', 'Große Wandlerkammer D', 'Der Ausbau der Wandlerkammer D erhöht die Umwandlungseffizienz auf ein Verhältnis von 2:1.\r\n\r\n', '', ''),
(20, 'Gro&szlig;e Wandlerkammer I', 150000, 40000, 10500, 3750, 0, 44, 27650, '2;16', 'Der Ausbau der Wandlerkammer I erh&ouml;ht die Umwandlungseffizienz auf ein Verh&auml;ltnis von 3:1.\r\n', 'Große Wandlerkammer I', 'Der Ausbau der Wandlerkammer I erhöht die Umwandlungseffizienz auf ein Verhältnis von 3:1.\r\n', '', ''),
(21, 'Gro&szlig;e Wandlerkammer E', 175000, 50000, 14000, 5000, 0, 50, 33700, '2;17', 'Der Ausbau der Wandlerkammer E erh&ouml;ht die Umwandlungseffizienz auf ein Verh&auml;ltnis von 4:1.\r\n', 'Große Wandlerkammer E', 'Der Ausbau der Wandlerkammer E erhöht die Umwandlungseffizienz auf ein Verhältnis von 4:1.\r\n', '', ''),
(22, 'Bau des Schutzes', 25000, 6000, 0, 0, 0, 28, 3700, '1', 'Der Schutz einer jeden Kolonie hat absoluten Vorrang, doch leider k&ouml;nnen nicht immer Schiffe bereitstehen. Verteidigungswaffen k&ouml;nnen diesen Nachteil ausgleichen. Um diese errichten und auch kontrollieren zu k&ouml;nnen, ist der Bau des Schutzes unumg&auml;nglich.\r\n', 'Bau des Schutzes', 'Der Schutz einer jeden Kolonie hat absoluten Vorrang, doch leider können nicht immer Schiffe bereitstehen. Verteidigungswaffen können diesen Nachteil ausgleichen. Um diese errichten und auch kontrollieren zu können, ist der Bau des Schutzes unumgänglich.\r\n', '', ''),
(23, 'Zellenempf&auml;nger', 20000, 20000, 0, 0, 0, 16, 6000, '2;4;65', 'Wenn sich Netzf&auml;nger selbst zerst&ouml;ren, um Kollektoren zum Heimatplaneten zu schicken, bildet der Zellenempf&auml;nger das Gegenportal, um die Kollektoren wieder zu rematerialisieren. Ohne Zellenempf&auml;nger sind Netzf&auml;nger wirkungslos.', 'Zellenempfänger', 'Wenn sich Netzfänger selbst zerstören, um Kollektoren zum Heimatplaneten zu schicken, bildet der Zellenempfänger das Gegenportal, um die Kollektoren wieder zu rematerialisieren. Ohne Zellenempfänger sind Netzfänger wirkungslos.', '', ''),
(24, 'Planetarer Schattenpanzer', 160000, 210000, 105000, 81000, 20, 102, 141900, '2;44', 'Der planetare Schattenpanzer bildet den Versuch, die schwachen K’tharr Verteidigungsanlagen einer Kolonie zu verst&auml;rken. Hierbei handelt es sich weniger um ein Schutzschild, als vielmehr um ein komplexes Netzwerk aus inaktiven Dr&uuml;sen, die den vorhandenen Dr&uuml;sen zum Verwechseln &auml;hnlich sehen und aus allen m&ouml;glichen T&auml;uschk&ouml;rpern und Gegenma&szlig;nahmen, die einen Teil des gegnerischen Feuers auf sich ziehen. Im Endeffekt erh&ouml;ht der planetare Schild damit die Anzahl der eigenen Dr&uuml;sen um 10%, bietet jedoch keinerlei offensive M&ouml;glichkeiten.\r\n', 'Planetarer Schattenpanzer', 'Der planetare Schattenpanzer bildet den Versuch, die schwachen K’tharr Verteidigungsanlagen einer Kolonie zu verstärken. Hierbei handelt es sich weniger um ein Schutzschild, als vielmehr um ein komplexes Netzwerk aus inaktiven Drüsen, die den vorhandenen Drüsen zum Verwechseln ähnlich sehen und aus allen möglichen Täuschkörpern und Gegenmaßnahmen, die einen Teil des gegnerischen Feuers auf sich ziehen. Im Endeffekt erhöht der planetare Schild damit die Anzahl der eigenen Drüsen um 10%, bietet jedoch keinerlei offensive Möglichkeiten.\r\n', '', ''),
(25, 'Efta-Projekt', 80000, 36000, 15000, 8000, 5, 72, 27900, '4;70;72;73;74', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es m&ouml;glich sein einen Cyborg zum Planeten Efta schicken zu k&ouml;nnen. Aufgrund der widrigen Umst&auml;nde ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit m&ouml;glich.', 'Efta-Projekt', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es möglich sein einen Cyborg zum Planeten Efta schicken zu können. Aufgrund der widrigen Umstände ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit möglich.', '', ''),
(26, 'Unendlichkeitskammer', 100000, 100000, 100000, 100000, 10, 96, 110000, '4;70', 'Unendlichkeitskammer', 'Unendlichkeitskammer', 'Unendlichkeitskammer', '', ''),
(27, 'Paleniumverst&auml;rker', 25000, 12000, 2000, 2000, 5, 32, 11300, '28', 'Der Paleniumverst&auml;rker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erh&ouml;hen.', 'Paleniumverstärker', 'Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.', '', ''),
(28, 'Artefaktbau', 14000, 2500, 21000, 5000, 0, 24, 10200, '4', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschlie&szlig;t man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', 'Artefaktbau', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', '', ''),
(29, 'Missionsbau', 28000, 5000, 42000, 10000, 2, 28, 22400, '4', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', 'Missionsbau', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', '', ''),
(30, 'Planetare Schattenpanzererweiterung', 50000, 105000, 350000, 125000, 25, 128, 206000, '2;24;46', 'Diese Erweiterung sorgt daf&uuml;r, dass die Wirkung von EMP-Waffen auf T&uuml;rme zu einem gewissen Teil absorbiert werden kann.', 'Planetare Schattenpanzererweiterung', 'Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.', '', ''),
(40, 'Panzerungs-Evolution I', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Die widerstandsf&auml;higen und trotzdem leichten H&uuml;llen der K’tharr Raumschiffe werden nicht produziert, sondern gez&uuml;chtet. Die kleinsten und leichtesten Panzer eignen sich besonders gut f&uuml;r Spider.', 'Panzerungs-Evolution I', 'Die widerstandsfähigen und trotzdem leichten Hüllen der K’tharr Raumschiffe werden nicht produziert, sondern gezüchtet. Die kleinsten und leichtesten Panzer eignen sich besonders gut für Spider.', '', ''),
(41, 'Panzerungs-Evolution II', 8000, 2000, 0, 0, 0, 12, 1200, '40', 'Je st&auml;rker die Waffen der K’tharr sind, desto h&auml;rter muss auch die Panzerung sein. Eine Verbesserung wurde erstmals mit Z&uuml;chtung der Arctic Spider notwendig.', 'Panzerungs-Evolution II', 'Je stärker die Waffen der K’tharr sind, desto härter muss auch die Panzerung sein. Eine Verbesserung wurde erstmals mit Züchtung der Arctic Spider notwendig.', '', ''),
(42, 'Panzerungs-Evolution III', 16000, 4000, 1000, 0, 0, 24, 2700, '41', 'Mit zunehmendem Evolutionsstand vergr&ouml;&szlig;ern sich auch die Anforderungen an die Panzerung. Um die ersten Werespiders z&uuml;chten zu k&ouml;nnen, musste deshalb eine neue, allgemein als Klasse III bekannte Panzerung erzeugt werden.\r\n', 'Panzerungs-Evolution III', 'Mit zunehmendem Evolutionsstand vergrößern sich auch die Anforderungen an die Panzerung. Um die ersten Werespiders züchten zu können, musste deshalb eine neue, allgemein als Klasse III bekannte Panzerung erzeugt werden.\r\n', '', ''),
(43, 'Panzerungs-Evolution IV', 32000, 8000, 2000, 1000, 0, 48, 5800, '42', 'Das Z&uuml;chten von Brutbeuteln machte es notwendig Panzerungen zu entwickeln, welche die transportierten Spiders nicht nur besonders sch&uuml;tzt, sondern ihnen auch noch die M&ouml;glichkeit gibt, die Tarantula gefahrlos zu verlassen. \r\n', 'Panzerungs-Evolution IV', 'Das Züchten von Brutbeuteln machte es notwendig Panzerungen zu entwickeln, welche die transportierten Spiders nicht nur besonders schützt, sondern ihnen auch noch die Möglichkeit gibt, die Tarantula gefahrlos zu verlassen. \r\n', '', ''),
(44, 'Panzerungs-Evolution V', 64000, 16000, 4000, 2000, 0, 96, 11600, '43', 'Auch wenn die Klasse IV Panzerung schon undurchdringlich erscheint, so ist sie doch f&uuml;r die Giganten der K’tharr Flotte v&ouml;llig unzureichend. Erst das Z&uuml;chten der gewaltigen V’er Panzer brachte die gew&uuml;nschten Ergebnisse.\r\n', 'Panzerungs-Evolution V', 'Auch wenn die Klasse IV Panzerung schon undurchdringlich erscheint, so ist sie doch für die Giganten der K’tharr Flotte völlig unzureichend. Erst das Züchten der gewaltigen V’er Panzer brachte die gewünschten Ergebnisse.\r\n', '', ''),
(45, 'Lichtfaden', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Lichtf&auml;den sondern einen, dem Laser nicht un&auml;hnlichen, Impuls ab, der jedoch um einiges st&auml;rker ist.\r\n', 'Lichtfaden', 'Lichtfäden sondern einen, dem Laser nicht unähnlichen, Impuls ab, der jedoch um einiges stärker ist.\r\n', '', ''),
(46, 'L&auml;hmfaden', 8000, 2000, 0, 0, 0, 12, 1200, '45', 'Beim Versuch die Impulse der Lichtf&auml;den zu verst&auml;rken, trat ein sonderbarer Effekt auf. Statt das Ziel zu zerst&ouml;ren, machte der Impuls das Ziel nun bewegungsunf&auml;hig, womit es zur leichten Beute f&uuml;r anderer Waffen wurde.', 'Lähmfaden', 'Beim Versuch die Impulse der Lichtfäden zu verstärken, trat ein sonderbarer Effekt auf. Statt das Ziel zu zerstören, machte der Impuls das Ziel nun bewegungsunfähig, womit es zur leichten Beute für anderer Waffen wurde.', '', ''),
(47, 'Materiefaden', 16000, 4000, 1000, 0, 0, 24, 2700, '46', 'Der Materiefaden setzt nicht mehr auf einen einfachen Energieimpuls. Seine Geschosse setzen sich aus mehreren Komponenten zusammen. Beim Aufprall &ouml;ffnet sich die &auml;u&szlig;ere Kapsel und ein Schwall hochkonzentrierter S&auml;ure zerfrisst in Sekundenbruchteilen jedes Material. Die innere Kapsel durchdringt die Panzerung und der biologische Sprengstoff, welcher sich im Inneren befindet, richtet schlie&szlig;lich schweren Schaden an.\r\n', 'Materiefaden', 'Der Materiefaden setzt nicht mehr auf einen einfachen Energieimpuls. Seine Geschosse setzen sich aus mehreren Komponenten zusammen. Beim Aufprall öffnet sich die äußere Kapsel und ein Schwall hochkonzentrierter Säure zerfrisst in Sekundenbruchteilen jedes Material. Die innere Kapsel durchdringt die Panzerung und der biologische Sprengstoff, welcher sich im Inneren befindet, richtet schließlich schweren Schaden an.\r\n', '', ''),
(48, 'Plasmafaden', 32000, 8000, 2000, 1000, 0, 48, 5800, '47', 'Plasmaf&auml;den erhitzen ihre Geschosse so stark, dass nur noch waberndes Plasma &uuml;brig bleibt, welches sich mit Leichtigkeit durch jede Art von Panzerung schmilzt.\r\n', 'Plasmafaden', 'Plasmafäden erhitzen ihre Geschosse so stark, dass nur noch waberndes Plasma übrig bleibt, welches sich mit Leichtigkeit durch jede Art von Panzerung schmilzt.\r\n', '', ''),
(49, 'Partikelfaden', 64000, 16000, 4000, 2000, 0, 96, 11600, '48', 'Der Partikelfaden ist der st&auml;rkste und gr&ouml;&szlig;te Waffensymbiont, den die K’tharr entwickelt haben. Er verschie&szlig;t einen stark geb&uuml;ndelten Energieimpuls, der kleine Partikel von Antimaterie in sich tr&auml;gt und sie von der Umwelt hermetisch abschlie&szlig;t. Bei seinem Aufprall wird dieser Schutz zerst&ouml;rt und Materie und Antimaterie reagieren miteinander. Diese Reaktion ist so heftig, dass nichts einer solchen Explosion standhalten kann. Die schiere Gr&ouml;&szlig;e der hierf&uuml;r ben&ouml;tigten Organe l&auml;sst eine Installation jedoch nur auf Black Widows zu.\r\n', 'Partikelfaden', 'Der Partikelfaden ist der stärkste und größte Waffensymbiont, den die K’tharr entwickelt haben. Er verschießt einen stark gebündelten Energieimpuls, der kleine Partikel von Antimaterie in sich trägt und sie von der Umwelt hermetisch abschließt. Bei seinem Aufprall wird dieser Schutz zerstört und Materie und Antimaterie reagieren miteinander. Diese Reaktion ist so heftig, dass nichts einer solchen Explosion standhalten kann. Die schiere Größe der hierfür benötigten Organe lässt eine Installation jedoch nur auf Black Widows zu.\r\n', '', ''),
(50, 'Sporenkanal', 6000, 2000, 0, 0, 0, 9, 1000, '0', 'Der Sporenkanal erzeugt Sporen und schleudert sie auf gegnerische Einheiten.\r\n', 'Sporenkanal', 'Der Sporenkanal erzeugt Sporen und schleudert sie auf gegnerische Einheiten.\r\n', '', ''),
(51, 'Sporengeschoss I', 4000, 1000, 0, 0, 0, 6, 600, '50', 'Sporengeschosse &auml;hneln Sprengk&ouml;rpern in ihrer Funktionsweise. Die erste und schw&auml;chste Stufe ist mit einfachen intelligenten Raketen zu vergleichen, wie sie auch in Raketent&uuml;rmen zu finden sind.\r\n', 'Sporengeschoss I', 'Sporengeschosse ähneln Sprengkörpern in ihrer Funktionsweise. Die erste und schwächste Stufe ist mit einfachen intelligenten Raketen zu vergleichen, wie sie auch in Raketentürmen zu finden sind.\r\n', '', ''),
(52, 'Sporengeschoss II', 8000, 2000, 0, 0, 0, 12, 1200, '51', 'Eine Kreuzung von einfachen Sporengeschossen und Lichtf&auml;den ergab einen schnellen hochenergetischen Lichtimpuls, der beim Aufprall immensen Schaden anrichtet.', 'Sporengeschoss II', 'Eine Kreuzung von einfachen Sporengeschossen und Lichtfäden ergab einen schnellen hochenergetischen Lichtimpuls, der beim Aufprall immensen Schaden anrichtet.', '', ''),
(53, 'Sporengeschoss III', 16000, 4000, 1000, 0, 0, 24, 2700, '52', 'Klasse III Sporengeschosse sind eine einfache B&uuml;ndelung von Klasse I Sporen, deren Explosionskraft jedoch um ein vielfaches h&ouml;her ist.\r\n', 'Sporengeschoss III', 'Klasse III Sporengeschosse sind eine einfache Bündelung von Klasse I Sporen, deren Explosionskraft jedoch um ein vielfaches höher ist.\r\n', '', ''),
(54, 'Sporengeschoss IV', 32000, 8000, 2000, 1000, 0, 48, 5800, '53', 'Die st&auml;rkste und gef&auml;hrlichste Art der Sporen beinhaltet einen einzelnen Antimateriepartikel. Auch wenn ihre Explosionskraft geringer als die der Partikelf&auml;den ist, richtet sie nach wie vor schwerste Zerst&ouml;rungen an.\r\n', 'Sporengeschoss IV', 'Die stärkste und gefährlichste Art der Sporen beinhaltet einen einzelnen Antimateriepartikel. Auch wenn ihre Explosionskraft geringer als die der Partikelfäden ist, richtet sie nach wie vor schwerste Zerstörungen an.\r\n', '', ''),
(55, 'Leichtes Skelett', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Skelette sind das Grundger&uuml;st jedes K’tharr Bioschiffes. Sie beherbergen Waffen- und Antriebsorgane und tragen die leichten bis schweren Panzerplatten der einzelnen K’tharr Schiffe. Die kleinen Skelette eignen sich auf Grund ihres geringen Gewichts und ihrer geringen Tragf&auml;higkeit nur f&uuml;r Spiders.\r\n', 'Leichtes Skelett', 'Skelette sind das Grundgerüst jedes K’tharr Bioschiffes. Sie beherbergen Waffen- und Antriebsorgane und tragen die leichten bis schweren Panzerplatten der einzelnen K’tharr Schiffe. Die kleinen Skelette eignen sich auf Grund ihres geringen Gewichts und ihrer geringen Tragfähigkeit nur für Spiders.\r\n', '', ''),
(56, 'Skelett', 4000, 1000, 0, 0, 0, 6, 600, '55', 'Um zus&auml;tzliche Biowaffen tragen zu k&ouml;nnen, sind gr&ouml;&szlig;ere und st&auml;rkere Skelette n&ouml;tig. Sowohl Arctic Spiders als auch Netzf&auml;nger profitieren von diesem verbesserten Skelett.\r\n', 'Skelett', 'Um zusätzliche Biowaffen tragen zu können, sind größere und stärkere Skelette nötig. Sowohl Arctic Spiders als auch Netzfänger profitieren von diesem verbesserten Skelett.\r\n', '', ''),
(57, 'Schweres Skelett', 16000, 4000, 1000, 0, 0, 24, 2700, '56', 'Das schwere Skelett ist die Grundlage der Werespiders und tr&auml;gt ihre Waffen, Antriebe und Panzerplatten.', 'Schweres Skelett', 'Das schwere Skelett ist die Grundlage der Werespiders und trägt ihre Waffen, Antriebe und Panzerplatten.', '', ''),
(58, 'Panzerskelett', 32000, 8000, 2000, 1000, 0, 48, 5800, '57', 'Das Panzerskelett basiert nicht auf dem schweren Skelett, auch wenn dieses der Vorg&auml;nger war. Vielmehr wurde eine v&ouml;llig neue Art von Skelett erschaffen, dass nicht gro&szlig;e und schwere Organe tragen kann, sondern ihnen auch ein geh&ouml;riges Ma&szlig; an Schutz bieten kann. Jede Tarantula besitzt ein solches Skelett.\r\n', 'Panzerskelett', 'Das Panzerskelett basiert nicht auf dem schweren Skelett, auch wenn dieses der Vorgänger war. Vielmehr wurde eine völlig neue Art von Skelett erschaffen, dass nicht große und schwere Organe tragen kann, sondern ihnen auch ein gehöriges Maß an Schutz bieten kann. Jede Tarantula besitzt ein solches Skelett.\r\n', '', ''),
(59, 'Schweres Panzerskelett', 64000, 16000, 4000, 2000, 0, 96, 11600, '58', 'Als die K’tharr die ersten Partikelf&auml;den z&uuml;chteten, versuchten sie, diese auf ihren bis dato gr&ouml;&szlig;ten Schiffe, den Tarantulas, einzupflanzen. Doch die Organe des Waffensymbionten beanspruchten fast den gesamten Platz im Panzerskelett f&uuml;r sich alleine. Deshalb entstanden das schwere Panzerskelett und mit ihm die Black Widow.\r\n', 'Schweres Panzerskelett', 'Als die K’tharr die ersten Partikelfäden züchteten, versuchten sie, diese auf ihren bis dato größten Schiffe, den Tarantulas, einzupflanzen. Doch die Organe des Waffensymbionten beanspruchten fast den gesamten Platz im Panzerskelett für sich alleine. Deshalb entstanden das schwere Panzerskelett und mit ihm die Black Widow.\r\n', '', ''),
(60, 'Coxad&uuml;se', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Coxad&uuml;sen sind kleine Hochleistungsantriebe. Sie sind extrem leicht zu man&ouml;vrieren und bieten kurzfristig hohe Geschwindigkeiten, eignen sich jedoch kaum f&uuml;r Langstreckenfl&uuml;ge.\r\n', 'Coxadüse', 'Coxadüsen sind kleine Hochleistungsantriebe. Sie sind extrem leicht zu manövrieren und bieten kurzfristig hohe Geschwindigkeiten, eignen sich jedoch kaum für Langstreckenflüge.\r\n', '', ''),
(61, 'Trochanterd&uuml;se', 4000, 1000, 0, 0, 0, 6, 600, '60', 'Diese D&uuml;sen sind die Antriebsorgane der Arctic Spiders. Im Gegensatz zu den Coxad&uuml;sen bieten sie auch &uuml;ber l&auml;ngere Strecken angenehm kurze Reisezeiten.\r\n\r\n', 'Trochanterdüse', 'Diese Düsen sind die Antriebsorgane der Arctic Spiders. Im Gegensatz zu den Coxadüsen bieten sie auch über längere Strecken angenehm kurze Reisezeiten.\r\n\r\n', '', ''),
(62, 'Femurd&uuml;se', 16000, 4000, 1000, 0, 0, 24, 2700, '61', 'Femurd&uuml;sen verleihen den Werespiders ihre Wendigkeit. Auch erm&ouml;glichen sie es diesen, selbst &uuml;ber lange Strecken mit h&ouml;chster Geschwindigkeit zu bewegen.\r\n', 'Femurdüse', 'Femurdüsen verleihen den Werespiders ihre Wendigkeit. Auch ermöglichen sie es diesen, selbst über lange Strecken mit höchster Geschwindigkeit zu bewegen.\r\n', '', ''),
(63, 'Patellad&uuml;se', 35000, 8000, 2000, 1100, 0, 52, 6140, '62', 'Die Patellad&uuml;se der Tarantulas bietet zwar hohe Geschwindigkeiten auf langen Strecken, ist jedoch nicht in der Lage, diese &uuml;ber kurze Distanzen zu erreichen, weshalb die Tarantula in Gefechten &auml;u&szlig;erst schwerf&auml;llig ist.\r\n', 'Patelladüse', 'Die Patelladüse der Tarantulas bietet zwar hohe Geschwindigkeiten auf langen Strecken, ist jedoch nicht in der Lage, diese über kurze Distanzen zu erreichen, weshalb die Tarantula in Gefechten äußerst schwerfällig ist.\r\n', '', ''),
(64, 'Tibad&uuml;se', 64000, 16000, 4000, 2000, 0, 96, 11600, '63', 'Die Tibad&uuml;se ist eine Weiterentwicklung der Patellad&uuml;se. Sie sollte der Black Widow die Geschwindigkeit einer Tarantula verleihen, doch die schiere Masse der Black Widow machte dies unm&ouml;glich.\r\n', 'Tibadüse', 'Die Tibadüse ist eine Weiterentwicklung der Patelladüse. Sie sollte der Black Widow die Geschwindigkeit einer Tarantula verleihen, doch die schiere Masse der Black Widow machte dies unmöglich.\r\n', '', ''),
(65, 'Netzevolution', 4000, 1000, 0, 0, 0, 5, 600, '0', 'Die Netzevolution erschuf den Netzteleporter, welcher das Einfangen und Teleportieren von Kollektoren erm&ouml;glicht. \r\n', 'Netzevolution', 'Die Netzevolution erschuf den Netzteleporter, welcher das Einfangen und Teleportieren von Kollektoren ermöglicht. \r\n', '', ''),
(66, 'Schattenfeld', 15000, 6000, 0, 0, 0, 18, 2700, '9', 'Schattenfelder kr&uuml;mmen das Licht um sich herum, so dass der Tr&auml;ger dieses Feldes unsichtbar erscheint. Vielaugen und Agenten nutzen dieses Organ, um unerkannt zu bleiben, doch vor den besten Scannern k&ouml;nnen sie sich nicht verstecken.\r\n', 'Schattenfeld', 'Schattenfelder krümmen das Licht um sich herum, so dass der Träger dieses Feldes unsichtbar erscheint. Vielaugen und Agenten nutzen dieses Organ, um unerkannt zu bleiben, doch vor den besten Scannern können sie sich nicht verstecken.\r\n', '', ''),
(67, 'Dr&uuml;senevolution', 8000, 2000, 0, 0, 0, 15, 1200, '22', 'Die Dr&uuml;senevolution erm&ouml;glichte es den K’tharr ihre Waffenorgane auch auf ihren Bauten an zu bringen. Da diese Waffen jedoch erst auf k&uuml;rzere Distanz ihr volles Potential entfalten, sind die Dr&uuml;senvarianten schwach und nur f&uuml;r den Notfall geeignet.\r\n', 'Drüsenevolution', 'Die Drüsenevolution ermöglichte es den K’tharr ihre Waffenorgane auch auf ihren Bauten an zu bringen. Da diese Waffen jedoch erst auf kürzere Distanz ihr volles Potential entfalten, sind die Drüsenvarianten schwach und nur für den Notfall geeignet.\r\n', '', ''),
(68, 'Brutbeutelevolution I', 12000, 3000, 1000, 0, 0, 18, 2100, '58', 'n sack&auml;hnlichen Beuteln schlummern Spiders vor sich hin, bis sie geweckt werden und in den Angriff &uuml;bergehen k&ouml;nnen. Die kleine Variante der Brutbeutel fasst 20 Spider und ist auf jeder Tarantula vor zu finden.\r\n', 'Brutbeutelevolution I', 'n sackähnlichen Beuteln schlummern Spiders vor sich hin, bis sie geweckt werden und in den Angriff übergehen können. Die kleine Variante der Brutbeutel fasst 20 Spider und ist auf jeder Tarantula vor zu finden.\r\n', '', ''),
(69, 'Brutbeutelevolution II', 24000, 4000, 2000, 1900, 0, 36, 4560, '59;68', 'Die Gr&ouml;&szlig;e der Black Widow erm&ouml;glicht es den K’tharr, gr&ouml;&szlig;ere Brutbeutel zu z&uuml;chten. Diese transportieren 50 Spiders, welche sich ohne R&uuml;cksicht auf Verluste auf den Gegner st&uuml;rzen. \r\n', 'Brutbeutelevolution II', 'Die Größe der Black Widow ermöglicht es den K’tharr, größere Brutbeutel zu züchten. Diese transportieren 50 Spiders, welche sich ohne Rücksicht auf Verluste auf den Gegner stürzen. \r\n', '', ''),
(70, 'Virtuelles Transmitterfeld', 50000, 20000, 6000, 4000, 1, 72, 13400, '65', 'Erm&ouml;glicht Transmitterverbindungen auch au&szlig;erhalb des normalen Transmittersystems, jedoch unter hohem Risiko f&uuml;r den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', 'Virtuelles Transmitterfeld', 'Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', '', ''),
(71, 'Cyborggrundlagen', 20000, 20000, 10000, 8000, 0, 36, 12200, '8', 'Erm&ouml;glicht die Entwicklung von Cyborgtechnologien.', 'Cyborggrundlagen', 'Ermöglicht die Entwicklung von Cyborgtechnologien.', '', ''),
(72, 'Cyborginkubationstank', 10000, 5000, 1000, 2000, 0, 24, 3100, '71', 'Dient zur Z&uuml;chtung des biologischen Cyborggewebes.', 'Cyborginkubationstank', 'Dient zur Züchtung des biologischen Cyborggewebes.', '', ''),
(73, 'Cyborgsteuerungskristall', 5000, 60000, 2000, 2000, 0, 54, 13900, '71', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsf&auml;hig.', 'Cyborgsteuerungskristall', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.', '', ''),
(74, 'Cyborgimplantate', 10000, 6000, 10000, 7000, 0, 36, 8000, '71', 'Sie bilden das Grundger&uuml;st des Cyborgs.', 'Cyborgimplantate', 'Sie bilden das Grundgerüst des Cyborgs.', '', ''),
(75, 'Blockerstrahlmodulator', 500000, 100000, 50000, 20000, 75, 144, 168000, '5', 'Der Blockerstrahlmodulator erm&ouml;glicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen au&szlig;er Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.', 'Blockerstrahlmodulator', 'Der Blockerstrahlmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.', '', ''),
(80, 'Wandlerzelle', 1000, 100, 0, 0, 0, 4, 120, '7', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung f&uuml;r Kollektoren f&uuml;hrt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann f&uuml;r den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', 'Wandlerzelle', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', '', ''),
(81, 'Spider', 750, 500, 0, 0, 0, 8, 175, '13;40;45;55;60', '(J&auml;gerklasse)\r\nSpider m&ouml;gen zwar klein und nur leicht bewaffnet sein, doch sollte man sie, so wie auch alle anderen K’tharr Schiffe, auf keinen Fall untersch&auml;tzen. Zu Dutzenden fallen sie &uuml;ber einzelne kleine J&auml;gereinheiten her und rei&szlig;en sie regelrecht in Fetzen. Doch sollen sie es - Ger&uuml;chten zu Folge - auch schon geschafft haben, gro&szlig;e Kampfschiffe zu zerst&ouml;ren. Egal ob die Ger&uuml;chte nun stimmen oder nicht, K’tharr Flotten mit vielen Spiders sind eine sehr ernstzunehmende Bedrohung auf die reagiert werden muss. Allerdings sind ihre Antriebe nicht f&uuml;r lange Fl&uuml;ge ausgelegt, weshalb sie f&uuml;r lange Strecken von Tr&auml;gerschiffen transportiert werden sollten.  ', 'Spider', '(Jägerklasse)\r\nSpider mögen zwar klein und nur leicht bewaffnet sein, doch sollte man sie, so wie auch alle anderen K’tharr Schiffe, auf keinen Fall unterschätzen. Zu Dutzenden fallen sie über einzelne kleine Jägereinheiten her und reißen sie regelrecht in Fetzen. Doch sollen sie es - Gerüchten zu Folge - auch schon geschafft haben, große Kampfschiffe zu zerstören. Egal ob die Gerüchte nun stimmen oder nicht, K’tharr Flotten mit vielen Spiders sind eine sehr ernstzunehmende Bedrohung auf die reagiert werden muss. Allerdings sind ihre Antriebe nicht für lange Flüge ausgelegt, weshalb sie für lange Strecken von Trägerschiffen transportiert werden sollten.  ', '', ''),
(82, 'Arctic Spider', 4000, 1500, 0, 0, 0, 16, 700, '13;41;45;46;56;61', '(Jagdbootklasse)\r\nObwohl Arctic Spiders ebenso wie die Spiders mit Lichtf&auml;den ausger&uuml;stet sind, ist ihre Prim&auml;rwaffe doch der L&auml;hmfaden. Ihre Prim&auml;raufgabe besteht darin, gegnerische Gro&szlig;kampfschiffe kampfunf&auml;hig zu machen, so dass diese keine Bedrohung mehr sind. Sie st&uuml;rzen sich in Gruppen auf jedes gro&szlig;e Schiff und selbst Giganten der Schlachtschiffklasse trudeln nach ein paar gezielten Treffern nur noch hilflos durchs Weltall. \r\n', 'Arctic Spider', '(Jagdbootklasse)\r\nObwohl Arctic Spiders ebenso wie die Spiders mit Lichtfäden ausgerüstet sind, ist ihre Primärwaffe doch der Lähmfaden. Ihre Primäraufgabe besteht darin, gegnerische Großkampfschiffe kampfunfähig zu machen, so dass diese keine Bedrohung mehr sind. Sie stürzen sich in Gruppen auf jedes große Schiff und selbst Giganten der Schlachtschiffklasse trudeln nach ein paar gezielten Treffern nur noch hilflos durchs Weltall. \r\n', '', ''),
(83, 'Werespider', 15000, 2500, 1500, 1000, 0, 32, 2850, '13;42;47;51;57;62', '(Zerst&ouml;rerklasse)\r\nBei dem Versuch die Kampfkraft der eigenen Schiffe noch mehr zu erh&ouml;hen, manipulierten die K’tharr am Gen-Code der Arctic Spiders. Sie schafften es zwar diese zu vergr&ouml;&szlig;ern und ihre Waffen weitaus effektiver zu nutzen, doch der ‚Charakter’ dieser Schiffe ist alles andere als berechenbar. Werespiders rasen mit Vorliebe durch die gegnerischen Flotten und hinterlassen dabei die rauchenden Wracks s&auml;mtlicher Schiffe, die dumm genug waren, sich ihnen in den Weg zu stellen. Doch die Werespiders gehen in ihrem Zerst&ouml;rungsrausch nicht planlos vor. Immer wieder wird berichtet, dass Schiffe der Jagdbootklasse ihre ersten Opfer sind, gefolgt von Kreuzerklassen und Schlachtschiffklassen. \r\n', 'Werespider', '(Zerstörerklasse)\r\nBei dem Versuch die Kampfkraft der eigenen Schiffe noch mehr zu erhöhen, manipulierten die K’tharr am Gen-Code der Arctic Spiders. Sie schafften es zwar diese zu vergrößern und ihre Waffen weitaus effektiver zu nutzen, doch der ‚Charakter’ dieser Schiffe ist alles andere als berechenbar. Werespiders rasen mit Vorliebe durch die gegnerischen Flotten und hinterlassen dabei die rauchenden Wracks sämtlicher Schiffe, die dumm genug waren, sich ihnen in den Weg zu stellen. Doch die Werespiders gehen in ihrem Zerstörungsrausch nicht planlos vor. Immer wieder wird berichtet, dass Schiffe der Jagdbootklasse ihre ersten Opfer sind, gefolgt von Kreuzerklassen und Schlachtschiffklassen. \r\n', '', ''),
(84, 'Tarantula', 30000, 6500, 2000, 2200, 0, 64, 5780, '13;43;48;52;58;63;68', '(Kreuzerklasse)\r\nMit den Tarantulas erschufen die K’tharr die ersten Gro&szlig;raumschiffe. Ausger&uuml;stet mit einer Unmenge an Waffensymbionten k&ouml;nnen sie es m&uuml;helos mit allen gr&ouml;&szlig;eren Schiffen aufnehmen. Doch auf Grund ihrer Tr&auml;gheit sind sie kaum in der Lage mit J&auml;gern fertig zu werden. Dies war auch der Grund weshalb nach einigen Testreihen die Tarantula mit Brutbeuteln ausger&uuml;stet wurde, worin sie bis zu 20 Spiders transportieren kann. \r\n', 'Tarantula', '(Kreuzerklasse)\r\nMit den Tarantulas erschufen die K’tharr die ersten Großraumschiffe. Ausgerüstet mit einer Unmenge an Waffensymbionten können sie es mühelos mit allen größeren Schiffen aufnehmen. Doch auf Grund ihrer Trägheit sind sie kaum in der Lage mit Jägern fertig zu werden. Dies war auch der Grund weshalb nach einigen Testreihen die Tarantula mit Brutbeuteln ausgerüstet wurde, worin sie bis zu 20 Spiders transportieren kann. \r\n', '', ''),
(85, 'Black Widow', 45000, 25000, 2000, 3000, 1, 96, 12300, '13;44;49;53;54;59;70;64;69', '(Schlachtschiffklasse)\r\nDie Black Widow ist zweifellos das gr&ouml;&szlig;te und gef&auml;hrlichste Schiff, welches die K’tharr jemals geschaffen haben. Nicht nur das sie vor Waffen starrt und damit problemlos alle gr&ouml;&szlig;eren Schiffe zu Weltraumschrott verarbeitet, sie ist auch noch in der Lage, bis zu 90 Spiders zu transportieren, die ihr kleine Schiffe vom Leib halten.\r\n', 'Black Widow', '(Schlachtschiffklasse)\r\nDie Black Widow ist zweifellos das größte und gefährlichste Schiff, welches die K’tharr jemals geschaffen haben. Nicht nur das sie vor Waffen starrt und damit problemlos alle größeren Schiffe zu Weltraumschrott verarbeitet, sie ist auch noch in der Lage, bis zu 90 Spiders zu transportieren, die ihr kleine Schiffe vom Leib halten.\r\n', '', ''),
(86, 'Hellspider', 1000, 500, 0, 0, 0, 10, 200, '13;41;53;56;61', '(Jagdboot-Klasse) Der Hellspider wurde speziell dazu entwickelt eine massive planetare Verteidigung zu durchbrechen und sie evtl. auszuschalten. Er ist sehr wirksam gegen planetare Gesch&uuml;tze, aber wegen der hohen Bombenlast nicht geeignet im Raumkampf.', 'Hellspider', '(Jagdboot-Klasse) Der Hellspider wurde speziell dazu entwickelt eine massive planetare Verteidigung zu durchbrechen und sie evtl. auszuschalten. Er ist sehr wirksam gegen planetare Geschütze, aber wegen der hohen Bombenlast nicht geeignet im Raumkampf.', '', ''),
(87, 'Netzf&auml;nger', 2000, 1000, 0, 0, 0, 12, 400, '13;23;56;61;65', '(Kollektorschiffklasse)\r\nNat&uuml;rlich k&ouml;nnten die K’tharr sich ihre Kollektoren auch selber bauen, doch ist es manchmal wesentlich einfacher, sie einem schwachen Gegner abzunehmen. Zu diesem Zweck erschufen sie die Netzf&auml;nger, welche mit einem einzigartigem Organismus ausgestattet sind. Hat ein Netzf&auml;nger einen Kollektor erreicht, umschlie&szlig;t er ihn und t&ouml;tet sich selbst beim Aufbauen eines Portals. Der Kollektor durchquert dieses Portal und erscheint ohne nennenswerten Zeitverlust auf dem Heimatplaneten des Netzf&auml;ngers.\r\n', 'Netzfänger', '(Kollektorschiffklasse)\r\nNatürlich könnten die K’tharr sich ihre Kollektoren auch selber bauen, doch ist es manchmal wesentlich einfacher, sie einem schwachen Gegner abzunehmen. Zu diesem Zweck erschufen sie die Netzfänger, welche mit einem einzigartigem Organismus ausgestattet sind. Hat ein Netzfänger einen Kollektor erreicht, umschließt er ihn und tötet sich selbst beim Aufbauen eines Portals. Der Kollektor durchquert dieses Portal und erscheint ohne nennenswerten Zeitverlust auf dem Heimatplaneten des Netzfängers.\r\n', '', ''),
(88, 'Gigantula', 60000, 30000, 6000, 6000, 1, 80, 17200, '13;44;45;59;64;69', '(Schlachtschiff-Klasse) Die Gigantula ist ein Tr&auml;gerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um gro&szlig;e Mengen J&auml;ger transportieren zu k&ouml;nnen wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste daf&uuml;r die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen J&auml;gern wieder wettmachen. Die Ladekapazit&auml;t betr&auml;gt 250 J&auml;ger', 'Gigantula', '(Schlachtschiff-Klasse) Die Gigantula ist ein Trägerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um große Mengen Jäger transportieren zu können wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste dafür die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen Jägern wieder wettmachen. Die Ladekapazität beträgt 250 Jäger', '', ''),
(100, 'Schwarm der Nestverteidiger', 7500, 5000, 0, 0, 0, 8, 1750, '13;22', 'Der Schwarm der Nestverteidiger beherbergt mehrere J&auml;ger. Jedoch sind diese J&auml;ger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden k&ouml;nnen.', 'Schwarm der Nestverteidiger', 'Der Schwarm der Nestverteidiger beherbergt mehrere Jäger. Jedoch sind diese Jäger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden können.', '', ''),
(101, 'Sporendr&uuml;se', 1000, 300, 0, 0, 0, 20, 160, '22;51;67', 'Sporendr&uuml;sen wurden f&uuml;r die Jagd auf Schiffe der Jagdbootklasse geschaffen. Zu diesem Zweck sind sie mit einer Vielzahl von hochexplosiven Sporen ausger&uuml;stet.\r\n', 'Sporendrüse', 'Sporendrüsen wurden für die Jagd auf Schiffe der Jagdbootklasse geschaffen. Zu diesem Zweck sind sie mit einer Vielzahl von hochexplosiven Sporen ausgerüstet.\r\n', '', ''),
(102, 'Lichtdr&uuml;se', 500, 500, 0, 0, 0, 10, 150, '22;45;67', 'Diese einfachste Form der Verteidigungsdr&uuml;sen wird mit Lichtf&auml;den &auml;hnlichen Organen herangez&uuml;chtet. Ihr erhabenes Design macht sie auch zu einem echten Augenschmaus.', 'Lichtdrüse', 'Diese einfachste Form der Verteidigungsdrüsen wird mit Lichtfäden ähnlichen Organen herangezüchtet. Ihr erhabenes Design macht sie auch zu einem echten Augenschmaus.', '', ''),
(103, 'Materiedr&uuml;se', 3000, 200, 0, 0, 0, 28, 340, '22;47;67', 'Ihre Geschosse durchbrechen mit Leichtigkeit die st&auml;rkste Panzerung. Deshalb sind sie besonders f&uuml;r die Vernichtung von Zerst&ouml;rern und Kreuzern geeignet. ', 'Materiedrüse', 'Ihre Geschosse durchbrechen mit Leichtigkeit die stärkste Panzerung. Deshalb sind sie besonders für die Vernichtung von Zerstörern und Kreuzern geeignet. ', '', ''),
(104, 'Plasmadr&uuml;se', 2250, 1500, 300, 0, 0, 48, 615, '22;48;67', 'Die gr&ouml;&szlig;te und gef&auml;hrlichste aller Dr&uuml;sen zerst&ouml;rt mit Leichtigkeit jede Schlachtschiffklasse, die sich in ihre Waffenreichweite wagt. ', 'Plasmadrüse', 'Die größte und gefährlichste aller Drüsen zerstört mit Leichtigkeit jede Schlachtschiffklasse, die sich in ihre Waffenreichweite wagt. ', '', ''),
(110, 'Vielauge', 500, 500, 0, 0, 0, 2, 150, '9;13;62;66', '(Sonde)\r\nVielaugen sind kleine merkw&uuml;rdige Gesch&ouml;pfe. Ihre Augen &auml;hneln denen des Raumblickes, womit sie in der Lage sind, Planeten aus gen&uuml;gender Entfernung auf Schiffe, Geb&auml;ude und Kollektoren zu untersuchen. Aufgrund ihrer Gr&ouml;&szlig;e sind sie nur schwer von Scannern auszumachen und wenn sie entdeckt werden, erscheinen sie nur als normale Sonden, wodurch ihre Herkunft unbestimmbar bleibt. Wie die Netzf&auml;nger springen die Vielaugen durch Portale, um in k&uuml;rzester Zeit bei ihrem Ziel zu sein. Die Energie reicht jedoch nur f&uuml;r einen Sprung und f&uuml;r die einmalige &Uuml;bertragung der ersp&auml;hten Daten. Nach diesem Kraftakt stirbt das Vielauge.\r\n', 'Vielauge', '(Sonde)\r\nVielaugen sind kleine merkwürdige Geschöpfe. Ihre Augen ähneln denen des Raumblickes, womit sie in der Lage sind, Planeten aus genügender Entfernung auf Schiffe, Gebäude und Kollektoren zu untersuchen. Aufgrund ihrer Größe sind sie nur schwer von Scannern auszumachen und wenn sie entdeckt werden, erscheinen sie nur als normale Sonden, wodurch ihre Herkunft unbestimmbar bleibt. Wie die Netzfänger springen die Vielaugen durch Portale, um in kürzester Zeit bei ihrem Ziel zu sein. Die Energie reicht jedoch nur für einen Sprung und für die einmalige Übertragung der erspähten Daten. Nach diesem Kraftakt stirbt das Vielauge.\r\n', '', ''),
(111, 'Zecke', 500, 500, 200, 100, 0, 8, 250, '66', 'Der Spion / Geheimagent:<br><br>\r\n\r\nDer Spion ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihm kann man &uuml;ber jeden beliebigen Spieler so ziemlich alles herausfinden.', 'Zecke', 'Der Spion / Geheimagent:<br><br>\r\n\r\nDer Spion ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihm kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.', '', '');
INSERT INTO `de_tech_data3` (`tech_id`, `tech_name`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `tech_ticks`, `score`, `tech_vor`, `des`, `tech_name1`, `des1`, `tech_name2`, `des2`) VALUES
(120, 'Sektorraumbasis', 7500000, 3750000, 750000, 375000, 250, 192, 2125000, '0', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und Wohn- und Arbeitst&auml;tte f&uuml;r alle Bewohner der Sektors sein kann.\r\n<br>Vornehmlich dient sie als Navigations- und Handelsst&uuml;tzpunkt, kann aber durch Erweiterung auch als Flottenbasis f&uuml;r die Sektorflotte dienen.\r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektorbasis ist aufgrund der unglaublichen Gr&ouml;&szlig;e sehr teuer in der Konstruktion, besonders Tronic wird f&uuml;r eine derartig gro&szlig;e Konstruktion viel ben&ouml;tigt.', 'Sektorraumbasis', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und Wohn- und Arbeitstätte für alle Bewohner der Sektors sein kann.\r\n<br>Vornehmlich dient sie als Navigations- und Handelsstützpunkt, kann aber durch Erweiterung auch als Flottenbasis für die Sektorflotte dienen.\r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektorbasis ist aufgrund der unglaublichen Größe sehr teuer in der Konstruktion, besonders Tronic wird für eine derartig große Konstruktion viel benötigt.', '', ''),
(121, 'Sektorsprungfeldbegrenzer', 1500000, 750000, 375000, 150000, 150, 144, 622500, '120', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', 'Sektorsprungfeldbegrenzer', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', '', ''),
(122, 'Sektorraumwerft', 750000, 750000, 375000, 375000, 60, 144, 547500, '120', 'In der Sektorraumwerft k&ouml;nnen Schiffe f&uuml;r die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', 'Sektorraumwerft', 'In der Sektorraumwerft können Schiffe für die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', '', ''),
(123, 'Sektorhandelszentrum', 150000, 75000, 75000, 150000, 30, 96, 142500, '120', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', 'Sektorhandelszentrum', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', '', ''),
(124, 'Scannerphalanx', 600000, 1500000, 250000, 750000, 110, 96, 845000, '120', 'Die Scannerphalanx erm&ouml;glicht das Scannen von Sektorraumbasen und deren Flottenst&auml;rke.', 'Scannerphalanx', 'Die Scannerphalanx ermöglicht das Scannen von Sektorraumbasen und deren Flottenstärke.', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tech_data4`
--

CREATE TABLE `de_tech_data4` (
  `tech_id` int(11) NOT NULL DEFAULT '0',
  `tech_name` varchar(40) NOT NULL DEFAULT '',
  `restyp01` int(11) NOT NULL DEFAULT '0',
  `restyp02` int(11) NOT NULL DEFAULT '0',
  `restyp03` int(11) NOT NULL DEFAULT '0',
  `restyp04` int(11) NOT NULL DEFAULT '0',
  `restyp05` int(11) NOT NULL DEFAULT '0',
  `tech_ticks` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `tech_vor` varchar(40) NOT NULL DEFAULT '',
  `des` text NOT NULL,
  `tech_name1` varchar(40) NOT NULL DEFAULT '',
  `des1` text NOT NULL,
  `tech_name2` varchar(40) NOT NULL DEFAULT '',
  `des2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_tech_data4`
--

INSERT INTO `de_tech_data4` (`tech_id`, `tech_name`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `tech_ticks`, `score`, `tech_vor`, `des`, `tech_name1`, `des1`, `tech_name2`, `des2`) VALUES
(1, 'kl. Stock', 4000, 1000, 0, 0, 0, 1, 600, '0', 'Der kl. Stock ist eine kleine Geb&auml;udefabrik. Hier werden einfache Geb&auml;ude durch Roboter gefertigt. Dieses Geb&auml;ude kann erweitert werden.', 'kl. Stock', 'Der kl. Stock ist eine kleine Gebäudefabrik. Hier werden einfache Gebäude durch Roboter gefertigt. Dieses Gebäude kann erweitert werden.', '', ''),
(2, 'Stock', 90000, 9000, 7000, 900, 0, 43, 13260, '1', 'Dies ist die Erweiterung des kl. Stocks. Hier lassen sich hochwertigere Geb&auml;ude herstellen.', 'Stock', 'Dies ist die Erweiterung des kl. Stocks. Hier lassen sich hochwertigere Gebäude herstellen.', '', ''),
(3, 'Planetare Handelswabe', 6000, 1500, 0, 0, 0, 9, 900, '65', 'Die Planetare Handelswabe erm&ouml;glicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine W&auml;hrung im Universum unn&ouml;tig ist. Dieses Geb&auml;ude kann erweitert werden.', 'Planetare Handelswabe', 'Die Planetare Handelswabe ermöglicht den Handel von Rohstoffen innerhalb des Sektors. Es besteht ein reiner Tauschhandel, da eine Währung im Universum unnötig ist. Dieses Gebäude kann erweitert werden.', '', ''),
(4, 'Handelswabe des Universums', 35000, 9500, 0, 0, 0, 21, 5400, '2;3', 'Die Handelswabe des Universums ist die Erweiterung der Planetaren Handelswabe und erm&ouml;glicht den Handel auch &uuml;ber die Sektorengrenzen\r\n\r\nhinaus.', 'Handelswabe des Universums', 'Die Handelswabe des Universums ist die Erweiterung der Planetaren Handelswabe und ermöglicht den Handel auch über die Sektorengrenzen\r\n\r\nhinaus.', '', ''),
(5, 'Wabe des Haltens', 190000, 190000, 75000, 37000, 75, 68, 169300, '2;63', 'Die Wabe des Haltens erm&ouml;glicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine gr&ouml;&szlig;ere M&ouml;glichkeit besteht noch Verst&auml;rkung von Verb&uuml;ndeten zu erhalten.', 'Wabe des Haltens', 'Die Wabe des Haltens ermöglicht es durch ein riesiges Kraftfeld eine anfliegende Feindflotte um einen Kampftick zu verlangsamen, damit eine größere Möglichkeit besteht noch Verstärkung von Verbündeten zu erhalten.', '', ''),
(6, 'Extraktorwabe', 120000, 80000, 20000, 5000, 0, 96, 36000, '2;65', 'Ist die Extraktorwabe gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Transwabenfeld eingefangen. Da die gegnerischen Schiffe weiter au&szlig;erhalb dieses Transwabenfeldes liegen, k&ouml;nnen nur die Wracks der verteidigenden Flotten und der eigenen T&uuml;rme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEin Recyclotron gewinnt 10% der Ressourcen der zerst&ouml;rten Schiffe zur&uuml;ck!\r\n\r\n\r\n', 'Extraktorwabe', 'Ist die Extraktorwabe gebaut, gewinnt es nach einer Raumschlacht Ressourcen aus den Wracks der Schiffe. Diese werden durch das Transwabenfeld eingefangen. Da die gegnerischen Schiffe weiter außerhalb dieses Transwabenfeldes liegen, können nur die Wracks der verteidigenden Flotten und der eigenen Türme recycelt werden. Es ist also ein defensives System das den Wiederaufbau beschleunigen soll. \r\n\r\nEin Recyclotron gewinnt 10% der Ressourcen der zerstörten Schiffe zurück!\r\n\r\n\r\n', '', ''),
(7, 'Arbeiterwabe', 6000, 1000, 0, 0, 0, 6, 800, '1', 'Die Arbeiterwabe stellt die Sammlerwaben zur Energiegewinnung her. Sind Sammlerwaben fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebraucht und auf die Sonne\r\n\r\nausgerichtet.', 'Arbeiterwabe', 'Die Arbeiterwabe stellt die Sammlerwaben zur Energiegewinnung her. Sind Sammlerwaben fertig so werden sie automatisch in eine Umlaufbahn um den Planeten gebraucht und auf die Sonne\r\n\r\nausgerichtet.', '', ''),
(8, 'Netzwerk des Denkens', 11000, 2750, 0, 0, 0, 10, 1650, '1', 'Das Netzwerk des Denkens dient zur Erforschung von noch unbekannten Technologien. Diese Technologien erm&ouml;glichen dann den weiteren Bau von Geb&auml;uden, Gesch&uuml;tzt&uuml;rmen und Schiffen.', 'Netzwerk des Denkens', 'Das Netzwerk des Denkens dient zur Erforschung von noch unbekannten Technologien. Diese Technologien ermöglichen dann den weiteren Bau von Gebäuden, Geschütztürmen und Schiffen.', '', ''),
(9, 'Aufkl&auml;rerwabe', 10000, 2500, 0, 0, 0, 12, 1500, '1', 'Die Aufkl&auml;rerwabe bietet die M&ouml;glichkeit in geheimen Werkst&auml;tten Tunnellarven zu bauen um mehr &uuml;ber den Gegner zu erfahren.', 'Aufklärerwabe', 'Die Aufklärerwabe bietet die Möglichkeit in geheimen Werkstätten Tunnellarven zu bauen um mehr über den Gegner zu erfahren.', '', ''),
(10, 'Augen der Arbeiterin', 3000, 750, 0, 0, 0, 10, 450, '1', 'Die Augen der Arbeiterin erm&ouml;glicht es ankommende Flotten fr&uuml;hzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit betr&auml;gt die Chance eine Flotte zu erkennen nur 33%! Es k&ouml;nnen aber auch Tunnellarven erkannt werden; hier betr&auml;gt die Chance 15 %. Dieses Geb&auml;ude kann erweitert werden!', 'Augen der Arbeiterin', 'Die Augen der Arbeiterin ermöglicht es ankommende Flotten frühzeitig zu erkennen und somit eine Verteidigung zu organisieren! Jedoch sind die Sensoren nicht die besten und somit beträgt die Chance eine Flotte zu erkennen nur 33%! Es können aber auch Tunnellarven erkannt werden; hier beträgt die Chance 15 %. Dieses Gebäude kann erweitert werden!', '', ''),
(11, 'Augen der Drohne', 17500, 4500, 750, 0, 0, 22, 2875, '2;10', 'Diese neue Scannertechnik benutzt einen weitreichenden Ultrawellenstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance betr&auml;gt hier 66% und f&uuml;r Tunnellarven 30 %. Dieses Geb&auml;ude kann erweitert werden!', 'Augen der Drohne', 'Diese neue Scannertechnik benutzt einen weitreichenden Ultrawellenstrahl und kann somit eine ankommende Flotte eher erkennen. Die Chance beträgt hier 66% und für Tunnellarven 30 %. Dieses Gebäude kann erweitert werden!', '', ''),
(12, 'Augen der Koenigin', 90000, 37500, 3500, 900, 0, 48, 17910, '11', 'Diese revolution&auml;re Scannertechnik erm&ouml;glicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Leider haben sie nach wie vor Probleme Sonden zu entdecken, so dass diese zwar mit einer Wahrscheinlichkeit von 45% entdeckt werden k&ouml;nnen, jedoch immer noch die M&ouml;glichkeit besteht, sie nicht zu entdecken.', 'Augen der Koenigin', 'Diese revolutionäre Scannertechnik ermöglicht es eine im Anflug befindliche Flotte mit einer 100%tigen Wahrscheinlichkeit zu erkennen. Leider haben sie nach wie vor Probleme Sonden zu entdecken, so dass diese zwar mit einer Wahrscheinlichkeit von 45% entdeckt werden können, jedoch immer noch die Möglichkeit besteht, sie nicht zu entdecken.', '', ''),
(13, 'Drohnenwabe', 12000, 2500, 0, 0, 0, 22, 1700, '1', 'Die Drohnenwabe dient zur Produktion der modernsten und gr&ouml;&szlig;ten Raumschiffen, die das All je gesehen hat.', 'Drohnenwabe', 'Die Drohnenwabe dient zur Produktion der modernsten und größten Raumschiffen, die das All je gesehen hat.', '', ''),
(14, 'Arbeiterlager M', 10000, 1000, 0, 0, 0, 3, 1200, '1', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Arbeiterl&auml;ger so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Arbeiterlager M', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(15, 'Arbeiterlager D', 12500, 2000, 0, 0, 0, 6, 1650, '14', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Arbeiterl&auml;ger so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Arbeiterlager D', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(16, 'Arbeiterlager I', 15000, 3000, 0, 0, 0, 9, 2100, '15', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Arbeiterl&auml;ger so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Arbeiterlager I', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(17, 'Arbeiterlager E', 17500, 4000, 0, 0, 0, 12, 2550, '16', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verh&auml;ltnis. Hat man mehrere Arbeiterl&auml;ger so mu&szlig; die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugef&uuml;hrt wird um so mehr produziert er von dem Rohstoff. Diese Geb&auml;ude lassen sich erweitern!', 'Arbeiterlager E', 'Das Arbeiterlager erzeugt aus der Energie der Kollektoren die jeweilige Ressource im bestimmten Verhältnis. Hat man mehrere Arbeiterläger so muß die Kollektorenenergie prozentual verteilt werden. Je mehr Energie einem Arbeiterlager zugeführt wird um so mehr produziert er von dem Rohstoff. Diese Gebäude lassen sich erweitern!', '', ''),
(18, 'Arbeitergrosslager M', 110000, 22000, 5500, 2200, 0, 32, 17930, '2;14', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'Arbeitergrosslager M', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(19, 'Arbeitergrosslager D', 130000, 32000, 8000, 3100, 0, 39, 23040, '2;15', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'Arbeitergrosslager D', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(20, 'Arbeitergrosslager I', 160000, 42500, 11500, 4150, 0, 46, 29610, '2;16', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'Arbeitergrosslager I', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(21, 'Arbeitergrosslager E', 185000, 55000, 14750, 5250, 0, 54, 36025, '2;17', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverh&auml;ltnis ist hier nat&uuml;rlich besser\r\n\r\noptimiert.', 'Arbeitergrosslager E', 'Das sind die Erweiterungen der Arbeiterlager. Das Produktionsverhältnis ist hier natürlich besser\r\n\r\noptimiert.', '', ''),
(22, 'Wabe der Hilfe', 16000, 4000, 0, 0, 0, 18, 2400, '1', 'Die Wabe der Hilfe ist f&uuml;r den Bau und die Verwaltung der planetaren Verteidigungsanlagen zust&auml;ndig.', 'Wabe der Hilfe', 'Die Wabe der Hilfe ist für den Bau und die Verwaltung der planetaren Verteidigungsanlagen zuständig.', '', ''),
(23, 'Sammlerwabe', 25000, 25000, 0, 0, 0, 30, 7500, '2;4;65', 'Die Sammlerwabe ist ein gigantischer orbitaler Transmitter, der in der Lage ist die Arbeiter zu empfangen, die von den Sammlern in K&auml;mpfen erbeutet und abgestrahlt werden.', 'Sammlerwabe', 'Die Sammlerwabe ist ein gigantischer orbitaler Transmitter, der in der Lage ist die Arbeiter zu empfangen, die von den Sammlern in Kämpfen erbeutet und abgestrahlt werden.', '', ''),
(24, 'Planetarer Panzer', 145000, 190000, 95000, 75000, 20, 94, 131000, '2;44', 'Die planetaren Verteidigungsanlagen f&uuml;hrten den Flotten der Angreifer meist unglaubliche Sch&auml;den zu, waren jedoch Schiffe in der Lage durch die R&auml;nge der verteidigenden Orbitalflotten und der Geschwader der Atmosph&auml;renj&auml;ger zu brechen, konnten sie die Planetenoberfl&auml;che mit gro&szlig;er Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen. \r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Geb&auml;ude der Planetenoberfl&auml;che viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Z’tha-ara bereit w&auml;ren eine enorme Menge von Ressourcen und Zeit f&uuml;r eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass eine Planetarer Panzer die besten Erfolgsquoten gegen planetengerichtete\r\n\r\nProjektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterst&uuml;tzt wurde.<br><br>\r\n\r\nDer Planetare Panzer verf&uuml;gt &uuml;ber eine Kombination von bodengest&uuml;tzen leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschie&szlig;en, sowie &uuml;ber eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schw&auml;chen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengest&uuml;tzten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden k&ouml;nnen. <br>', 'Planetarer Panzer', 'Die planetaren Verteidigungsanlagen führten den Flotten der Angreifer meist unglaubliche Schäden zu, waren jedoch Schiffe in der Lage durch die Ränge der verteidigenden Orbitalflotten und der Geschwader der Atmosphärenjäger zu brechen, konnten sie die Planetenoberfläche mit großer Leichtigkeit bombardieren und so die verwundbaren Verteidigungssysteme mit wenigen Schiffen unbrauchbar machen. \r\n<br><br>\r\nDie immer wiederkehrenden Bombardements zeigten, dass die Gebäude der Planetenoberfläche viel zu verwundbar sind, um sich auf einen einfachen Verteidigungsring der Orbitalflotten zu verlassen. <br>Mehr und mehr zeigte sich, dass die Z’tha-ara bereit wären eine enorme Menge von Ressourcen und Zeit für eine passive und sichere Verteidigung des Planeten aufzuwenden. Forschungen auf dem Bereich der Abwehrsysteme ergaben, dass eine Planetarer Panzer die besten Erfolgsquoten gegen planetengerichtete\r\n\r\nProjektil- und Strahlenwaffen hatte, besonders wenn er von orbitaler Verteidigung unterstützt wurde.<br><br>\r\n\r\nDer Planetare Panzer verfügt über eine Kombination von bodengestützen leichten \r\nLasern und Raketendrohnen, die in der Lage sind einen Grossteil der anfliegenden Raketen und Bomben abzuschießen, sowie über eine Reihe von schwachen bodennahen Ozonfeldern. <br>Diese Ozonfelder sind in der Lage jegliche Form von Wellen bis zu einem gewissen Grad zu absorbieren und schwächen somit die Energiewaffen weitestgehend ab.  <br>Um die Wirksamkeit der bodengestützten Strahlenwaffen zu garantieren sind diese Ozonfelder mit ionisiertem Helium durchsetzt, so dass kurzzeitige Fenster geschaffen werden können. <br>', '', ''),
(25, 'Efta-Projekt', 80000, 36000, 15000, 8000, 5, 48, 27900, '4;70;72;73;74', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es m&ouml;glich sein einen Cyborg zum Planeten Efta schicken zu k&ouml;nnen. Aufgrund der widrigen Umst&auml;nde ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit m&ouml;glich.', 'Efta-Projekt', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es möglich sein einen Cyborg zum Planeten Efta schicken zu können. Aufgrund der widrigen Umstände ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit möglich.', '', ''),
(26, 'Unendlichkeitssph&auml;re', 100000, 100000, 100000, 100000, 10, 96, 110000, '4;70', 'Unendlichkeitssph&auml;re', 'Unendlichkeitssphäre', 'Unendlichkeitssphäre', '', ''),
(27, 'Paleniumverst&auml;rker', 25000, 12000, 2000, 2000, 5, 32, 11300, '28', 'Der Paleniumverst&auml;rker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erh&ouml;hen.', 'Paleniumverstärker', 'Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen.', '', ''),
(28, 'Artefaktstock', 8000, 6000, 23000, 4000, 0, 26, 10500, '4', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschlie&szlig;t man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', 'Artefaktstock', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Mit diesem Geb&auml;ude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', '', ''),
(29, 'Missionsstock', 16000, 12000, 46000, 8000, 2, 30, 23000, '4', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', 'Missionsstock', 'Dieses Geb&auml;ude dient zur Koordinierung von Missionen.', '', ''),
(30, 'Planetare Panzererweiterung', 50000, 55000, 450000, 75000, 25, 128, 206000, '2;24;46', 'Diese Erweiterung sorgt daf&uuml;r, dass die Wirkung von EMP-Waffen auf T&uuml;rme zu einem gewissen Teil absorbiert werden kann.', 'Planetare Panzererweiterung', 'Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.', '', ''),
(40, 'Exoskelett', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Das Exoskelett ist f&uuml;r J&auml;ger.', 'Exoskelett', 'Schützt Schiffe vor physischen und Energieangriffen. Das Exoskelett ist für Jäger.', '', ''),
(41, 'Chitinbeschichtung', 8000, 2000, 0, 0, 0, 12, 1200, '40', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Die Chitinbeschichtung ist f&uuml;r Jagdboote.', 'Chitinbeschichtung', 'Schützt Schiffe vor physischen und Energieangriffen. Die Chitinbeschichtung ist für Jagdboote.', '', ''),
(42, 'Doppelchitinbeschichtung', 18000, 4500, 1125, 0, 0, 27, 3038, '41', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Die Doppelchitinbeschichtung ist f&uuml;r Zerst&ouml;rer.', 'Doppelchitinbeschichtung', 'Schützt Schiffe vor physischen und Energieangriffen. Die Doppelchitinbeschichtung ist für Zerstörer.', '', ''),
(43, 'Chitinskelett', 33000, 8250, 2100, 1100, 0, 50, 6020, '42', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Das Chitinskelett ist f&uuml;r Kreuzer.', 'Chitinskelett', 'Schützt Schiffe vor physischen und Energieangriffen. Das Chitinskelett ist für Kreuzer.', '', ''),
(44, 'Chitinpanzer', 64000, 16000, 4000, 2000, 0, 96, 11600, '43', 'Sch&uuml;tzt Schiffe vor physischen und Energieangriffen. Wegen ihrer Gr&ouml;&szlig;e ben&ouml;tigen die Schlachtschiffe diesen besonderen Chitinpanzer.', 'Chitinpanzer', 'Schützt Schiffe vor physischen und Energieangriffen. Wegen ihrer Größe benötigen die Schlachtschiffe diesen besonderen Chitinpanzer.', '', ''),
(45, 'Stachel', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Eine Art \"Laser\" die leichten Schaden verursachen.', 'Stachel', 'Eine Art \"Laser\" die leichten Schaden verursachen.', '', ''),
(46, 'Giftstachel', 8000, 2000, 0, 0, 0, 12, 1200, '45', 'Mit dieser Waffe ist es dem Jagdboot m&ouml;glich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zuk&auml;mpfen.', 'Giftstachel', 'Mit dieser Waffe ist es dem Jagdboot möglich Schiffe lahm zu legen! Diese Schiffe sind nicht mehr in der Lage zukämpfen.', '', ''),
(47, 'Grosser Stachel', 18000, 4500, 1125, 0, 0, 27, 3038, '46', 'Ist eine Art \"Automatikkanone\", die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.', 'Grosser Stachel', 'Ist eine Art \"Automatikkanone\", die ein Hochgeschwindigkeitsprojektil abfeuert und panzerbrechend wirkt.', '', ''),
(48, 'S&auml;urestachel', 33000, 8500, 2100, 1000, 0, 50, 6030, '47', 'Der S&auml;urestachel feuert einen Plasmaenergiesto&szlig; ab, der gro&szlig;en Schaden anrichtet.', 'Säurestachel', 'Der Säurestachel feuert einen Plasmaenergiestoß ab, der großen Schaden anrichtet.', '', ''),
(49, 'Feuerstachel', 64000, 16000, 4000, 2000, 0, 96, 11600, '48', 'Der Feuerstachel ist die st&auml;rkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem geb&uuml;ndeltem Energiestrahl ab. Wegen ihrer Gr&ouml;&szlig;e sind solche Kanonen nur bei Schlachtschiffen installiert.', 'Feuerstachel', 'Der Feuerstachel ist die stärkste bekannte Strahlenwaffe. Sie feuert einen Materiestrom in einem gebündeltem Energiestrahl ab. Wegen ihrer Größe sind solche Kanonen nur bei Schlachtschiffen installiert.', '', ''),
(50, 'Speicheldr&uuml;sen', 6000, 2000, 0, 0, 0, 9, 1000, '0', 'Die Forschung im Bereich der Speicheldr&uuml;sen schafft die Basis f&uuml;r \"Raketen\", die sich im Weltraum selbstst&auml;ndig bewegen.', 'Speicheldrüsen', 'Die Forschung im Bereich der Speicheldrüsen schafft die Basis für \"Raketen\", die sich im Weltraum selbstständig bewegen.', '', ''),
(51, 'Nadelspeichel', 4000, 1000, 0, 0, 0, 6, 600, '50', 'Der Nadelspeichel ist die einfachste \"Rakete\", die es gibt. Sie wird bevorzugt in Speichelbatterien verbaut und dient zur \"Luftabwehr\".', 'Nadelspeichel', 'Der Nadelspeichel ist die einfachste \"Rakete\", die es gibt. Sie wird bevorzugt in Speichelbatterien verbaut und dient zur \"Luftabwehr\".', '', ''),
(52, 'Stachelspeichel', 8000, 2000, 0, 0, 0, 12, 1200, '51', 'Ist ein Energiegeschoss, das mit &Uuml;berlichtgeschwindigkeit sein Ziel trifft.', 'Stachelspeichel', 'Ist ein Energiegeschoss, das mit Überlichtgeschwindigkeit sein Ziel trifft.', '', ''),
(53, 'Giftspeichel', 18000, 4500, 1125, 0, 0, 27, 3038, '52', 'Giftspeichel sind eigentlich dicke, globige \"Raketen\", die sich auch als Bomben verwenden lassen.', 'Giftspeichel', 'Giftspeichel sind eigentlich dicke, globige \"Raketen\", die sich auch als Bomben verwenden lassen.', '', ''),
(54, 'S&auml;urespeichel', 33000, 8250, 2000, 1000, 0, 50, 5950, '53', 'Diese \"Rakete\" besteht aus Antimateriekugeln, die beim Aufschlag detonieren.', 'Säurespeichel', 'Diese \"Rakete\" besteht aus Antimateriekugeln, die beim Aufschlag detonieren.', '', ''),
(55, 'Larvenstadium', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Larvenstadium', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(56, 'Bienenstadium', 8000, 2000, 0, 0, 0, 12, 1200, '55', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Bienenstadium', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(57, 'Drohnenstadium', 18000, 4500, 1125, 0, 0, 27, 3038, '56', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Drohnenstadium', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(58, 'Wespenstadium', 33000, 8250, 2000, 1000, 0, 50, 5950, '57', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Wespenstadium', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(59, 'Hummelstadium', 64000, 16000, 4000, 2000, 0, 96, 11600, '58', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', 'Hummelstadium', 'Das Chassis ist der Rumpf des Schiffes, an dem Bewaffnung, Antrieb und Schilde montiert werden.', '', ''),
(60, 'Fl&uuml;gel', 4000, 1000, 0, 0, 0, 6, 600, '0', 'Kleiner kompakter Antrieb, genau richtig f&uuml;r J&auml;ger.', 'Flügel', 'Kleiner kompakter Antrieb, genau richtig für Jäger.', '', ''),
(61, 'Strukturfl&uuml;gel', 8000, 2000, 0, 0, 0, 12, 1200, '60', 'Der Strukturfl&uuml;gel beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.', 'Strukturflügel', 'Der Strukturflügel beschleunigt die Schiffe der Jagdbootklasse auf eine erstaunliche Geschwindigkeit.', '', ''),
(62, 'Chitinfl&uuml;gel', 8000, 2000, 500, 0, 0, 12, 1350, '61', 'Nur dem Chitinfl&uuml;gel ist es zu verdanken, da&szlig; sich der Zerst&ouml;rer als wendiges Schiff erweist.', 'Chitinflügel', 'Nur dem Chitinflügel ist es zu verdanken, daß sich der Zerstörer als wendiges Schiff erweist.', '', ''),
(63, 'Raumfl&uuml;gel', 30000, 7750, 1800, 900, 0, 42, 5450, '62', 'Der Kreuzer wird durch einen Raumfl&uuml;gel bewegt, jedoch ist der Antrieb deutlich\r\n\r\nschwerf&auml;lliger.', 'Raumflügel', 'Der Kreuzer wird durch einen Raumflügel bewegt, jedoch ist der Antrieb deutlich\r\n\r\nschwerfälliger.', '', ''),
(64, 'Fl&uuml;gelsynchronisation', 64000, 16000, 4000, 2000, 0, 96, 11600, '63', 'Die Fl&uuml;gelsynchronisation hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.', 'Flügelsynchronisation', 'Die Flügelsynchronisation hat die Aufgabe die gigantische Masse eines Schlachtschiffs zu bewegen.', '', ''),
(65, 'Transwabenfeld', 6000, 1500, 0, 0, 0, 6, 900, '0', 'Durch das Transwabenfeld wird ein eroberter Arbeiter zu deinem Planeten gebracht.', 'Transwabenfeld', 'Durch das Transwabenfeld wird ein eroberter Arbeiter zu deinem Planeten gebracht.', '', ''),
(66, 'Schwarzfeld', 5000, 2000, 0, 0, 0, 9, 900, '9', 'Das Schwarzfeld verhindert die Entdeckung der Tunnellarve und Kundschafter. Jedenfalls meistens!', 'Schwarzfeld', 'Das Schwarzfeld verhindert die Entdeckung der Tunnellarve und Kundschafter. Jedenfalls meistens!', '', ''),
(67, 'Stachelkanal', 4000, 1000, 0, 0, 0, 9, 600, '22', 'Der Stachelkanal ist ein Multiplex-Sockel der mit verschiedenen Waffenk&ouml;pfen besetzt werden kann.', 'Stachelkanal', 'Der Stachelkanal ist ein Multiplex-Sockel der mit verschiedenen Waffenköpfen besetzt werden kann.', '', ''),
(68, 'Larventaschen', 16000, 4000, 1000, 0, 0, 24, 2700, '58', 'Erm&ouml;glicht es 25 J&auml;ger zu transportieren und zu\r\n\r\nversorgen.', 'Larventaschen', 'Ermöglicht es 25 Jäger zu transportieren und zu\r\n\r\nversorgen.', '', ''),
(69, 'Doppelte Larventaschen', 25000, 4500, 2000, 2000, 0, 37, 4800, '59;68', 'Erm&ouml;glicht es 50 J&auml;ger zu transportieren und zu versorgen.', 'Doppelte Larventaschen', 'Ermöglicht es 50 Jäger zu transportieren und zu versorgen.', '', ''),
(70, 'Virtuelles Transmitterfeld', 50000, 20000, 6000, 4000, 1, 72, 13400, '65', 'Erm&ouml;glicht Transmitterverbindungen auch au&szlig;erhalb des normalen Transmittersystems, jedoch unter hohem Risiko f&uuml;r den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', 'Virtuelles Transmitterfeld', 'Ermöglicht Transmitterverbindungen auch außerhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', '', ''),
(71, 'Cyborggrundlagen', 20000, 20000, 10000, 8000, 0, 36, 12200, '8', 'Erm&ouml;glicht die Entwicklung von Cyborgtechnologien.', 'Cyborggrundlagen', 'Ermöglicht die Entwicklung von Cyborgtechnologien.', '', ''),
(72, 'Cyborginkubationstank', 10000, 5000, 1000, 2000, 0, 24, 3100, '71', 'Dient zur Z&uuml;chtung des biologischen Cyborggewebes.', 'Cyborginkubationstank', 'Dient zur Züchtung des biologischen Cyborggewebes.', '', ''),
(73, 'Cyborgsteuerungskristall', 5000, 60000, 2000, 2000, 0, 54, 13900, '71', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsf&auml;hig.', 'Cyborgsteuerungskristall', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.', '', ''),
(74, 'Cyborgimplantate', 10000, 6000, 10000, 7000, 0, 36, 8000, '71', 'Sie bilden das Grundger&uuml;st des Cyborgs.', 'Cyborgimplantate', 'Sie bilden das Grundgerüst des Cyborgs.', '', ''),
(75, 'Frequenzzusatzwabe', 500000, 100000, 50000, 20000, 75, 144, 168000, '5', 'Der Sprungfeldfrequenzmodulator erm&ouml;glicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen au&szlig;er Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.', 'Frequenzzusatzwabe', 'Der Sprungfeldfrequenzmodulator ermöglicht es die Frequenz eines feindlichen SFB zu bestimmen, um diesen außer Kraft zu setzen. Dazu werden die Antriebe der Raumschiffe an die Frequenzen des feindlichen SFB angepasst.', '', ''),
(80, 'Arbeiter', 1000, 100, 0, 0, 0, 4, 120, '7', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung f&uuml;r Kollektoren f&uuml;hrt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann f&uuml;r den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', 'Arbeiter', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', '', ''),
(81, 'Wespe', 500, 150, 0, 0, 0, 6, 80, '13;40;45;55;60', '(J&auml;ger-Klasse) Der \"Wespe\"-J&auml;ger ist ein kleines wendiges Kampfschiff, das es zu allererst auf die Transmitterschiffe und J&auml;ger des Gegners abgesehen hat.<br>Der wendige J&auml;ger ist nur mit einem kleinen Antrieb ausger&uuml;stet und kann weite Strecken nur sehr langsam zur&uuml;cklegen.<br>Daher werden Wespen meist in Verbindung mit Kreuzern oder Schlachtschiffen eingesetzt, die ihnen durch die Technologie der J&auml;gerbuchten als Tr&auml;gerschiffe dienen k&ouml;nnen.', 'Wespe', '(Jäger-Klasse) Der \"Wespe\"-Jäger ist ein kleines wendiges Kampfschiff, das es zu allererst auf die Transmitterschiffe und Jäger des Gegners abgesehen hat.<br>Der wendige Jäger ist nur mit einem kleinen Antrieb ausgerüstet und kann weite Strecken nur sehr langsam zurücklegen.<br>Daher werden Wespen meist in Verbindung mit Kreuzern oder Schlachtschiffen eingesetzt, die ihnen durch die Technologie der Jägerbuchten als Trägerschiffe dienen können.', '', ''),
(82, 'Feuerskorpion', 3000, 1000, 0, 0, 0, 14, 500, '13;41;45;46;56;61', '(Jagdboot-Klasse) Der Feuerskorpion hat als Hauptwaffe einen Giftstachel, der gegnerische Schiffe au&szlig;er Gefecht setzen k&ouml;nnen egal wie gro&szlig; das Schiff ist. Je gr&ouml;&szlig;er aber das gegnerische Schiff um so mehr Treffer ben&ouml;tigt aber der Feuerskorpion um es zu l&auml;hmen.', 'Feuerskorpion', '(Jagdboot-Klasse) Der Feuerskorpion hat als Hauptwaffe einen Giftstachel, der gegnerische Schiffe außer Gefecht setzen können egal wie groß das Schiff ist. Je größer aber das gegnerische Schiff um so mehr Treffer benötigt aber der Feuerskorpion um es zu lähmen.', '', ''),
(83, 'Geisterschrecke', 10000, 5000, 2000, 500, 0, 30, 2800, '13;42;47;51;57;62', '(Zerst&ouml;rer-Klasse) Die Geisterschrecke ist ein wendiges Gro&szlig;kampfschiff und am besten geeignet Jagdboote in die ewigen Jagdgr&uuml;nde zu schicken. Aber auch gegen Kreuzer und Schlachtschiffe macht sich die Geisterschrecke ganz gut.', 'Geisterschrecke', '(Zerstörer-Klasse) Die Geisterschrecke ist ein wendiges Großkampfschiff und am besten geeignet Jagdboote in die ewigen Jagdgründe zu schicken. Aber auch gegen Kreuzer und Schlachtschiffe macht sich die Geisterschrecke ganz gut.', '', ''),
(84, 'Skarab&auml;us', 25000, 10000, 1000, 500, 0, 60, 5000, '13;43;48;52;58;63;68', '(Kreuzer-Klasse) Der Skarab&auml;us ist der kleine Bruder des Schlachtschiffes. Er ist st&auml;rker bewaffnet als ein Zerst&ouml;rer und konzentriert sich haupts&auml;chlich auf gr&ouml;&szlig;ere Schiffe. Au&szlig;erdem besitzt er eine Ladekapazit&auml;t f&uuml;r 30 J&auml;ger.', 'Skarabäus', '(Kreuzer-Klasse) Der Skarabäus ist der kleine Bruder des Schlachtschiffes. Er ist stärker bewaffnet als ein Zerstörer und konzentriert sich hauptsächlich auf größere Schiffe. Außerdem besitzt er eine Ladekapazität für 30 Jäger.', '', ''),
(85, 'Mantis', 50000, 15000, 3000, 3000, 2, 90, 12100, '13;44;49;53;54;59;70;64;69', '(Schlachtschiff-Klasse) Die Mantis gilt als K&ouml;nig der Raumschlacht. Mit dem gro&szlig;en Waffenarsenal k&auml;mpft es vor allem gegen seinesgleichen, jedoch sind auch Kreuzer und Zerst&ouml;rer willkommene Opfer. Au&szlig;erdem besitzt es eine Ladekapazit&auml;t f&uuml;r 100 J&auml;ger.', 'Mantis', '(Schlachtschiff-Klasse) Die Mantis gilt als König der Raumschlacht. Mit dem großen Waffenarsenal kämpft es vor allem gegen seinesgleichen, jedoch sind auch Kreuzer und Zerstörer willkommene Opfer. Außerdem besitzt es eine Ladekapazität für 100 Jäger.', '', ''),
(86, 'H&ouml;llenk&auml;fer', 1000, 750, 0, 0, 0, 8, 250, '13;41;53;56;61', '(Jagdboot-Klasse) Der H&ouml;llenk&auml;fer wurde speziell dazu entwickelt eine massive planetare Verteidigung zu durchbrechen und sie evtl. auszuschalten. Er ist sehr wirksam gegen planetare Gesch&uuml;tze, aber wegen der hohen Bombenlast nicht geeignet im Raumkampf.', 'Höllenkäfer', '(Jagdboot-Klasse) Der Höllenkäfer wurde speziell dazu entwickelt eine massive planetare Verteidigung zu durchbrechen und sie evtl. auszuschalten. Er ist sehr wirksam gegen planetare Geschütze, aber wegen der hohen Bombenlast nicht geeignet im Raumkampf.', '', ''),
(87, 'Sammler', 2000, 1000, 0, 0, 0, 6, 400, '13;23;56;61;65', '(Transmitterschiff) Sammler, auch als Arbeiterpirat bekannt. Mit Hilfe dieser Schiffe werden die Arbeiter zu dem eigenen Planeten gebracht. Die ben&ouml;tigte Energie zum Transfer mittels Transwabenstrahl ist jedoch so hoch, da&szlig; das Schiff durch den Transfer zerst&ouml;rt wird.', 'Sammler', '(Transmitterschiff) Sammler, auch als Arbeiterpirat bekannt. Mit Hilfe dieser Schiffe werden die Arbeiter zu dem eigenen Planeten gebracht. Die benötigte Energie zum Transfer mittels Transwabenstrahl ist jedoch so hoch, daß das Schiff durch den Transfer zerstört wird.', '', ''),
(88, 'Ekelbr&uuml;ter', 50000, 25000, 7000, 6000, 1, 76, 15500, '13;44;45;59;64;69', '(Schlachtschiff-Klasse) Der Ekelbr&uuml;ter ist ein Tr&auml;gerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um gro&szlig;e Mengen J&auml;ger transportieren zu k&ouml;nnen wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste daf&uuml;r die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen J&auml;gern wieder wettmachen.', 'Ekelbrüter', '(Schlachtschiff-Klasse) Der Ekelbrüter ist ein Trägerschiff, das aus der Basis des Schlachtschiffs entwickelt wurde. Um große Mengen Jäger transportieren zu können wurden riesige Hangars in den Rumpf eingebaut. Jedoch musste dafür die Feuerkraft erheblich reduziert werden, was jedoch Hunderte von kleinen Jägern wieder wettmachen.', '', ''),
(100, 'Larvenstock', 5000, 1500, 0, 0, 0, 6, 800, '13;22', 'Der Larvenstock beherbergt mehrere J&auml;ger. Jedoch sind diese J&auml;ger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden k&ouml;nnen.', 'Larvenstock', 'Der Larvenstock beherbergt mehrere Jäger. Jedoch sind diese Jäger nur defensive Einheiten, die nur zur Verteidigung der Kollektoren verwendet werden können.', '', ''),
(101, 'Speichelbatterie', 600, 400, 100, 0, 0, 20, 170, '22;51;67', 'Die Speichelbatterie ist mit haufenweise Nadelspeichel best&uuml;ckt und dient zur Abwehr von Jagdbooten und Transmitterschiffen.', 'Speichelbatterie', 'Die Speichelbatterie ist mit haufenweise Nadelspeichel bestückt und dient zur Abwehr von Jagdbooten und Transmitterschiffen.', '', ''),
(102, 'Bodenstachel', 300, 300, 0, 0, 0, 10, 90, '22;45;67', 'Der Bodenstachel ist der leichteste Gesch&uuml;tzturm und hauptsachlich zur Abwehr von J&auml;gern geeignet.', 'Bodenstachel', 'Der Bodenstachel ist der leichteste Geschützturm und hauptsachlich zur Abwehr von Jägern geeignet.', '', ''),
(103, 'Giftstachelbatterie', 2000, 400, 50, 0, 0, 28, 295, '22;47;67', 'Diese gro&szlig;kalibrige Kanone ist der wahre Panzerbrecher und vernichtet Zerst&ouml;rer und Kreuzer massenweise.', 'Giftstachelbatterie', 'Diese großkalibrige Kanone ist der wahre Panzerbrecher und vernichtet Zerstörer und Kreuzer massenweise.', '', ''),
(104, 'Feuerstachelbatterie', 1000, 1500, 100, 250, 0, 48, 530, '22;48;67', 'Die Feuerstachelbatterie ist der Turm der jedem Schlachtschiff Respekt abverlangt und f&uuml;r klare Verh&auml;ltnisse sorgt.', 'Feuerstachelbatterie', 'Die Feuerstachelbatterie ist der Turm der jedem Schlachtschiff Respekt abverlangt und für klare Verhältnisse sorgt.', '', ''),
(110, 'Kundschafter', 500, 500, 0, 0, 0, 2, 150, '9;13;62;66', 'Sonde:<br><br>\r\n\r\nDer Kundschafter ist neben dem Wandellarve die 2. M&ouml;glichkeit, um andere Spieler auszukundschaften.<br>\r\n\r\nSie scant nur oberfl&auml;chlich die Eigenschaften ihres Ziels, genauere Informationen beschafft die Wandellarve wesentlich besser.<br><br>\r\n\r\nDa der Kundschafter mit enorm hohen Geschwindigkeiten fliegen soll, &uuml;berlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus. \r\nSonden kehren nie zur&uuml;ck.<br><br>\r\n\r\nKundschafter k&ouml;nnen von den feindlichen Scannern entdeckt werden, abh&auml;ngig vom Typ des Scanners.', 'Kundschafter', 'Sonde:<br><br>\r\n\r\nDer Kundschafter ist neben dem Wandellarve die 2. Möglichkeit, um andere Spieler auszukundschaften.<br>\r\n\r\nSie scant nur oberflächlich die Eigenschaften ihres Ziels, genauere Informationen beschafft die Wandellarve wesentlich besser.<br><br>\r\n\r\nDa der Kundschafter mit enorm hohen Geschwindigkeiten fliegen soll, überlastet sich nach dem Start der Antrieb und brennt bis zur maximalen Geschwindigkeit aus. \r\nSonden kehren nie zurück.<br><br>\r\n\r\nKundschafter können von den feindlichen Scannern entdeckt werden, abhängig vom Typ des Scanners.', '', ''),
(111, 'Wandellarve', 500, 500, 200, 100, 0, 8, 250, '66', 'Die Wandellarve / Geheimagent:<br><br>\r\n\r\nDie Wandellarve ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihr kann man &uuml;ber jeden beliebigen Spieler so ziemlich alles herausfinden.', 'Wandellarve', 'Die Wandellarve / Geheimagent:<br><br>\r\n\r\nDie Wandellarve ist eine der wichtigsten Einheiten des Spiels.\r\nMit ihr kann man über jeden beliebigen Spieler so ziemlich alles herausfinden.', '', ''),
(120, 'Sektorraumbasis', 7500000, 3750000, 750000, 375000, 250, 192, 2125000, '0', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und\r\n\r\nWohn- und Arbeitst&auml;tte f&uuml;r alle Bewohner der Sektors sein kann. \r\n<br>Vornehmlich dient sie als Navigations- und Handelsst&uuml;tzpunkt, kann aber durch Erweiterung auch als Flottenbasis f&uuml;r die Sektorflotte dienen. \r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektobasis ist aufgrund der unglaublichen Gr&ouml;&szlig;e sehr teuer in der Konstruktion, besonders Tronic wird f&uuml;r eine derartig gro&szlig;e Konstruktion viel ben&ouml;tigt.', 'Sektorraumbasis', 'Die Sektorraumbasis ist eine riesige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und\r\n\r\nWohn- und Arbeitstätte für alle Bewohner der Sektors sein kann. \r\n<br>Vornehmlich dient sie als Navigations- und Handelsstützpunkt, kann aber durch Erweiterung auch als Flottenbasis für die Sektorflotte dienen. \r\n<br>In Ihrer freien Sektion in der Mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektobasis ist aufgrund der unglaublichen Größe sehr teuer in der Konstruktion, besonders Tronic wird für eine derartig große Konstruktion viel benötigt.', '', ''),
(121, 'Sektorsprungfeldbegrenzer', 1500000, 750000, 375000, 150000, 150, 144, 622500, '120', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', 'Sektorsprungfeldbegrenzer', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', '', ''),
(122, 'Sektorraumwerft', 750000, 750000, 375000, 375000, 60, 144, 547500, '120', 'In der Sektorraumwerft k&ouml;nnen Schiffe f&uuml;r die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', 'Sektorraumwerft', 'In der Sektorraumwerft können Schiffe für die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', '', ''),
(123, 'Sektorhandelszentrum', 150000, 75000, 75000, 150000, 30, 96, 142500, '120', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', 'Sektorhandelszentrum', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', '', ''),
(124, 'Scannerphalanx', 600000, 1500000, 250000, 750000, 110, 96, 845000, '120', 'Die Scannerphalanx erm&ouml;glicht das Scannen von Sektorraumbasen und deren Flottenst&auml;rke.', 'Scannerphalanx', 'Die Scannerphalanx ermöglicht das Scannen von Sektorraumbasen und deren Flottenstärke.', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_tech_data5`
--

CREATE TABLE `de_tech_data5` (
  `tech_id` int(11) NOT NULL DEFAULT '0',
  `tech_name` varchar(40) NOT NULL DEFAULT '',
  `restyp01` int(11) NOT NULL DEFAULT '0',
  `restyp02` int(11) NOT NULL DEFAULT '0',
  `restyp03` int(11) NOT NULL DEFAULT '0',
  `restyp04` int(11) NOT NULL DEFAULT '0',
  `restyp05` int(11) NOT NULL DEFAULT '0',
  `tech_ticks` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `tech_vor` varchar(40) NOT NULL DEFAULT '',
  `des` text NOT NULL,
  `tech_name1` varchar(40) NOT NULL DEFAULT '',
  `des1` text NOT NULL,
  `tech_name2` varchar(40) NOT NULL DEFAULT '',
  `des2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `de_tech_data5`
--

INSERT INTO `de_tech_data5` (`tech_id`, `tech_name`, `restyp01`, `restyp02`, `restyp03`, `restyp04`, `restyp05`, `tech_ticks`, `score`, `tech_vor`, `des`, `tech_name1`, `des1`, `tech_name2`, `des2`) VALUES
(1, 'Replikator', 4000, 1000, 0, 0, 0, 4, 600, '0', 'platzhalter', 'Replikator', 'platzhalter', '', ''),
(2, 'Replikator-Xx', 100000, 10000, 8000, 1000, 0, 32, 14800, '1', 'platzhalter', 'Replikator-Xx', 'platzhalter', '', ''),
(3, 'Transfermatrix', 8000, 2000, 0, 0, 0, 8, 1200, '65', 'platzhalter', 'Transfermatrix', 'platzhalter', '', ''),
(4, 'Unimatrix', 40000, 10000, 0, 0, 0, 16, 6000, '2;3', 'platzhalter', 'Unimatrix', 'platzhalter', '', ''),
(5, 'EMP-Dominator', 200000, 200000, 80000, 40000, 75, 72, 107500, '2;63', 'platzhalter', 'EMP-Dominator', 'platzhalter', '', ''),
(6, 'Rekonverter', 120000, 80000, 20000, 5000, 0, 64, 36000, '2;65', 'platzhalter', 'Rekonverter', 'platzhalter', '', ''),
(7, 'AssimFab-DX', 6000, 1000, 0, 0, 0, 8, 800, '1', 'platzhalter', 'AssimFab-DX', 'platzhalter', '', ''),
(8, 'Kompilator', 12000, 3000, 0, 0, 0, 16, 1800, '1', 'platzhalter', 'Kompilator', 'platzhalter', '', ''),
(9, 'Inputanalysator', 16000, 4000, 0, 0, 0, 12, 2400, '1', 'platzhalter', 'Inputanalysator', 'platzhalter', '', ''),
(10, 'RWS-Nexus-DX0A', 4000, 1000, 0, 0, 0, 8, 600, '1', 'platzhalter', 'RWS-Nexus-DX0A', 'platzhalter', '', ''),
(11, 'RWS-Nexus-DX0B', 20000, 5000, 1000, 0, 0, 16, 3300, '2;10', 'platzhalter', 'RWS-Nexus-DX0B', 'platzhalter', '', ''),
(12, 'RWS-Nexus-DX0C', 100000, 40000, 4000, 1000, 0, 32, 19600, '11', 'platzhalter', 'RWS-Nexus-DX0C', 'platzhalter', '', ''),
(13, 'Dualassambler', 10000, 2000, 0, 0, 0, 12, 1400, '1', 'platzhalter', 'Dualassambler', 'platzhalter', '', ''),
(14, 'Manipulator M', 10000, 1000, 0, 0, 0, 4, 1200, '1', 'platzhalter', 'Manipulator M', 'platzhalter', '', ''),
(15, 'Manipulator D', 12500, 2000, 0, 0, 0, 8, 1650, '14', 'platzhalter', 'Manipulator D', 'platzhalter', '', ''),
(16, 'Manipulator I', 15000, 3000, 0, 0, 0, 12, 2100, '15', 'platzhalter', 'Manipulator I', 'platzhalter', '', ''),
(17, 'Manipulator E', 17500, 4000, 0, 0, 0, 16, 2550, '16', 'platzhalter', 'Manipulator E', 'platzhalter', '', ''),
(18, 'Extrem-Manipulator M', 100000, 20000, 5000, 2000, 0, 12, 16300, '2;14', 'platzhalter', 'Extrem-Manipulator M', 'platzhalter', '', ''),
(19, 'Extrem-Manipulator D', 125000, 30000, 8000, 3000, 0, 16, 22100, '2;15', 'platzhalter', 'Extrem-Manipulator D', 'platzhalter', '', ''),
(20, 'Extrem-Manipulator I', 150000, 40000, 11000, 4000, 0, 20, 27900, '2;16', 'platzhalter', 'Extrem-Manipulator I', 'platzhalter', '', ''),
(21, 'Extrem-Manipulator E', 175000, 50000, 14000, 5000, 0, 24, 33700, '2;17', 'platzhalter', 'Extrem-Manipulator E', 'platzhalter', '', ''),
(22, 'Kontrastyx', 16000, 4000, 0, 0, 0, 12, 2400, '1', 'platzhalter', 'Kontrastyx', 'platzhalter', '', ''),
(23, 'Assimkollektor', 20000, 20000, 0, 0, 0, 16, 6000, '2;4;65', 'platzhalter', 'Assimkollektor', 'platzhalter', '', ''),
(24, 'Lurcrefelktor', 150000, 200000, 100000, 80000, 20, 96, 119000, '2;44', 'platzhalter', 'Lurcrefelktor', 'platzhalter', '', ''),
(25, 'Por-Zi-Eftox', 80000, 36000, 15000, 8000, 5, 48, 23400, '4;70;72;73;74', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es m&ouml;glich sein einen Cyborg zum Planeten Efta schicken zu k&ouml;nnen. Aufgrund der widrigen Umst&auml;nde ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit m&ouml;glich.', 'Por-Zi-Eftox', 'Das Efta-Projekt erweitert die Weltraumhandelsgilde um die Technologie der virtuellen Transmitterfelder. So sollte es möglich sein einen Cyborg zum Planeten Efta schicken zu können. Aufgrund der widrigen Umstände ist nur immer die Steuerung eines Cyborgs zur gleichen Zeit möglich.', '', ''),
(26, 'DX-Warp-Bi-Tri', 100000, 100000, 100000, 100000, 10, 96, 101000, '4;70', 'platzhalter', 'DX-Warp-Bi-Tri', 'platzhalter', '', ''),
(27, 'Paleniumverst&auml;rker', 25000, 12000, 2000, 2000, 5, 32, 6800, '25', 'Der Paleniumverst&auml;rker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erh&ouml;hen. Palenium tritt in verst&auml;rkter Konzentration in EFTA auf.', 'Paleniumverstärker', 'Der Paleniumverstärker nutzt das seltene Element Palenium um den Energieoutput der Kollektoren zu erhöhen. Palenium tritt in verstärkter Konzentration in EFTA auf.', '', ''),
(28, 'Artefakt-JD-BED', 11000, 4500, 26000, 2500, 0, 10, 10800, '4', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Die Artefakte k&ouml;nnen &uuml;ber den Handel, oder &uuml;ber EFTA bezogen werden. Mit diesem Geb&auml;ude erschlie&szlig;t man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', 'Artefakt-JD-BED', 'Dieses Geb&auml;ude dient der Aufbewahrung und Veredelung von Artefakten. Die Artefakte k&ouml;nnen über den Handel, oder über EFTA bezogen werden. Mit diesem Geb&auml;ude erschließt man sich die Errungenschaften der Erbauer und kommt der Ewigkeit einen Schritt n&auml;her.', '', ''),
(29, 'Arch&auml;ologie-KT-TCK', 22000, 9000, 52000, 5000, 2, 10, 21800, '4', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.', 'Archäologie-KT-TCK', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.', '', ''),
(30, 'Lurcrefelktor Exalt', 50000, 80000, 400000, 100000, 25, 128, 183500, '2;24;46', 'Diese Erweiterung sorgt daf&uuml;r, dass die Wirkung von EMP-Waffen auf T&uuml;rme zu einem gewissen Teil absorbiert werden kann.', 'Lurcrefelktor Exalt', 'Diese Erweiterung sorgt dafür, dass die Wirkung von EMP-Waffen auf Türme zu einem gewissen Teil absorbiert werden kann.', '', ''),
(40, 'DX-FW-Schild 0A', 4000, 1000, 0, 0, 0, 1, 600, '0', 'platzhalter', 'DX-FW-Schild 0A', 'platzhalter', '', ''),
(41, 'DX-FW-Schild 0B', 8000, 2000, 0, 0, 0, 3, 1200, '40', 'platzhalter', 'DX-FW-Schild 0B', 'platzhalter', '', ''),
(42, 'DX-FW-Schild 0C', 16000, 4000, 1000, 0, 0, 5, 2700, '41', 'platzhalter', 'DX-FW-Schild 0C', 'platzhalter', '', ''),
(43, 'DX-FW-Schild 0D', 32000, 8000, 2000, 1000, 0, 11, 5800, '42', 'platzhalter', 'DX-FW-Schild 0D', 'platzhalter', '', ''),
(44, 'DX-FW-Masterschild', 64000, 16000, 4000, 2000, 0, 21, 11600, '43', 'platzhalter', 'DX-FW-Masterschild', 'platzhalter', '', ''),
(45, 'X-Magma', 4000, 1000, 0, 0, 0, 1, 600, '0', 'platzhalter', 'X-Magma', 'platzhalter', '', ''),
(46, 'EMP-Kanone', 8000, 2000, 0, 0, 0, 3, 1200, '45', 'platzhalter', 'EMP-Kanone', 'platzhalter', '', ''),
(47, 'Zermalmer', 16000, 4000, 1000, 0, 0, 5, 2700, '46', 'platzhalter', 'Zermalmer', 'platzhalter', '', ''),
(48, 'ER-Plasmawerfer', 32000, 8000, 2000, 1000, 0, 11, 5800, '47', 'platzhalter', 'ER-Plasmawerfer', 'platzhalter', '', ''),
(49, 'Sternenneuralisator', 64000, 16000, 4000, 2000, 0, 21, 11600, '48', 'platzhalter', 'Sternenneuralisator', 'platzhalter', '', ''),
(50, 'A01-A5-Lafette', 6000, 2000, 0, 0, 0, 2, 1000, '0', 'platzhalter', 'A01-A5-Lafette', 'platzhalter', '', ''),
(51, 'ZoM-DX-Kopf', 4000, 1000, 0, 0, 0, 1, 600, '50', 'platzhalter', 'ZoM-DX-Kopf', 'platzhalter', '', ''),
(52, 'itri-03-DX-Kopf', 8000, 2000, 0, 0, 0, 3, 1200, '51', 'platzhalter', 'itri-03-DX-Kopf', 'platzhalter', '', ''),
(53, 'Ye-0B-DX-Kopf', 16000, 4000, 1000, 0, 0, 5, 2700, '52', 'platzhalter', 'Ye-0B-DX-Kopf', 'platzhalter', '', ''),
(54, 'Ultra-Ra-DX-Kopf', 32000, 8000, 2000, 1000, 0, 11, 5800, '53', 'platzhalter', 'Ultra-Ra-DX-Kopf', 'platzhalter', '', ''),
(55, 'Xinth-Xoc-Konstrukt', 4000, 1000, 0, 0, 0, 1, 600, '0', 'platzhalter', 'Xinth-Xoc-Konstrukt', 'platzhalter', '', ''),
(56, 'Hunm-oc-Konstrukt', 8000, 2000, 0, 0, 0, 3, 1200, '55', 'platzhalter', 'Hunm-oc-Konstrukt', 'platzhalter', '', ''),
(57, 'Ez-maC-Konstrukt', 16000, 4000, 1000, 0, 0, 5, 2700, '56', 'platzhalter', 'Ez-maC-Konstrukt', 'platzhalter', '', ''),
(58, 'Zao-tuX-Konstrukt', 32000, 8000, 2000, 1000, 0, 11, 5800, '57', 'platzhalter', 'Zao-tuX-Konstrukt', 'platzhalter', '', ''),
(59, 'Lor-ReX-Konstrukt', 64000, 16000, 4000, 2000, 0, 21, 11600, '58', 'platzhalter', 'Lor-ReX-Konstrukt', 'platzhalter', '', ''),
(60, 'Positronenantrieb', 4000, 1000, 0, 0, 0, 1, 600, '0', 'platzhalter', 'Positronenantrieb', 'platzhalter', '', ''),
(61, 'Srid-XF-Antrieb', 8000, 2000, 0, 0, 0, 3, 1200, '60', 'platzhalter', 'Srid-XF-Antrieb', 'platzhalter', '', ''),
(62, 'Quorxantrieb', 16000, 4000, 1000, 0, 0, 5, 2700, '61', 'platzhalter', 'Quorxantrieb', 'platzhalter', '', ''),
(63, 'Sol-FX-Antrieb', 32000, 8000, 2000, 1000, 0, 11, 5800, '62', 'platzhalter', 'Sol-FX-Antrieb', 'platzhalter', '', ''),
(64, 'Worx-0-3-Antrieb', 64000, 16000, 4000, 2000, 0, 21, 11600, '63', 'platzhalter', 'Worx-0-3-Antrieb', 'platzhalter', '', ''),
(65, 'Quad-DX-Mittler', 8000, 2000, 0, 0, 0, 2, 1200, '0', 'platzhalter', 'Quad-DX-Mittler', 'platzhalter', '', ''),
(66, 'DX-Cov-23-ER-Feld', 10000, 4000, 0, 0, 0, 3, 1800, '9', 'platzhalter', 'DX-Cov-23-ER-Feld', 'platzhalter', '', ''),
(67, 'Styx-Mod', 4000, 1000, 0, 0, 0, 2, 600, '22', 'platzhalter', 'Styx-Mod', 'platzhalter', '', ''),
(68, 'Xinth-Xc-Box', 12000, 3000, 1000, 0, 0, 4, 2100, '58', 'platzhalter', 'Xinth-Xc-Box', 'platzhalter', '', ''),
(69, 'Xinth-Xc-Container', 24000, 4000, 2000, 2000, 0, 8, 4600, '59;68', 'platzhalter', 'Xinth-Xc-Container', 'platzhalter', '', ''),
(70, 'Virtuelles Transmitterfeld', 50000, 20000, 6000, 4000, 1, 16, 12500, '65', 'Erm&ouml;glicht Transmitterverbindungen auch ausserhalb des normalen Transmittersystems, jedoch unter hohem Risiko f&uuml;r den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', 'Virtuelles Transmitterfeld', 'Ermöglicht Transmitterverbindungen auch ausserhalb des normalen Transmittersystems, jedoch unter hohem Risiko für den Reisenden. Es wird Lebewesen nicht empfohlen Transmitter auf dieser Technologie basieren zu benutzen.', '', ''),
(71, 'Cyborggrundlagen', 20000, 20000, 10000, 8000, 0, 8, 12200, '8', 'Erm&ouml;glicht die Entwicklung von Cyborgtechnologien.', 'Cyborggrundlagen', 'Ermöglicht die Entwicklung von Cyborgtechnologien.', '', ''),
(72, 'Cyborginkubationstank', 10000, 5000, 1000, 2000, 0, 5, 3100, '71', 'Dient zur Z&uuml;chtung des biologischen Cyborggewebes.', 'Cyborginkubationstank', 'Dient zur Züchtung des biologischen Cyborggewebes.', '', ''),
(73, 'Cyborgsteuerungskristall', 5000, 60000, 2000, 2000, 0, 12, 13900, '71', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsf&auml;hig.', 'Cyborgsteuerungskristall', 'Der Steuerungskristall lenkt den Cyborg. Er ist in hohem Grade entwicklungsfähig.', '', ''),
(74, 'Cyborgimplantate', 10000, 6000, 10000, 7000, 0, 8, 8000, '71', 'Sie bilden das Grundger&uuml;st des Cyborgs.', 'Cyborgimplantate', 'Sie bilden das Grundgerüst des Cyborgs.', '', ''),
(75, 'EMP-Domimodulator', 500000, 100000, 50000, 20000, 75, 48, 100500, '5', 'platzhalter', 'EMP-Domimodulator', 'platzhalter', '', ''),
(80, 'Assimilatoren', 1000, 100, 0, 0, 0, 4, 120, '7', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung f&uuml;r Kollektoren f&uuml;hrt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann f&uuml;r den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', 'Assimilatoren', 'Die Kollektoren wandeln Sonnenlicht in nutzbare Energie um, die zur Rohstoffgewinnung eingesetzt werden kann.<br> Eine hohe Preissteigerung für Kollektoren führt aber dazu, dass sie gerne von anderen als Beute gesehen werden, die dann für den neuen Besitzer Energie und somit mehr Ressourcen produzieren.', '', ''),
(81, 'Xinth-Xc', 600, 500, 0, 0, 0, 7, 160, '13;40;45;55;60', 'platzhalter', 'Xinth-Xc', 'platzhalter', '', ''),
(82, 'Hunm-oc', 3250, 1500, 0, 0, 0, 15, 625, '13;41;45;46;56;61', 'platzhalter', 'Hunm-oc', 'platzhalter', '', ''),
(83, 'Ez-maC', 12000, 4000, 1800, 750, 0, 31, 2840, '13;42;47;51;57;62', 'platzhalter', 'Ez-maC', 'platzhalter', '', ''),
(84, 'Zao-tuX', 34000, 7000, 750, 1250, 0, 62, 5525, '13;43;48;52;58;63;68', 'KAPAZIT&Auml;T BETR&Auml;GT 40 J&Auml;GER!', 'Zao-tuX', 'KAPAZITÄT BETRÄGT 40 JÄGER!', '', ''),
(85, 'Lor-ReX', 55000, 25000, 3000, 2500, 2, 93, 12600, '13;44;49;53;54;59;70;64;69', 'KAPAZIT&Auml;T BETR&Auml;GT 120 J&Auml;GER!', 'Lor-ReX', 'KAPAZITÄT BETRÄGT 120 JÄGER!', '', ''),
(86, 'Xor-L2R', 750, 750, 0, 0, 0, 9, 225, '13;41;53;56;61', 'platzhalter', 'Xor-L2R', 'platzhalter', '', ''),
(87, 'Os-mTz', 2000, 1000, 0, 0, 0, 11, 400, '13;23;56;61;65', 'platzhalter', 'Os-mTz', 'platzhalter', '', ''),
(88, 'Bi-SoX', 58000, 27000, 6000, 4000, 2, 78, 14800, '13;44;45;59;64;69', 'KAPAZIT&Auml;T BETR&Auml;GT 400 J&Auml;GER!', 'Bi-SoX', 'KAPAZITÄT BETRÄGT 400 JÄGER!', '', ''),
(100, 'Xinth-Base', 8500, 3500, 0, 0, 0, 7, 1550, '13;22', 'platzhalter', 'Xinth-Base', 'platzhalter', '', ''),
(101, 'EMP-Kanonen-Styx', 450, 450, 100, 0, 0, 18, 165, '22;51;67', 'platzhalter', 'EMP-Kanonen-Styx', 'platzhalter', '', ''),
(102, 'X-Magma-Styx', 400, 350, 0, 0, 0, 9, 110, '22;45;67', 'platzhalter', 'X-Magma-Styx', 'platzhalter', '', ''),
(103, 'Zermalmer-Styx', 2500, 100, 100, 0, 0, 24, 300, '22;47;67', 'platzhalter', 'Zermalmer-Styx', 'platzhalter', '', ''),
(104, 'ER-Plasmawerfer-Styx', 2000, 1250, 350, 150, 0, 44, 615, '22;48;67', 'platzhalter', 'ER-Plasmawerfer-Styx', 'platzhalter', '', ''),
(110, 'Infiltrator', 500, 500, 0, 0, 0, 2, 150, '9;13;62;66', 'platzhalter', 'Infiltrator', 'platzhalter', '', ''),
(111, 'Morph-DX', 500, 500, 200, 100, 0, 8, 250, '66', 'platzhalter', 'Morph-DX', 'platzhalter', '', ''),
(120, 'Sektorraumbasis', 7500000, 3750000, 750000, 375000, 250, 192, 1900000, '0', 'Die Sektoraumbasis ist eine risige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und Wohn- und Arbeitst&auml;tte f&uuml;r alle Bewohner der Sektors sein kann.\r\n<br>Vornehmlich diehnt sie als Navigations und Handelsst&uuml;tzpunkt, kann aber durch Erweiterung auch als Flottenbasis f&uuml;r die Sektorflotte dienen.\r\n<br>In Ihrer freien Sektion in der mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektobasis ist aufgrund der unglaublichen Gr&ouml;&szlig;e sehr teuer in der Konstruktion, besonders Tronic wird f&uuml;r eine derartig gro&szlig;e Konstrukion viel ben&ouml;tigt.', 'Sektorraumbasis', 'Die Sektoraumbasis ist eine risige Weltraumstation, die meist mittig im Sektor gebaut, Heimat und Wohn- und Arbeitstätte für alle Bewohner der Sektors sein kann.\r\n<br>Vornehmlich diehnt sie als Navigations und Handelsstützpunkt, kann aber durch Erweiterung auch als Flottenbasis für die Sektorflotte dienen.\r\n<br>In Ihrer freien Sektion in der mitte kann man zudem einen Sektorsprungfeldbegrenzer errichten.<br><br>Die Sektobasis ist aufgrund der unglaublichen Größe sehr teuer in der Konstruktion, besonders Tronic wird für eine derartig große Konstrukion viel benötigt.', '', ''),
(121, 'Sektorsprungfeldbegrenzer', 1500000, 750000, 375000, 150000, 150, 144, 487500, '120', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', 'Sektorsprungfeldbegrenzer', 'Der Sektorsprungfeldbegrenzer ist ebenfalls eine Erweiterung zur Sektorbasis.<br>Nach seinem Bau ist er sofort aktiv und bremst alle sektorfremden Flotten im Angriffsflug um 1 Kampftick runter.', '', ''),
(122, 'Sektorraumwerft', 750000, 750000, 375000, 375000, 60, 144, 493500, '120', 'In der Sektorraumwerft k&ouml;nnen Schiffe f&uuml;r die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', 'Sektorraumwerft', 'In der Sektorraumwerft können Schiffe für die Sektorflotte gebaut werden.<br>Sie ist eine Erweiterung zur Sektorbasis.', '', ''),
(123, 'Sektorhandelszentrum', 150000, 75000, 75000, 150000, 30, 96, 115500, '120', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', 'Sektorhandelszentrum', 'Das Sektorhandelszentrum erleichtert den Sektorhandel.<br>Es ist eine Erweiterung zur Sektorbasis.', '', ''),
(124, 'Scannerphalanx', 600000, 1500000, 250000, 750000, 110, 96, 746000, '120', 'Die Scannerphalanx erm&ouml;glicht das Scannen von Sektorraumbasen und deren Flottenst&auml;rke.', 'Scannerphalanx', 'Die Scannerphalanx ermöglicht das Scannen von Sektorraumbasen und deren Flottenstärke.', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trades`
--

CREATE TABLE `de_trades` (
  `e81` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e82` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e83` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e84` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e85` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e86` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e87` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e88` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e100` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e101` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e102` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e103` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `e104` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_artefact`
--

CREATE TABLE `de_trade_artefact` (
  `id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_depot`
--

CREATE TABLE `de_trade_depot` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `race` char(1) NOT NULL DEFAULT '',
  `e81` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e82` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e83` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e84` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e85` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e86` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e87` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e88` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `e89` int(14) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_fleetoffer`
--

CREATE TABLE `de_trade_fleetoffer` (
  `id` int(14) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sell_type` char(3) NOT NULL DEFAULT '',
  `amount` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `price` double UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(1) NOT NULL DEFAULT '',
  `locked` char(1) NOT NULL DEFAULT '',
  `race` int(1) NOT NULL DEFAULT '0',
  `timestamp` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `remaining_ticks` int(4) NOT NULL DEFAULT '0',
  `ssatz` tinyint(2) NOT NULL DEFAULT '0',
  `sector` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `fromdepot` char(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_fleetrequest`
--

CREATE TABLE `de_trade_fleetrequest` (
  `id` int(14) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `buy_type` char(3) NOT NULL DEFAULT '',
  `amount` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `price` double UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(1) NOT NULL DEFAULT '',
  `locked` char(1) NOT NULL DEFAULT '',
  `race` int(1) NOT NULL DEFAULT '0',
  `timestamp` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `ssatz` tinyint(2) NOT NULL DEFAULT '0',
  `sector` int(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_fleettransit`
--

CREATE TABLE `de_trade_fleettransit` (
  `id` int(14) UNSIGNED NOT NULL,
  `timestamp` int(14) NOT NULL DEFAULT '0',
  `seller_id` int(10) NOT NULL DEFAULT '0',
  `target_id` int(10) NOT NULL DEFAULT '0',
  `amount` int(14) NOT NULL DEFAULT '0',
  `shiptype` char(3) NOT NULL DEFAULT '',
  `remaining_ticks` int(4) NOT NULL DEFAULT '0',
  `race` char(1) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_log`
--

CREATE TABLE `de_trade_log` (
  `id` int(12) UNSIGNED NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `seller_id` int(11) NOT NULL DEFAULT '0',
  `seller_race` char(1) NOT NULL DEFAULT '',
  `seller_sector` int(4) NOT NULL DEFAULT '0',
  `buyer_id` int(11) NOT NULL DEFAULT '0',
  `sell_type` varchar(20) NOT NULL DEFAULT '',
  `sell_amount` int(14) NOT NULL DEFAULT '0',
  `sell_currency` varchar(20) NOT NULL DEFAULT '',
  `sell_price` double NOT NULL DEFAULT '0',
  `buy_price` double NOT NULL DEFAULT '0',
  `seller_got` double NOT NULL DEFAULT '0',
  `buyer_paid` double NOT NULL DEFAULT '0',
  `buyer_bookback` double NOT NULL DEFAULT '0',
  `seller_sectax` double NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_resoffer`
--

CREATE TABLE `de_trade_resoffer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sell_type` char(1) NOT NULL DEFAULT '',
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `price` double UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(1) NOT NULL DEFAULT '',
  `locked` char(1) NOT NULL DEFAULT '',
  `race` int(1) NOT NULL DEFAULT '0',
  `timestamp` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `remaining_ticks` int(4) NOT NULL DEFAULT '0',
  `ssatz` tinyint(2) NOT NULL DEFAULT '0',
  `sector` int(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_trade_resrequest`
--

CREATE TABLE `de_trade_resrequest` (
  `id` int(14) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `buy_type` char(1) NOT NULL DEFAULT '',
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `price` double UNSIGNED NOT NULL DEFAULT '0',
  `currency` char(1) NOT NULL DEFAULT '',
  `locked` char(1) NOT NULL DEFAULT '',
  `race` int(1) NOT NULL DEFAULT '0',
  `timestamp` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `remaining_ticks` int(4) NOT NULL DEFAULT '0',
  `ssatz` tinyint(2) NOT NULL DEFAULT '0',
  `sector` int(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_transactions`
--

CREATE TABLE `de_transactions` (
  `id` int(12) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT '',
  `identifier` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `amount` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `timestamp` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(10) NOT NULL DEFAULT '',
  `ticks_remaining` int(4) NOT NULL DEFAULT '0',
  `flags` varchar(10) NOT NULL DEFAULT '',
  `note` varchar(20) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_achievement`
--

CREATE TABLE `de_user_achievement` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ac1` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac2` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac3` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac4` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac5` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac6` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac7` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac8` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac9` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac10` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac11` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac12` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac13` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac14` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac15` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac16` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac17` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac18` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac19` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac20` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac21` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac22` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac23` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac24` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac25` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `ac999` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_artefact`
--

CREATE TABLE `de_user_artefact` (
  `lid` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_bg_register`
--

CREATE TABLE `de_user_bg_register` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `bg_id` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_build`
--

CREATE TABLE `de_user_build` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `tech_id` mediumint(9) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0',
  `verbzeit` mediumint(9) NOT NULL DEFAULT '0',
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `recycling` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `factory_id` tinyint(4) NOT NULL DEFAULT '0',
  `factory_used_capacity` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_comserver`
--

CREATE TABLE `de_user_comserver` (
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `v1` int(11) DEFAULT NULL,
  `v2` int(11) DEFAULT NULL,
  `v3` int(11) DEFAULT NULL,
  `v4` int(11) DEFAULT NULL,
  `v5` int(11) DEFAULT NULL,
  `v6` int(11) DEFAULT NULL,
  `v7` int(11) DEFAULT NULL,
  `v8` int(11) DEFAULT NULL,
  `v9` int(11) DEFAULT NULL,
  `v10` int(11) DEFAULT NULL,
  `v11` int(11) DEFAULT NULL,
  `v12` int(11) DEFAULT NULL,
  `v13` int(11) DEFAULT NULL,
  `v14` int(11) DEFAULT NULL,
  `v15` int(11) DEFAULT NULL,
  `v16` int(11) DEFAULT NULL,
  `v17` int(11) DEFAULT NULL,
  `v18` int(11) DEFAULT NULL,
  `v19` int(11) DEFAULT NULL,
  `v20` int(11) DEFAULT NULL,
  `v21` int(11) DEFAULT NULL,
  `v22` int(11) DEFAULT NULL,
  `v23` int(11) DEFAULT NULL,
  `v24` int(11) DEFAULT NULL,
  `v25` int(11) DEFAULT NULL,
  `v26` int(11) DEFAULT NULL,
  `v27` int(11) DEFAULT NULL,
  `v28` int(11) DEFAULT NULL,
  `v29` int(11) DEFAULT NULL,
  `v30` int(11) DEFAULT NULL,
  `v31` int(11) DEFAULT NULL,
  `v32` int(11) DEFAULT NULL,
  `v33` int(11) DEFAULT NULL,
  `v34` int(11) DEFAULT NULL,
  `v35` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_data`
--

CREATE TABLE `de_user_data` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `spielername` varchar(20) NOT NULL DEFAULT '',
  `tick` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `fixscore` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `fleetscore` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `ehscore` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `restyp01` double(20,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `restyp02` double(20,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `restyp03` double(20,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `restyp04` double(20,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `restyp05` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `col` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `col_build` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sonde` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `agent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `agent_lost` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `techs` varchar(110) NOT NULL DEFAULT '',
  `buildgnr` mediumint(9) NOT NULL DEFAULT '0',
  `buildgtime` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `resnr` mediumint(9) NOT NULL DEFAULT '0',
  `restime` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `ekey` varchar(11) NOT NULL DEFAULT '',
  `sector` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `system` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `allytag` varchar(8) NOT NULL DEFAULT '',
  `ally_tronic` int(10) NOT NULL DEFAULT '0',
  `newtrans` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `newnews` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `votefor` int(11) NOT NULL DEFAULT '0',
  `e100` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e101` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e102` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e103` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e104` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `defenseexp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ally_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `tradescore` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `sells` int(11) NOT NULL DEFAULT '0',
  `rasse` tinyint(4) NOT NULL DEFAULT '1',
  `hide_secpics` char(1) NOT NULL DEFAULT '0',
  `secmoves` mediumint(9) NOT NULL DEFAULT '0',
  `premium` tinyint(4) NOT NULL DEFAULT '0',
  `tcount` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `zcount` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `eartefakt` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `kartefakt` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `dartefakt` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `werberid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `geworben` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `platz` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `rang` tinyint(3) UNSIGNED NOT NULL DEFAULT '24',
  `scanhistory` varchar(150) NOT NULL DEFAULT '',
  `platz_last_day` mediumint(8) NOT NULL DEFAULT '0',
  `trade_sell_sum` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `trade_buy_sum` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `trade_forbidden` char(1) NOT NULL DEFAULT '0',
  `nrspielername` varchar(20) NOT NULL DEFAULT '',
  `nrrasse` tinyint(4) NOT NULL DEFAULT '0',
  `credits` mediumint(8) NOT NULL DEFAULT '0',
  `credittransfer` int(11) NOT NULL DEFAULT '0',
  `sm_rboost` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_rboost_rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_col` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_col_rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_kartefakt` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_kartefakt_rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_tronic` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_tronic_rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art1` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art2` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art3` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art4` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art5` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art6` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art7` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art8` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art9` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art10` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art11` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art12` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art13` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art14` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art15` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art16` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art17` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art18` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art19` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art1rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art2rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art3rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art4rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art5rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art6rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art7rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art8rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art9rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sm_art10rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art11rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art12rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art13rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art14rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art15rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art16rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art17rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art18rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sm_art19rem` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `actpoints` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `patime` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `palenium` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `npc` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `ovopt` varchar(13) NOT NULL DEFAULT '',
  `soundoff` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `artbldglevel` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `spend01` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `spend02` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `spend03` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `spend04` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `spend05` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `npccol` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `npcartefact` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `archi` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sm_remtime` smallint(5) UNSIGNED NOT NULL DEFAULT '60',
  `roundpoints` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `useefta` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `chatoff` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `chatoffallg` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `chatoffglobal` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `chatclear` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `chatchannel` tinyint(3) UNSIGNED NOT NULL DEFAULT '3',
  `secsort` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `kg01` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `kg02` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `kg03` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `kg04` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `kgget` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `design1` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `design2` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `design3` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `design4` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `sou_user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `efta_user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `geteacredits` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `geteftabonus` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `secatt` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `secstatdisable` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `sc1` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sc2` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sc3` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `sc4` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `ehlock` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `eftagetlastartefact` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `dailygift` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `dailyallygift` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `helper` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `helperprogress` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `specreset` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `spec1` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `spec2` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `spec3` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `spec4` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `spec5` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `tradesystemscore` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `tradesystemtrades` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `tradesystem_mb_uid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tradesystem_mb_tick` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `trade_reminder` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `lastpcatt` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `wurdegeruesselt` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `npcfollow` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `show_ally_secstatus` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `eh_counter` mediumint(9) NOT NULL DEFAULT '0',
  `eh_siege` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `last_sector` smallint(6) NOT NULL DEFAULT '0',
  `pve_score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `pve_bldg_score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `bgscore0` int(11) NOT NULL DEFAULT '0',
  `bgscore1` int(11) NOT NULL DEFAULT '0',
  `bgscore2` int(11) NOT NULL DEFAULT '0',
  `bgscore3` int(11) NOT NULL DEFAULT '0',
  `bgscore4` int(11) NOT NULL DEFAULT '0',
  `vs_auto_explore` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_fleet`
--

CREATE TABLE `de_user_fleet` (
  `user_id` varchar(8) NOT NULL DEFAULT '0',
  `komatt` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `hsec` mediumint(9) NOT NULL DEFAULT '0',
  `hsys` mediumint(9) NOT NULL DEFAULT '0',
  `komdef` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `zielsec` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `zielsys` mediumint(3) UNSIGNED NOT NULL DEFAULT '0',
  `aktion` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `zeit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `aktzeit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `gesrzeit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `entdeckt` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `entdecktsec` tinyint(1) UNSIGNED DEFAULT NULL,
  `showfleettarget` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `e81` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e82` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e83` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e84` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e85` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e86` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e87` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e88` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e89` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `e90` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `fleetsize` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `artid1` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artlvl1` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `artid2` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artlvl2` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `artid3` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artlvl3` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `artid4` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artlvl4` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `artid5` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artlvl5` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `artid6` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `artlvl6` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `mission_time` bigint(20) UNSIGNED NOT NULL,
  `mission_data` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_getcol`
--

CREATE TABLE `de_user_getcol` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `zuser_id` int(10) UNSIGNED NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `colanz` smallint(5) NOT NULL,
  `energiewert` int(10) UNSIGNED NOT NULL,
  `getexp` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_hyper`
--

CREATE TABLE `de_user_hyper` (
  `id` int(10) UNSIGNED NOT NULL,
  `empfaenger` int(11) NOT NULL DEFAULT '0',
  `absender` int(11) NOT NULL DEFAULT '0',
  `fromsec` smallint(6) DEFAULT NULL,
  `fromsys` smallint(5) UNSIGNED DEFAULT NULL,
  `fromnic` varchar(20) NOT NULL DEFAULT '',
  `time` bigint(20) UNSIGNED NOT NULL,
  `betreff` tinytext NOT NULL,
  `text` text NOT NULL,
  `archiv` tinyint(2) NOT NULL DEFAULT '0',
  `sender` smallint(2) NOT NULL DEFAULT '0',
  `gelesen` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_info`
--

CREATE TABLE `de_user_info` (
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `vorname` varchar(20) NOT NULL DEFAULT '',
  `nachname` varchar(20) NOT NULL DEFAULT '',
  `strasse` varchar(30) NOT NULL DEFAULT '',
  `plz` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ort` varchar(30) NOT NULL DEFAULT '',
  `land` varchar(22) NOT NULL DEFAULT '',
  `telefon` varchar(20) NOT NULL DEFAULT '',
  `tag` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `monat` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `jahr` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `geschlecht` tinyint(4) NOT NULL DEFAULT '0',
  `kommentar` text NOT NULL,
  `submit` tinyint(4) NOT NULL DEFAULT '1',
  `ui_icq` varchar(30) NOT NULL DEFAULT '',
  `ui_aim` varchar(30) NOT NULL DEFAULT '',
  `ui_yahoo` varchar(30) NOT NULL DEFAULT '',
  `ui_msn` varchar(30) NOT NULL DEFAULT '',
  `ui_motto` varchar(200) NOT NULL DEFAULT '',
  `ui_logo` varchar(250) NOT NULL DEFAULT '',
  `ui_hobbys` varchar(200) NOT NULL DEFAULT '',
  `ui_beruf` varchar(200) NOT NULL DEFAULT '',
  `ui_website` varchar(100) NOT NULL DEFAULT '',
  `ui_mail` varchar(100) NOT NULL DEFAULT '',
  `ui_wohnort` varchar(50) NOT NULL DEFAULT '',
  `ui_tag` int(2) NOT NULL DEFAULT '0',
  `ui_monat` int(2) NOT NULL DEFAULT '0',
  `ui_jahr` int(4) NOT NULL DEFAULT '0',
  `ui_irc_serv` varchar(50) NOT NULL DEFAULT '',
  `ui_irc_chan` varchar(50) NOT NULL DEFAULT '',
  `ui_sonstiges` varchar(100) NOT NULL DEFAULT '',
  `ui_level` varchar(51) NOT NULL DEFAULT '000000000000000000000000000000000000000000000000000',
  `gpfad` varchar(255) NOT NULL DEFAULT '',
  `transparency` tinyint(3) UNSIGNED DEFAULT '100',
  `ircname` varchar(20) NOT NULL DEFAULT '',
  `observation_stat` smallint(1) NOT NULL DEFAULT '0',
  `observation_by` char(20) NOT NULL DEFAULT '',
  `ud_all` text NOT NULL,
  `ud_sector` text NOT NULL,
  `ud_ally` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_ip`
--

CREATE TABLE `de_user_ip` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `browser` varchar(200) NOT NULL,
  `loginhelp` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_locks`
--

CREATE TABLE `de_user_locks` (
  `id` mediumint(9) UNSIGNED DEFAULT '0',
  `locked` tinyint(3) UNSIGNED DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_log`
--

CREATE TABLE `de_user_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serverid` smallint(5) UNSIGNED NOT NULL,
  `userid` mediumint(8) UNSIGNED NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL,
  `file` varchar(25) DEFAULT NULL,
  `getpost` varchar(4096) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_map`
--

CREATE TABLE `de_user_map` (
  `user_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `known_since` bigint(20) NOT NULL,
  `specialsystem_data` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_map_bldg`
--

CREATE TABLE `de_user_map_bldg` (
  `user_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `field_id` smallint(5) UNSIGNED NOT NULL,
  `bldg_id` smallint(5) UNSIGNED NOT NULL,
  `bldg_level` smallint(5) UNSIGNED NOT NULL,
  `bldg_time` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_map_loot`
--

CREATE TABLE `de_user_map_loot` (
  `user_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `field_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_mission`
--

CREATE TABLE `de_user_mission` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `mission_id` smallint(6) NOT NULL,
  `reward` text NOT NULL,
  `reward_percentage` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `need_agents` bigint(20) NOT NULL DEFAULT '0',
  `end_time` bigint(20) UNSIGNED NOT NULL,
  `get_reward` tinyint(4) NOT NULL DEFAULT '0',
  `counter` mediumint(9) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_news`
--

CREATE TABLE `de_user_news` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `typ` tinyint(3) DEFAULT NULL,
  `time` varchar(14) DEFAULT NULL,
  `text` text NOT NULL,
  `seen` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_npcx`
--

CREATE TABLE `de_user_npcx` (
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_quest`
--

CREATE TABLE `de_user_quest` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `pid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `flag1` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `flag2` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `anzahl` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_scan`
--

CREATE TABLE `de_user_scan` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `zuser_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `rasse` tinyint(4) NOT NULL DEFAULT '0',
  `atime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `allytag` varchar(8) NOT NULL DEFAULT '',
  `ftime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e81` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e82` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e83` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e84` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e85` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e86` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e87` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e88` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e89` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e90` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dtime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e100` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e101` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e102` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e103` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `e104` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `stime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `fleet` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `defense` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `build` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `col` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `buildings` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `restyp01` double(20,2) NOT NULL DEFAULT '0.00',
  `restyp02` double(20,2) NOT NULL DEFAULT '0.00',
  `restyp03` double(20,2) NOT NULL DEFAULT '0.00',
  `restyp04` double(20,2) NOT NULL DEFAULT '0.00',
  `restyp05` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ps` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_setbounty`
--

CREATE TABLE `de_user_setbounty` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `zuser_id` int(10) UNSIGNED NOT NULL,
  `energiewert` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_special_ship`
--

CREATE TABLE `de_user_special_ship` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `data` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_stat`
--

CREATE TABLE `de_user_stat` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `datum` varchar(10) NOT NULL DEFAULT '',
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `col` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cybexp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `h0` tinyint(1) NOT NULL DEFAULT '0',
  `h1` tinyint(1) NOT NULL DEFAULT '0',
  `h2` tinyint(1) NOT NULL DEFAULT '0',
  `h3` tinyint(1) NOT NULL DEFAULT '0',
  `h4` tinyint(1) NOT NULL DEFAULT '0',
  `h5` tinyint(1) NOT NULL DEFAULT '0',
  `h6` tinyint(1) NOT NULL DEFAULT '0',
  `h7` tinyint(1) NOT NULL DEFAULT '0',
  `h8` tinyint(1) NOT NULL DEFAULT '0',
  `h9` tinyint(1) NOT NULL DEFAULT '0',
  `h10` tinyint(1) NOT NULL DEFAULT '0',
  `h11` tinyint(1) NOT NULL DEFAULT '0',
  `h12` tinyint(1) NOT NULL DEFAULT '0',
  `h13` tinyint(1) NOT NULL DEFAULT '0',
  `h14` tinyint(1) NOT NULL DEFAULT '0',
  `h15` tinyint(1) NOT NULL DEFAULT '0',
  `h16` tinyint(1) NOT NULL DEFAULT '0',
  `h17` tinyint(1) NOT NULL DEFAULT '0',
  `h18` tinyint(1) NOT NULL DEFAULT '0',
  `h19` tinyint(1) NOT NULL DEFAULT '0',
  `h20` tinyint(1) NOT NULL DEFAULT '0',
  `h21` tinyint(1) NOT NULL DEFAULT '0',
  `h22` tinyint(1) NOT NULL DEFAULT '0',
  `h23` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_storage`
--

CREATE TABLE `de_user_storage` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` mediumint(8) UNSIGNED NOT NULL,
  `item_amount` bigint(20) NOT NULL,
  `item_wt_change` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_techs`
--

CREATE TABLE `de_user_techs` (
  `user_id` int(11) NOT NULL,
  `tech_id` int(11) NOT NULL,
  `time_finished` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_user_trade`
--

CREATE TABLE `de_user_trade` (
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `trade_id` bigint(20) NOT NULL,
  `deliverytime` int(10) UNSIGNED NOT NULL,
  `offertime` int(10) UNSIGNED NOT NULL,
  `selltyp` tinyint(3) UNSIGNED NOT NULL,
  `sellamount` bigint(20) UNSIGNED NOT NULL,
  `buytyp` tinyint(3) UNSIGNED NOT NULL,
  `buyamount` bigint(20) UNSIGNED NOT NULL,
  `quality` tinyint(3) UNSIGNED NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `tradescore` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_vote_stimmen`
--

CREATE TABLE `de_vote_stimmen` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `vote_id` int(5) NOT NULL DEFAULT '0',
  `votefor` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_vote_umfragen`
--

CREATE TABLE `de_vote_umfragen` (
  `id` int(5) UNSIGNED NOT NULL,
  `frage` varchar(75) NOT NULL DEFAULT '',
  `antworten` text NOT NULL,
  `hinweis` text NOT NULL,
  `stimmen` varchar(30) NOT NULL DEFAULT '0|0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `startdatum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enddatum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ergebnisse` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `de_alliforum_posts`
--
ALTER TABLE `de_alliforum_posts`
  ADD PRIMARY KEY (`postid`),
  ADD UNIQUE KEY `postid` (`postid`);

--
-- Indizes für die Tabelle `de_alliforum_threads`
--
ALTER TABLE `de_alliforum_threads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indizes für die Tabelle `de_allys`
--
ALTER TABLE `de_allys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `allytag` (`allytag`);

--
-- Indizes für die Tabelle `de_ally_antrag`
--
ALTER TABLE `de_ally_antrag`
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_ally_buendniss_antrag`
--
ALTER TABLE `de_ally_buendniss_antrag`
  ADD UNIQUE KEY `ally_id_antragsteller` (`ally_id_antragsteller`);

--
-- Indizes für die Tabelle `de_ally_history`
--
ALTER TABLE `de_ally_history`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `allytag` (`allytag`,`allyid`);

--
-- Indizes für die Tabelle `de_ally_scans`
--
ALTER TABLE `de_ally_scans`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `owner_allytag` (`owner_allytag`);

--
-- Indizes für die Tabelle `de_ally_stat`
--
ALTER TABLE `de_ally_stat`
  ADD KEY `id` (`id`);

--
-- Indizes für die Tabelle `de_ally_storage`
--
ALTER TABLE `de_ally_storage`
  ADD KEY `ally_id` (`ally_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indizes für die Tabelle `de_artefakt`
--
ALTER TABLE `de_artefakt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indizes für die Tabelle `de_auction`
--
ALTER TABLE `de_auction`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_basedata_map_kanten`
--
ALTER TABLE `de_basedata_map_kanten`
  ADD KEY `sec_id` (`sec_id`);

--
-- Indizes für die Tabelle `de_basedata_map_knoten`
--
ALTER TABLE `de_basedata_map_knoten`
  ADD KEY `sec_id` (`sec_id`);

--
-- Indizes für die Tabelle `de_basedata_map_sector`
--
ALTER TABLE `de_basedata_map_sector`
  ADD UNIQUE KEY `sec_id` (`sec_id`);

--
-- Indizes für die Tabelle `de_chat_msg`
--
ALTER TABLE `de_chat_msg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel` (`channel`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indizes für die Tabelle `de_dez_ausgaben`
--
ALTER TABLE `de_dez_ausgaben`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_dez_zeitung`
--
ALTER TABLE `de_dez_zeitung`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_item_data`
--
ALTER TABLE `de_item_data`
  ADD PRIMARY KEY (`item_id`);

--
-- Indizes für die Tabelle `de_login`
--
ALTER TABLE `de_login`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `nic` (`nic`),
  ADD KEY `last_login` (`last_login`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `status` (`status`),
  ADD KEY `pass` (`pass`),
  ADD KEY `newpass` (`newpass`),
  ADD KEY `loginkey` (`loginkey`),
  ADD KEY `loginkeytime` (`loginkeytime`);

--
-- Indizes für die Tabelle `de_map_kanten`
--
ALTER TABLE `de_map_kanten`
  ADD UNIQUE KEY `zwei` (`knoten_id1`,`knoten_id2`),
  ADD KEY `knoten_id1` (`knoten_id1`),
  ADD KEY `knoten_id2` (`knoten_id2`);

--
-- Indizes für die Tabelle `de_map_objects`
--
ALTER TABLE `de_map_objects`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_news_overview`
--
ALTER TABLE `de_news_overview`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_news_sector`
--
ALTER TABLE `de_news_sector`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indizes für die Tabelle `de_news_server`
--
ALTER TABLE `de_news_server`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wt` (`wt`);

--
-- Indizes für die Tabelle `de_sector`
--
ALTER TABLE `de_sector`
  ADD PRIMARY KEY (`sec_id`),
  ADD KEY `zielsec` (`zielsec`),
  ADD KEY `aktion` (`aktion`);

--
-- Indizes für die Tabelle `de_sectorforum_posts`
--
ALTER TABLE `de_sectorforum_posts`
  ADD PRIMARY KEY (`postid`),
  ADD UNIQUE KEY `postid` (`postid`),
  ADD KEY `postid_2` (`postid`);

--
-- Indizes für die Tabelle `de_sectorforum_threads`
--
ALTER TABLE `de_sectorforum_threads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indizes für die Tabelle `de_sector_build`
--
ALTER TABLE `de_sector_build`
  ADD KEY `verbzeit` (`verbzeit`);

--
-- Indizes für die Tabelle `de_sector_stat`
--
ALTER TABLE `de_sector_stat`
  ADD KEY `sec_id` (`sec_id`);

--
-- Indizes für die Tabelle `de_sector_umzug`
--
ALTER TABLE `de_sector_umzug`
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_sector_voteout`
--
ALTER TABLE `de_sector_voteout`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Indizes für die Tabelle `de_server_round_toplist`
--
ALTER TABLE `de_server_round_toplist`
  ADD PRIMARY KEY (`round_id`);

--
-- Indizes für die Tabelle `de_server_stat`
--
ALTER TABLE `de_server_stat`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_system`
--
ALTER TABLE `de_system`
  ADD UNIQUE KEY `lasttick` (`lasttick`);

--
-- Indizes für die Tabelle `de_tauction`
--
ALTER TABLE `de_tauction`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_tech_data`
--
ALTER TABLE `de_tech_data`
  ADD PRIMARY KEY (`tech_id`);

--
-- Indizes für die Tabelle `de_tech_data1`
--
ALTER TABLE `de_tech_data1`
  ADD PRIMARY KEY (`tech_id`);

--
-- Indizes für die Tabelle `de_tech_data2`
--
ALTER TABLE `de_tech_data2`
  ADD PRIMARY KEY (`tech_id`);

--
-- Indizes für die Tabelle `de_tech_data3`
--
ALTER TABLE `de_tech_data3`
  ADD PRIMARY KEY (`tech_id`);

--
-- Indizes für die Tabelle `de_tech_data4`
--
ALTER TABLE `de_tech_data4`
  ADD PRIMARY KEY (`tech_id`),
  ADD UNIQUE KEY `tech_id` (`tech_id`),
  ADD KEY `tech_id_2` (`tech_id`);

--
-- Indizes für die Tabelle `de_tech_data5`
--
ALTER TABLE `de_tech_data5`
  ADD PRIMARY KEY (`tech_id`),
  ADD UNIQUE KEY `tech_id` (`tech_id`),
  ADD KEY `tech_id_2` (`tech_id`);

--
-- Indizes für die Tabelle `de_trade_depot`
--
ALTER TABLE `de_trade_depot`
  ADD UNIQUE KEY `id` (`user_id`,`race`);

--
-- Indizes für die Tabelle `de_trade_fleetoffer`
--
ALTER TABLE `de_trade_fleetoffer`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `remaining_ticks` (`remaining_ticks`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sell_type` (`sell_type`,`currency`,`price`);

--
-- Indizes für die Tabelle `de_trade_fleetrequest`
--
ALTER TABLE `de_trade_fleetrequest`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sell_type` (`buy_type`,`currency`,`price`);

--
-- Indizes für die Tabelle `de_trade_fleettransit`
--
ALTER TABLE `de_trade_fleettransit`
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`);

--
-- Indizes für die Tabelle `de_trade_log`
--
ALTER TABLE `de_trade_log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_trade_resoffer`
--
ALTER TABLE `de_trade_resoffer`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `remaining_ticks` (`remaining_ticks`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sell_type` (`sell_type`,`currency`,`price`);

--
-- Indizes für die Tabelle `de_trade_resrequest`
--
ALTER TABLE `de_trade_resrequest`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `remaining_ticks` (`remaining_ticks`),
  ADD KEY `buy_type` (`buy_type`,`currency`,`price`);

--
-- Indizes für die Tabelle `de_transactions`
--
ALTER TABLE `de_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_user_achievement`
--
ALTER TABLE `de_user_achievement`
  ADD PRIMARY KEY (`user_id`);

--
-- Indizes für die Tabelle `de_user_artefact`
--
ALTER TABLE `de_user_artefact`
  ADD PRIMARY KEY (`lid`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id` (`id`);

--
-- Indizes für die Tabelle `de_user_bg_register`
--
ALTER TABLE `de_user_bg_register`
  ADD UNIQUE KEY `user_id_bg_id` (`user_id`,`bg_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bg_id` (`bg_id`);

--
-- Indizes für die Tabelle `de_user_build`
--
ALTER TABLE `de_user_build`
  ADD KEY `verbzeit` (`verbzeit`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tech_id` (`tech_id`);

--
-- Indizes für die Tabelle `de_user_comserver`
--
ALTER TABLE `de_user_comserver`
  ADD PRIMARY KEY (`user_id`);

--
-- Indizes für die Tabelle `de_user_data`
--
ALTER TABLE `de_user_data`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `spielername` (`spielername`),
  ADD UNIQUE KEY `nrspielername` (`nrspielername`),
  ADD KEY `rasse` (`rasse`),
  ADD KEY `sector` (`sector`),
  ADD KEY `system` (`system`);

--
-- Indizes für die Tabelle `de_user_fleet`
--
ALTER TABLE `de_user_fleet`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `hsec` (`hsec`),
  ADD KEY `hsys` (`hsys`),
  ADD KEY `zielsec` (`zielsec`),
  ADD KEY `zielsys` (`zielsys`),
  ADD KEY `aktion` (`aktion`),
  ADD KEY `entdeckt` (`entdeckt`),
  ADD KEY `mision_time` (`mission_time`);

--
-- Indizes für die Tabelle `de_user_getcol`
--
ALTER TABLE `de_user_getcol`
  ADD KEY `zuser_id` (`zuser_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_user_hyper`
--
ALTER TABLE `de_user_hyper`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empfaenger` (`empfaenger`),
  ADD KEY `absender` (`absender`),
  ADD KEY `time` (`time`),
  ADD KEY `sender` (`sender`);

--
-- Indizes für die Tabelle `de_user_ip`
--
ALTER TABLE `de_user_ip`
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_user_locks`
--
ALTER TABLE `de_user_locks`
  ADD KEY `id` (`id`);

--
-- Indizes für die Tabelle `de_user_log`
--
ALTER TABLE `de_user_log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `de_user_map`
--
ALTER TABLE `de_user_map`
  ADD UNIQUE KEY `user_id` (`user_id`,`map_id`);

--
-- Indizes für die Tabelle `de_user_map_bldg`
--
ALTER TABLE `de_user_map_bldg`
  ADD UNIQUE KEY `user_map_field` (`user_id`,`map_id`,`field_id`);

--
-- Indizes für die Tabelle `de_user_map_loot`
--
ALTER TABLE `de_user_map_loot`
  ADD UNIQUE KEY `user_map` (`user_id`,`map_id`,`field_id`);

--
-- Indizes für die Tabelle `de_user_mission`
--
ALTER TABLE `de_user_mission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`mission_id`);

--
-- Indizes für die Tabelle `de_user_news`
--
ALTER TABLE `de_user_news`
  ADD KEY `user_id` (`user_id`,`seen`);

--
-- Indizes für die Tabelle `de_user_quest`
--
ALTER TABLE `de_user_quest`
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_user_scan`
--
ALTER TABLE `de_user_scan`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `zuser_id` (`zuser_id`);

--
-- Indizes für die Tabelle `de_user_setbounty`
--
ALTER TABLE `de_user_setbounty`
  ADD KEY `zuser_id` (`zuser_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_user_special_ship`
--
ALTER TABLE `de_user_special_ship`
  ADD PRIMARY KEY (`user_id`);

--
-- Indizes für die Tabelle `de_user_stat`
--
ALTER TABLE `de_user_stat`
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `de_user_storage`
--
ALTER TABLE `de_user_storage`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indizes für die Tabelle `de_user_techs`
--
ALTER TABLE `de_user_techs`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time_finished` (`time_finished`),
  ADD KEY `tech_id` (`tech_id`);

--
-- Indizes für die Tabelle `de_user_trade`
--
ALTER TABLE `de_user_trade`
  ADD PRIMARY KEY (`trade_id`);

--
-- Indizes für die Tabelle `de_vote_umfragen`
--
ALTER TABLE `de_vote_umfragen`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `de_alliforum_posts`
--
ALTER TABLE `de_alliforum_posts`
  MODIFY `postid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_alliforum_threads`
--
ALTER TABLE `de_alliforum_threads`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_allys`
--
ALTER TABLE `de_allys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_ally_history`
--
ALTER TABLE `de_ally_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_ally_scans`
--
ALTER TABLE `de_ally_scans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_artefakt`
--
ALTER TABLE `de_artefakt`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT für Tabelle `de_auction`
--
ALTER TABLE `de_auction`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_chat_msg`
--
ALTER TABLE `de_chat_msg`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3390;

--
-- AUTO_INCREMENT für Tabelle `de_dez_ausgaben`
--
ALTER TABLE `de_dez_ausgaben`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_dez_zeitung`
--
ALTER TABLE `de_dez_zeitung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_item_data`
--
ALTER TABLE `de_item_data`
  MODIFY `item_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `de_login`
--
ALTER TABLE `de_login`
  MODIFY `user_id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_map_objects`
--
ALTER TABLE `de_map_objects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_news_overview`
--
ALTER TABLE `de_news_overview`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_news_sector`
--
ALTER TABLE `de_news_sector`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_news_server`
--
ALTER TABLE `de_news_server`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_sector`
--
ALTER TABLE `de_sector`
  MODIFY `sec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2001;

--
-- AUTO_INCREMENT für Tabelle `de_sectorforum_posts`
--
ALTER TABLE `de_sectorforum_posts`
  MODIFY `postid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_sectorforum_threads`
--
ALTER TABLE `de_sectorforum_threads`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_server_round_toplist`
--
ALTER TABLE `de_server_round_toplist`
  MODIFY `round_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_server_stat`
--
ALTER TABLE `de_server_stat`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_tauction`
--
ALTER TABLE `de_tauction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_tech_data`
--
ALTER TABLE `de_tech_data`
  MODIFY `tech_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT für Tabelle `de_trade_fleetoffer`
--
ALTER TABLE `de_trade_fleetoffer`
  MODIFY `id` int(14) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_trade_fleetrequest`
--
ALTER TABLE `de_trade_fleetrequest`
  MODIFY `id` int(14) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_trade_fleettransit`
--
ALTER TABLE `de_trade_fleettransit`
  MODIFY `id` int(14) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_trade_log`
--
ALTER TABLE `de_trade_log`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_trade_resoffer`
--
ALTER TABLE `de_trade_resoffer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_trade_resrequest`
--
ALTER TABLE `de_trade_resrequest`
  MODIFY `id` int(14) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_transactions`
--
ALTER TABLE `de_transactions`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_user_artefact`
--
ALTER TABLE `de_user_artefact`
  MODIFY `lid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_user_hyper`
--
ALTER TABLE `de_user_hyper`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_user_log`
--
ALTER TABLE `de_user_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_user_mission`
--
ALTER TABLE `de_user_mission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_user_trade`
--
ALTER TABLE `de_user_trade`
  MODIFY `trade_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `de_vote_umfragen`
--
ALTER TABLE `de_vote_umfragen`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
