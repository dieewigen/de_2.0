<?php
//Abmeldung von den Lageberichten von Fluxurion (Link aus den Mails), funktioniert ohne Login.
//GET zeigt nur eine Bestätigung, abgemeldet wird per POST, damit Link-Scanner in Mailprogrammen nichts auslösen.
//Ein POST auf die Link-Adresse ist zugleich die Ein-Klick-Abmeldung nach RFC 8058 (List-Unsubscribe-Post).
include 'inc/sv.inc.php';
include 'inccon.php';
include 'inc/lang/'.$sv_server_lang.'_exile.lang.php';
require_once 'vendor/autoload.php';

$uid = intval($_REQUEST['u'] ?? 0);
$token = is_string($_REQUEST['t'] ?? null) ? $_REQUEST['t'] : '';

$exileService = new \DieEwigen\DE2\Model\Exile\ExileService($GLOBALS['dbi']);
$valid = $uid > 0 && $token !== '' && $exileService->isValidToken($uid, $token);

$done = false;
if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $done = $exileService->optOut($uid, $token);
}

if ($done) {
    $content = '<p>'.$exile_lang['optout_fertig'].'</p>';
} elseif ($valid) {
    $content = '<p>'.$exile_lang['optout_frage'].'</p>
        <form method="post" action="exile_optout.php">
            <input type="hidden" name="u" value="'.$uid.'">
            <input type="hidden" name="t" value="'.htmlspecialchars($token, ENT_QUOTES, 'UTF-8').'">
            <button type="submit">'.$exile_lang['optout_button'].'</button>
        </form>';
} else {
    $content = '<p>'.$exile_lang['optout_ungueltig'].'</p>';
}

echo '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>'.$exile_lang['optout_titel'].' - Die Ewigen</title>
    <style>
        body { margin: 0; padding: 20px 16px; font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5;
               color: #FFFFFF; background: #000000 url(https://login.die-ewigen.com/img/bg.jpg) center top; }
        .box { max-width: 560px; margin: 40px auto; padding: 20px; background: url(https://login.die-ewigen.com/img/bgtr1.png); }
        h1 { font-size: 22px; margin: 0 0 16px 0; }
        a { color: #f8ae56; }
        button { font: inherit; font-weight: bold; padding: 10px 22px; color: #f8ae56; background: transparent; border: 1px solid #f8ae56; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h1>'.$exile_lang['optout_titel'].'</h1>
        '.$content.'
    </div>
</body>
</html>';
