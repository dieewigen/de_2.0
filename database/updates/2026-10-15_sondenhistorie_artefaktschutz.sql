-- Schemaänderung Oktober 2026 (Übernahme der Browser-Extension ins Spiel), auf beiden Servern einspielen:
-- Sondenhistorie (secret.php) und Artefaktschutz (artefacts.php). Beide Änderungen stehen auch in de.sql.

CREATE TABLE IF NOT EXISTS `de_user_scan_history` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `zuser_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `score` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `fleet` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `defense` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `build` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `col` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `buildings` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `restyp01` double(20,2) NOT NULL DEFAULT 0.00,
  `restyp02` double(20,2) NOT NULL DEFAULT 0.00,
  `restyp03` double(20,2) NOT NULL DEFAULT 0.00,
  `restyp04` double(20,2) NOT NULL DEFAULT 0.00,
  `restyp05` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ziel` (`user_id`,`zuser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `de_user_artefact` ADD `schutz` tinyint(1) UNSIGNED NOT NULL DEFAULT 0;
