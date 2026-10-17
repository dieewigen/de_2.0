-- Schemaänderung Oktober 2026 (Werkstatt des Basissterns, docs/werkstatt.md), auf beiden Servern einspielen.
-- Die Tabelle steht auch in de.sql. Erfahrung, Würfe, Staub und Lagerstufe liegen im serialisierten Basisstern
-- (de_user_special_ship.data), dafür ist keine Änderung nötig.

CREATE TABLE IF NOT EXISTS `de_user_bs_item` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `platz` tinyint(3) UNSIGNED NOT NULL,
  `guete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `werte` varchar(600) NOT NULL DEFAULT '',
  `angelegt` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `wt` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
