-- Schemaänderung Oktober 2026 (Sektorprojekte und Kampfmittel aus den Vergessenen Systemen, docs/sektorprojekte.md),
-- auf beiden Servern einspielen. Alles steht auch in de.sql. Ohne die drei Tabellen laufen die Seiten weiter, nur das
-- Sektor-VS-Lager, die Projekte und die Kampfmittel fehlen. Die Spalte produktion_basis braucht Hekate für die
-- Skalierung der Auftragsmengen am Median der VS-Förderung.

ALTER TABLE `de_hekate` ADD `produktion_basis` double NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS `de_sector_vs` (
  `sec_id` smallint(5) UNSIGNED NOT NULL,
  `relais` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `raffinerie` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `werft` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `gitter` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `bau_projekt` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `bau_rest_wt` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `km_schild` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `km_nachbrenner` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `km_stoersender` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `km_enter` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `schild_bis_kt` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stoersender_bis_wt` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `enter_aktiv` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`sec_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `de_sector_vs_lager` (
  `sec_id` smallint(5) UNSIGNED NOT NULL,
  `item_id` smallint(5) UNSIGNED NOT NULL,
  `menge` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`sec_id`, `item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `de_sector_vs_einzahlung` (
  `sec_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` smallint(5) UNSIGNED NOT NULL,
  `menge` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`sec_id`, `user_id`, `item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
