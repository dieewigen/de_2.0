<?php
//Zugangsdaten und Dienstkonfiguration. Die Defaultwerte gelten, wenn die
//Umgebungsvariablen nicht gesetzt sind; sie heißen wie der PHP-Schlüssel in
//Großbuchstaben (z. B. ENV_DB_DIEEWIGEN_HOST) und kommen auf den Docker-Servern
//aus docker/sde/.env bzw. docker/xde/.env. Referenz: env.inc.sample.php.

require_once __DIR__ . '/../src/Config/EnvLoader.php';

use DieEwigen\DE2\Config\EnvLoader;

//Admin-E-Mail-Adresse
$GLOBALS['env_admin_email'] = EnvLoader::str('ENV_ADMIN_EMAIL', '');

//Forum
$GLOBALS['env_db_forum_host'] = EnvLoader::str('ENV_DB_FORUM_HOST', '');
$GLOBALS['env_db_forum_user'] = EnvLoader::str('ENV_DB_FORUM_USER', '');
$GLOBALS['env_db_forum_password'] = EnvLoader::str('ENV_DB_FORUM_PASSWORD', '');
$GLOBALS['env_db_forum_database'] = EnvLoader::str('ENV_DB_FORUM_DATABASE', '');

//Accountverwaltung
$GLOBALS['env_db_loginsystem_host'] = EnvLoader::str('ENV_DB_LOGINSYSTEM_HOST', '');
$GLOBALS['env_db_loginsystem_user'] = EnvLoader::str('ENV_DB_LOGINSYSTEM_USER', '');
$GLOBALS['env_db_loginsystem_password'] = EnvLoader::str('ENV_DB_LOGINSYSTEM_PASSWORD', '');
$GLOBALS['env_db_loginsystem_database'] = EnvLoader::str('ENV_DB_LOGINSYSTEM_DATABASE', '');

$GLOBALS['env_db_logging_host'] = EnvLoader::str('ENV_DB_LOGGING_HOST', '');
$GLOBALS['env_db_logging_user'] = EnvLoader::str('ENV_DB_LOGGING_USER', '');
$GLOBALS['env_db_logging_password'] = EnvLoader::str('ENV_DB_LOGGING_PASSWORD', '');
$GLOBALS['env_db_logging_database'] = EnvLoader::str('ENV_DB_LOGGING_DATABASE', '');

//DE
$GLOBALS['env_db_dieewigen_host'] = EnvLoader::str('ENV_DB_DIEEWIGEN_HOST', '');
$GLOBALS['env_db_dieewigen_user'] = EnvLoader::str('ENV_DB_DIEEWIGEN_USER', '');
$GLOBALS['env_db_dieewigen_password'] = EnvLoader::str('ENV_DB_DIEEWIGEN_PASSWORD', '');
$GLOBALS['env_db_dieewigen_database'] = EnvLoader::str('ENV_DB_DIEEWIGEN_DATABASE', '');

//SMTP-Postfach
$GLOBALS['env_mail_server'] = EnvLoader::str('ENV_MAIL_SERVER', '');
$GLOBALS['env_mail_user'] = EnvLoader::str('ENV_MAIL_USER', '');
$GLOBALS['env_mail_password'] = EnvLoader::str('ENV_MAIL_PASSWORD', '');
$GLOBALS['env_mail_noreply'] = EnvLoader::str('ENV_MAIL_NOREPLY', '');

//Discord Webhooks
$GLOBALS['webhooks'] = array(
    'der_reporter' => EnvLoader::str('WEBHOOK_DER_REPORTER', ''),
    'global'       => EnvLoader::str('WEBHOOK_GLOBAL', ''),
    'CDE'          => EnvLoader::str('WEBHOOK_CDE', ''),
    'DDE'          => EnvLoader::str('WEBHOOK_DDE', ''),
    'EDE'          => EnvLoader::str('WEBHOOK_EDE', ''),
    'RDE'          => EnvLoader::str('WEBHOOK_RDE', ''),
    'SDE'          => EnvLoader::str('WEBHOOK_SDE', ''),
    'xDE'          => EnvLoader::str('WEBHOOK_XDE', ''),
);

//RPC Authcode
$GLOBALS['env_rpc_authcode'] = EnvLoader::str('ENV_RPC_AUTHCODE', '');

// API Key for external services (ohne Key ist die API gesperrt)
$GLOBALS['env_api_key'] = EnvLoader::str('ENV_API_KEY', '');

// NPC-Testseite npcx/ freischalten; nur lokal, sie zeigt den API-Key im Browser
$GLOBALS['env_npcx_enabled'] = EnvLoader::bool('ENV_NPCX_ENABLED', false);
