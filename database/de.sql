-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250312.09988faae1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 03. Jul 2025 um 18:14
-- Server-Version: 11.5.2-MariaDB-log
-- PHP-Version: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Datenbank: `de_ang_export`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `de_login`
--

CREATE TABLE `de_login` (
  `user_id` mediumint(9) NOT NULL,
  `owner_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `nic` varchar(100) NOT NULL DEFAULT '',
  `reg_mail` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(255) DEFAULT NULL,
  `level` mediumint(3) NOT NULL DEFAULT 1,
  `register` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_click` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `com_sperre` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `supporter` varchar(40) NOT NULL DEFAULT '',
  `logins` smallint(6) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `clicks` int(11) NOT NULL DEFAULT 0,
  `lastpopup` int(11) UNSIGNED DEFAULT NULL,
  `newpass` varchar(32) DEFAULT NULL,
  `sendmail` tinyint(4) NOT NULL DEFAULT 0,
  `savestatus` tinyint(1) NOT NULL DEFAULT 0,
  `inaktmail` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `blocktime` int(11) NOT NULL DEFAULT 0,
  `activetime` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `loginkey` varchar(16) NOT NULL DEFAULT '',
  `loginkeytime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loginkeyip` varchar(15) NOT NULL DEFAULT '',
  `delmode` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `cooperation` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `de_login`
--
ALTER TABLE `de_login`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `nic` (`nic`),
  ADD KEY `last_login` (`last_login`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `status` (`status`),
  ADD KEY `pass` (`pass`(250)),
  ADD KEY `newpass` (`newpass`),
  ADD KEY `loginkey` (`loginkey`),
  ADD KEY `loginkeytime` (`loginkeytime`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `de_login`
--
ALTER TABLE `de_login`
  MODIFY `user_id` mediumint(9) NOT NULL AUTO_INCREMENT;
COMMIT;
