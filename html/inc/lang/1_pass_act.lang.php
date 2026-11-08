<?php
$passact_lang['actmailbetreff']='Bestätigungsmail für die Anmeldung bei Die Ewigen';
$passact_lang['actmailbody']='Hallo {SPIELER}!

Klicken Sie bitte auf diesen Link, um Ihren Account zu aktivieren:
{ACTIV-LINK}

Ihre Zugangsdaten sind:
- Loginname: {LOGIN}
- Passwort: {PASS}

Sollte die Aktivierung nicht funktionieren, so kann das an einem Zeilenumbruch im Aktivierungslink liegen.
Um dieses zu umgehen, kopieren Sie bitte den Link aus der Mail vollständig heraus und fügen ihn in der
Adresszeile des Browsers ein.

Sollten dann noch immer Probleme bestehen, schreibe bitte eine Mail an mailto:support@die-ewigen.com

Nach der Aktivierung können bis zu 15 Minuten vergehen, bis der Account ins Spielsystem integriert wurde.

Sollte der Account nicht innerhalb von 48 Stunden freigeschalten werden, so wird er automatisch wieder gelöscht.
';


$passact_lang['passmailbetreff']='Die Ewigen - Passwortanforderung';
$passact_lang['passmailbody']='Hallo!

Sie haben Ihre Zugangsdaten für Die Ewigen angefordert
und es wurde ein alternatives Passwort generiert.

Sollten sie sich mit dem neuen Passwort einloggen,
so verliert das alte Passwort seine Gültigkeit.

Ihre Zugangsdaten sind:
Login-Name: {LOGIN}
Passwort: {PASS} // Das ist Ihr alternativ-Passwort

Diese E-Mail wurde an {EMAIL} gesendet.';

$passact_lang['email']='E-Mail';
$passact_lang['emailanfordern']='E-Mail anfordern';
$passact_lang['keinaccount']='Es konnte kein passender Account gefunden werden.';
$passact_lang['login']='Zum Login';
$passact_lang['loginname']='Loginname';
$passact_lang['msg1']='Sollte man sein Passwort vergessen haben, so kann man hier ein neues Passwort aktivieren, das zusätzlich zu dem alten Passwort funktioniert. Bei dessen erster Benutzung wird dann das alte durch das neue Passwort ersetzt.';
$passact_lang['msg2']='Sollte der Account noch nicht aktiviert sein, so wird nicht das Passwort verschickt sondern die Aktivierungsmail.';
$passact_lang['msg3']='Eins der beiden Felder muss ausgefüllt werden.';
$passact_lang['ueberschrift']='Passwort oder Aktivierungsmail senden';
$passact_lang['versendet']='Die E-Mail wurde verschickt.';
$passact_lang['title']='Die Ewigen - Passwort oder Aktivierungsmail senden';
?>
